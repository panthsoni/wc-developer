<?php
namespace panthsoni\lib;


class NucleusCryption
{
    /**
     * error code 说明.
     * <ul>
     *    <li>-40001: 签名验证错误</li>
     *    <li>-40002: xml解析失败</li>
     *    <li>-40003: sha加密生成签名失败</li>
     *    <li>-40004: encodingAesKey 非法</li>
     *    <li>-40005: appid 校验错误</li>
     *    <li>-40006: aes 加密失败</li>
     *    <li>-40007: aes 解密失败</li>
     *    <li>-40008: 解密后得到的buffer非法</li>
     *    <li>-40009: base64加密失败</li>
     *    <li>-40010: base64解密失败</li>
     *    <li>-40011: 生成xml失败</li>
     * </ul>
     */
    protected static $OK = 0;
    protected static $ValidateSignatureError = -40001;
    protected static $ParseXmlError = -40002;
    protected static $ComputeSignatureError = -40003;
    protected static $IllegalAesKey = -40004;
    protected static $ValidateAppIdError = -40005;
    protected static $EncryptAESError = -40006;
    protected static $DecryptAESError = -40007;
    protected static $IllegalBuffer = -40008;
    protected static $EncodeBase64Error = -40009;
    protected static $DecodeBase64Error = -40010;
    protected static $GenReturnXmlError = -40011;

    private $token;
    private $encodingAesKey;
    private $appId;
    protected static $block_size = 32;

    public function __construct($token,$encodingAesKey,$appId){
        $this->token = $token;
        $this->encodingAesKey = $encodingAesKey;
        $this->appId = $appId;
    }

    /**
     * 将公众平台回复用户的消息加密打包.
     * <ol>
     *    <li>对要发送的消息进行AES-CBC加密</li>
     *    <li>生成安全签名</li>
     *    <li>将消息密文和安全签名打包成xml格式</li>
     * </ol>
     *
     * @param $replyMsg string 公众平台待回复用户的消息，xml格式的字符串
     * @param $timeStamp string 时间戳，可以自己生成，也可以用URL参数的timestamp
     * @param $nonce string 随机串，可以自己生成，也可以用URL参数的nonce
     * @param &$encryptMsg string 加密后的可以直接回复用户的密文，包括msg_signature, timestamp, nonce, encrypt的xml格式的字符串,
     *                      当return返回0时有效
     *
     * @return int 成功0，失败返回对应的错误码
     */
    public function encryptMsg($replyMsg,$timeStamp,$nonce,&$encryptMsg){
        /*加密*/
        $array = self::encrypt($replyMsg,$this->appId,base64_decode($this->encodingAesKey . "="));
        $ret = $array[0];
        if ($ret != 0) return $ret;
        if ($timeStamp == null) $timeStamp = time();
        $encrypt = $array[1];

        /*生成安全签名*/
        $array = self::getSHA1($this->token,$timeStamp,$nonce,$encrypt);
        $ret = $array[0];
        if ($ret != 0) return $ret;
        $signature = $array[1];

        /*生成发送的xml*/
        $encryptMsg = self::generate($encrypt,$signature,$timeStamp,$nonce);

        return self::$OK;
    }

    /**
     * 检验消息的真实性，并且获取解密后的明文.
     * <ol>
     *    <li>利用收到的密文生成安全签名，进行签名验证</li>
     *    <li>若验证通过，则提取xml中的加密消息</li>
     *    <li>对消息进行解密</li>
     * </ol>
     *
     * @param $msgSignature string 签名串，对应URL参数的msg_signature
     * @param $timestamp string 时间戳 对应URL参数的timestamp
     * @param $nonce string 随机串，对应URL参数的nonce
     * @param $postData string 密文，对应POST请求的数据
     * @param &$msg string 解密后的原文，当return返回0时有效
     *
     * @return int 成功0，失败返回对应的错误码
     */
    public function decryptMsg($msgSignature,$timestamp = null,$nonce,$postData,&$msg){
        if (strlen($this->encodingAesKey) != 43) return self::$IllegalAesKey;

        /*提取密文*/
        $array = self::extract($postData);
        $ret = $array[0];
        if ($ret != 0) return $ret;
        if ($timestamp == null) $timestamp = time();
        $encrypt = $array[1];

        /*验证安全签名*/
        $array = self::getSHA1($this->token,$timestamp,$nonce,$encrypt);
        $ret = $array[0];
        if ($ret != 0) return $ret;

        $signature = $array[1];
        if ($signature != $msgSignature) return self::$ValidateSignatureError;

        $result = self::decrypt($encrypt,$this->appId,base64_decode($this->encodingAesKey . "="));
        if ($result[0] != 0) return $result[0];
        $msg = $result[1];

        return self::$OK;
    }

    /**
     * 对明文进行加密
     * @param $text
     * @param $appid
     * @param $key
     * @return array
     */
    public function encrypt($text,$appid,$key){
        try {
            //获得16位随机字符串，填充到明文之前
            $random = self::getRandomStr();
            $text = $random . pack("N", strlen($text)) . $text . $appid;
            // 网络字节序
            $iv = substr($key, 0, 16);

            //使用自定义的填充方式对明文进行补位填充
            $text = self::encode($text);

            $encrypted = openssl_encrypt($text,'AES-256-CBC',$key,OPENSSL_RAW_DATA,$iv);
            $encrypted = substr($encrypted,0,strlen($encrypted)-16);

            //使用BASE64对加密后的字符串进行编码
            return array(self::$OK,base64_encode($encrypted));
        } catch (\Exception $e) {
            return array(self::$EncryptAESError, null);
        }
    }

    /**
     * 对密文进行解密
     * @param $encrypted
     * @param $appid
     * @param $key
     * @return array|string
     */
    public function decrypt($encrypted,$appid,$key,$isUrlVerify=false){
        try {
            //使用BASE64对需要解密的字符串进行解码
            if (!$isUrlVerify){
                $ciphertext_dec = base64_decode($encrypted);
            }else{
                $ciphertext_dec = $encrypted;
            }

            $iv = substr($key,0,16);
            if (!$isUrlVerify){
                $decrypted = openssl_decrypt($ciphertext_dec,'AES-256-CBC',$key,OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING,$iv);
            }else{
                $decrypted = openssl_decrypt($ciphertext_dec,'AES-256-CBC',$key,OPENSSL_ZERO_PADDING,$iv);
            }

        } catch (\Exception $e) {
            return array(self::$DecryptAESError,null);
        }

        try {
            //去除补位字符
            $result = self::decode($decrypted);

            //去除16位随机字符串,网络字节序和AppId
            if (strlen($result) < 16) return [];
            $content = substr($result,16,strlen($result));
            $len_list = unpack("N",substr($content,0,4));
            $xml_len = $len_list[1];
            $xml_content = substr($content,4,$xml_len);
            $from_appid = substr($content,$xml_len + 4);
        }catch(\Exception $e) {
            return array(self::$IllegalBuffer,null);
        }

        if ($from_appid != $appid) return array(self::$ValidateAppIdError,null);

        return array(0,$xml_content);
    }

    /**
     * 随机生成16位字符串
     * @return string
     */
    protected static function getRandomStr(){
        $str = "";
        $str_pol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($str_pol) - 1;
        for ($i = 0; $i < 16; $i++) {
            $str .= $str_pol[mt_rand(0, $max)];
        }

        return $str;
    }

    /**
     * 对需要加密的明文进行填充补位
     * @param $text
     * @return string
     */
    protected static function encode($text){
        $text_length = strlen($text);
        //计算需要填充的位数
        $amount_to_pad = self::$block_size - ($text_length % self::$block_size);
        if ($amount_to_pad == 0) {
            $amount_to_pad = self::$block_size;
        }
        //获得补位所用的字符
        $pad_chr = chr($amount_to_pad);
        $tmp = "";
        for ($index = 0;$index < $amount_to_pad;$index++) {
            $tmp .= $pad_chr;
        }

        return $text . $tmp;
    }

    /**
     * 对解密后的明文进行补位删除
     * @param $text
     * @return bool|string
     */
    protected static function decode($text){
        $pad = ord(substr($text,-1));
        if ($pad < 1 || $pad > 32) $pad = 0;
        return substr($text,0,(strlen($text) - $pad));
    }

    /**
     * 用SHA1算法生成安全签名
     * @param $token
     * @param $timestamp
     * @param $nonce
     * @param $encrypt_msg
     * @return array
     */
    protected static function getSHA1($token,$timestamp,$nonce,$encrypt_msg){
        //排序
        try{
            $array = array($encrypt_msg,$token,$timestamp,$nonce);
            sort($array,SORT_STRING);
            $str = implode($array);
            return array(self::$OK,sha1($str));
        }catch(\Exception $e){
            return array(self::$ComputeSignatureError,null);
        }
    }

    /**
     * 提取出xml数据包中的加密消息
     * @param $xmltext
     * @return array
     */
    public function extract($xmltext){
        try {
            $xml = new \DOMDocument();
            $xml->loadXML($xmltext);
            $array_e = $xml->getElementsByTagName('Encrypt');
            $array_a = $xml->getElementsByTagName('ToUserName');
            $encrypt = $array_e->item(0)->nodeValue;
            $tousername = $array_a->item(0)->nodeValue;
            return array(0,$encrypt,$tousername);
        } catch (\Exception $e) {
            return array(self::$ParseXmlError,null,null);
        }
    }

    /**
     * 生成xml消息
     * @param $encrypt
     * @param $signature
     * @param $timestamp
     * @param $nonce
     * @return string
     */
    public function generate($encrypt,$signature,$timestamp,$nonce){
        $format = "<xml>
                        <Encrypt><![CDATA[%s]]></Encrypt>
                        <MsgSignature><![CDATA[%s]]></MsgSignature>
                        <TimeStamp>%s</TimeStamp>
                        <Nonce><![CDATA[%s]]></Nonce>
                   </xml>";
        return sprintf($format,$encrypt,$signature,$timestamp,$nonce);
    }

    /**
     * 验证URL
     * @param $echoStr
     * @param $sMsgSignature
     * @param $sTimeStamp
     * @param $sNonce
     * @param $sEchoStr
     * @param $sReplyEchoStr
     * @return int|mixed|string
     */
    public function verifyURL($sMsgSignature, $sTimeStamp, $sNonce, $sEchoStr, &$sReplyEchoStr){
        if (strlen($this->encodingAesKey) != 43) {
            return self::$IllegalAesKey;
        }

        //verify msg_signature
        $array = self::getSHA1($this->token, $sTimeStamp, $sNonce, $sEchoStr);
        $ret = $array[0];
        if ($ret != 0) {
            return $ret;
        }

        $signature = $array[1];
        if ($signature != $sMsgSignature) {
            return self::$ValidateSignatureError;
        }

        $result = $this->decrypt($sEchoStr,$this->appId,base64_decode($this->encodingAesKey.'='),true);
        if ($result[0] != 0) {
            return $result[0];
        }

        $sReplyEchoStr = $result[1];

        return self::$OK;
    }
}