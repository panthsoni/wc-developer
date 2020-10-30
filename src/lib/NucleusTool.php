<?php


namespace panthsoni\lib;


class NucleusTool
{
    public static $template = [
        'text' => "<xml> 
                    <ToUserName><![CDATA[%s]]></ToUserName> 
                    <FromUserName><![CDATA[%s]]></FromUserName> 
                    <CreateTime>%s</CreateTime> 
                    <MsgType><![CDATA[text]]></MsgType> 
                    <Content><![CDATA[%s]]></Content> 
               </xml>",
        'image' => "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[image]]></MsgType>
                    <Image>
                        <MediaId><![CDATA[%s]]></MediaId>
                    </Image>
                </xml>",
        'voice' => "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[voice]]></MsgType>
                    <Voice>
                        <MediaId><![CDATA[%s]]></MediaId>
                    </Voice>
                </xml>",
        'video' => "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[video]]></MsgType>
                    <Video>
                        <MediaId><![CDATA[%s]]></MediaId>
                        <Title><![CDATA[%s]]></Title>
                        <Description><![CDATA[%s]]></Description>
                    </Video> 
                </xml>",
        'music' => "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[music]]></MsgType>
                    <Music>
                        <Title><![CDATA[%s]]></Title>
                        <Description><![CDATA[%s]]></Description>
                        <MusicUrl><![CDATA[%s]]></MusicUrl>
                        <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
                        <ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
                    </Music>
                </xml>",
        'news' => [
            'main' => "<xml>
                       <ToUserName><![CDATA[%s]]></ToUserName>
                       <FromUserName><![CDATA[%s]]></FromUserName>
                       <CreateTime>%s</CreateTime>
                       <MsgType><![CDATA[news]]></MsgType>
                       <ArticleCount>%s</ArticleCount>
                       <Articles>%s</Articles>
                   </xml>",
            'item' => "<item>
                        <Title><![CDATA[%s]]></Title> 
                        <Description><![CDATA[%s]]></Description>
                        <PicUrl><![CDATA[%s]]></PicUrl>
                        <Url><![CDATA[%s]]></Url>
                    </item>"
        ],
        'transfer_customer_service' => [
            'main' => "<xml> 
                          <ToUserName><![CDATA[%s]]></ToUserName>  
                          <FromUserName><![CDATA[%s]]></FromUserName>  
                          <CreateTime>%s</CreateTime>  
                          <MsgType><![CDATA[transfer_customer_service]]></MsgType>",
            'item' => "<TransInfo> 
                        <KfAccount><![CDATA[%s]]></KfAccount> 
                      </TransInfo>"
        ]
    ];

    /**
     * Get参数替换
     * @var array
     */
    protected static $paramsReplaceList = [
        'appid' => '[appid]',
        'secret' => '[secret]',
        'access_token' => '[access_token]',
        'redirect_uri' => '[redirect_uri]',
        'scope' => '[scope]',
        'state' => '[state]',
        'code' => '[code]',
        'refresh_token' => '[refresh_token]',
        'openid' => '[openid]',
        'lang' => '[lang]'
    ];

    /**
     * 实例化
     * Tools constructor.
     */
    public function __construct(){}

    /**
     * 组建请求结果
     * @param $requestUrl
     * @param $requestParams
     * @param $requestWay
     * @param bool $isBackUrl
     * @return bool|mixed
     */
    public static function buildRequestResult($requestUrl,$requestParams,$requestWay,$isBackUrl=false){
        /*替换参数处理*/
        $replaceParams = [];
        $replaceValues = [];
        foreach (self::$paramsReplaceList as $key=>$val){
            $replaceParams[] = $val;
            $replaceValues[] = isset($requestParams[$key])?$requestParams[$key]:'';
        }

        /*替换结果*/
        $requestUrl = str_replace($replaceParams,$replaceValues,$requestUrl);

        /*根据不同的请求方式，将参数进行过滤*/
        $getParams = explode('&',isset(explode('?',$requestUrl)[1])?explode('?',$requestUrl)[1]:'');
        foreach ($getParams as $val){
            $val = explode('=',$val)[0];
            if (isset($requestParams[$val])) unset($requestParams[$val]);
        }

        /*返回链接*/
        if ($isBackUrl) return $requestUrl;

        /*curl请求*/
        return self::httpCurl($requestUrl,$requestWay,$requestParams);
    }

    /**
     * curl请求
     * @param string $url
     * @param string $requestWay
     * @param array $params
     * @return bool|mixed
     */
    protected static function httpCurl($url='',$requestWay='GET',$params=[]){
        if (!$url) return false;

        /*curl请求*/
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$url);//抓取指定网页
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);//设置header
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        if ($requestWay == 'POST'){
            curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        }
        $data = curl_exec($ch);//运行curl
        curl_close($ch);

        return json_decode($data,true);
    }

    /**
     * 公众号监听事件
     * @param $requestParamsList
     * @param $isSafe
     * @return int|mixed
     * @throws \Exception
     */
    public static function listen($requestParamsList,$isSafe){
        /*判断token*/
        if (isset($requestParamsList['echostr']) || $isSafe){
            if (!isset($requestParamsList['token'])) throw new \Exception('token缺失',-10026);
        }

        /*是否首次接入*/
        if (isset($requestParamsList['echostr'])){
            $signature = self::makeSignature($requestParamsList);
            if ($signature != $requestParamsList['signature']){
                throw new \Exception('signature校验错误',-10023);
            }

            exit($requestParamsList['echostr']);
        }

        /*接收微信数据包*/
        $requestParamsList['post_data'] = file_get_contents('php://input');

        /*响应消息*/
        return self::response($requestParamsList,$isSafe);
    }

    /**
     * 响应用户信息
     * @param $requestParamsList
     * @param $isSafe
     * @return int|mixed
     * @throws \Exception
     */
    protected static function response($requestParamsList,$isSafe){
        /*是否安全模式*/
        if ($isSafe){
            /*判断是否设置公众号appid*/
            if (!isset($requestParamsList['appid'])) throw new \Exception('缺少必备的appid参数',-10004);

            /*验证是否设置aes_key*/
            if (!isset($requestParamsList['encoding_aes_key'])) throw new \Exception('encoding_aes_key缺失',-10027);

            /*检测消息签名*/
            if (!isset($requestParamsList['msg_signature'])) throw new \Exception('msg_signature缺失',-10028);

            /*消息解密*/
            return self::decryptMsg($requestParamsList);
        }

        return self::xmlToArray($requestParamsList['post_data']);
    }

    /**
     * 消息解密
     * @param array $data
     * @return int|mixed
     */
    protected static function decryptMsg($data=[]){
        /*提取数据包加密信息*/
        $domDocument = new \DOMDocument();
        $domDocument->loadXML($data['post_data']);
        $encrypt = $domDocument->getElementsByTagName('Encrypt')->item(0)->nodeValue;

        /*构建消息格式并解密*/
        $msg = "";
        $fromXml = sprintf("<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>", $encrypt);
        $cryptGraph = new NucleusCryption($data['token'],$data['encoding_aes_key'],isset($data['appid'])?$data['appid']:$data['component_appid']);
        $errorCode = $cryptGraph->decryptMsg($data['msg_signature'],$data['timestamp'],$data['nonce'],$fromXml,$msg);
        if ($errorCode !=0) return $errorCode;

        /*将xml数据转为数组*/
        return self::xmlToArray($msg);
    }

    /**
     * xml转数组
     * @param $xml
     * @return mixed
     */
    protected static function xmlToArray($xml){
        $ob= simplexml_load_string($xml,'SimpleXMLElement', LIBXML_NOCDATA);
        $json = json_encode($ob);
        $configData = json_decode($json, true);

        return $configData;
    }

    /**
     * 回复用户消息
     * @param $requestParamsList
     * @param $isSafe
     * @return int|string
     * @throws \Exception
     */
    public static function replyMessage($requestParamsList,$isSafe){
        NucleusTool::validate($requestParamsList,new SingleValidate(),$requestParamsList['msg_type']);

        /*图文消息处理参数*/
        if ($requestParamsList['msg_type'] == 'news'){
            /*处理文章个数和内容个数是否对应*/
            if ($requestParamsList['article_counts'] != count($requestParamsList['article_content'])){
                throw new \Exception('文章个数与文章内容设置的个数不一致',-10040);
            }

            /*处理参数*/
            foreach ($requestParamsList['article_content'] as $val){
                NucleusTool::validate($val,new SingleValidate(),'news_item');
            }
        }

        /*组建消息xml*/
        $content = self::buildXmlMessage($requestParamsList);

        /*是否安全模式，需要加密处理*/
        if ($isSafe){
            /*判断是否设置token*/
            if (!isset($requestParamsList['token'])) throw new \Exception('token缺失',-10026);

            /*判断是否设置公众号appid*/
            if (!isset($requestParamsList['appid'])) throw new \Exception('缺少必备的appid参数',-10004);

            /*验证是否设置aes_key*/
            if (!isset($requestParamsList['encoding_aes_key'])) throw new \Exception('encoding_aes_key缺失',-10027);

            /*加密处理*/
            $encryptMsg = "";
            $cryptGraph = new NucleusCryption($requestParamsList['token'],$requestParamsList['encoding_aes_key'],$requestParamsList['appid']);
            $errorCode = $cryptGraph->encryptMsg($content,time(),'panthsoni',$encryptMsg);
            if ($errorCode !=0) return $errorCode;

            return $encryptMsg;
        }

        return $content;
    }

    /**
     * 组建xml消息
     * @param $requestParamsList
     * @return string
     */
    protected static function buildXmlMessage($requestParamsList){
        switch ($requestParamsList['msg_type']){
            case 'text':
                return sprintf(self::$template[$requestParamsList['msg_type']],$requestParamsList['to_user_name'],$requestParamsList['from_user_name'],time(),$requestParamsList['content']);
                break;
            case 'voice':
                return sprintf(self::$template[$requestParamsList['msg_type']],$requestParamsList['to_user_name'],$requestParamsList['from_user_name'],time(),$requestParamsList['media_id']);
                break;
            case 'video':
                return sprintf(self::$template[$requestParamsList['msg_type']],$requestParamsList['to_user_name'],$requestParamsList['from_user_name'],time(),$requestParamsList['media_id'],isset($requestParamsList['title'])?$requestParamsList['title']:'',isset($requestParamsList['description'])?$requestParamsList['description']:'');
                break;
            case 'music':
                return sprintf(self::$template[$requestParamsList['msg_type']],$requestParamsList['to_user_name'],$requestParamsList['from_user_name'],time(),$requestParamsList['media_id'],isset($requestParamsList['title'])?$requestParamsList['title']:'',isset($requestParamsList['description'])?$requestParamsList['description']:'',isset($requestParamsList['music_url'])?$requestParamsList['music_url']:'',isset($requestParamsList['hq_music_url'])?$requestParamsList['hq_music_url']:'');
                break;
            case 'image':
                return sprintf(self::$template[$requestParamsList['msg_type']],$requestParamsList['to_user_name'],$requestParamsList['from_user_name'],time(),$requestParamsList['media_id']);
                break;
            case 'news':
                return sprintf(self::$template[$requestParamsList['msg_type']]['main'],$requestParamsList['to_user_name'],$requestParamsList['from_user_name'],time(),$requestParamsList['article_counts'],self::buildNewsItem($requestParamsList['article_content']));
                break;
            case 'transfer_customer_service':
                return self::buildServiceTemplate($requestParamsList);
                break;
            default:
                return 'success';
                break;
        }
    }

    /**
     * 组建图文的item数据
     * @param $articleContent
     * @return string
     */
    protected static function buildNewsItem($articleContent){
        $item = '';
        foreach ($articleContent as $val){
            $item .= sprintf(self::$template['news']['item'],$val['title'],$val['description'],$val['pic_url'],$val['url']);
        }

        return $item;
    }

    /**
     * 组建客服模板消息
     * @param $requestParamsList
     * @return string
     */
    protected static function buildServiceTemplate($requestParamsList){
        $template = sprintf(self::$template[$requestParamsList['msg_type']]['main'],$requestParamsList['to_user_name'],$requestParamsList['from_user_name'],time());

        /*指定客服*/
        if (isset($requestParamsList['kf_account'])){
            $item = sprintf(self::$template[$requestParamsList['msg_type']]['item'],$requestParamsList['kf_account']);
            $template .= $item;
        }

        $template .= "</xml>";

        return $template;
    }

    /**
     * 生成签名
     * @param $requestParamsList
     * @return string
     */
    protected static function makeSignature($requestParamsList){
        /*生成签名*/
        $tmpArr = [$requestParamsList['token'],$requestParamsList['timestamp'],$requestParamsList['nonce']];
        sort($tmpArr, SORT_STRING);
        $signature = sha1(implode($tmpArr));

        return $signature;
    }

    /**
     * 数据验证
     * @param $data
     * @param $validate
     * @param $scene
     * @return array
     * @throws \Exception
     */
    public static function validate($data,$validate,$scene){
        /*数据接收*/
        if (!is_array($data)) throw new \Exception('参数必须为数组',-10001);

        /*验证场景验证*/
        if (!$scene) throw new \Exception('场景不能为空',-10002);
        $validate->scene($scene);

        /*数据验证*/
        if (!$validate->check($data)){
            throw new \Exception($validate->getError(),-10003);
        }

        $scene = $validate->scene[$scene];
        $_scene = [];
        foreach ($scene as $key => $val){
            if(is_numeric($key)){
                $_scene[] = $val;
            }else{
                $_scene[] = $key;
            }
        }

        $_data = [];
        foreach ($data as $key=>$val){
            if ($val === '' || $val === null) continue;

            if(is_numeric($key)){
                if(in_array($key,$_scene)) $_data[$key] = $val;
            }else{
                if(in_array($key,$_scene)) $_data[$key] = $val;
            }
        }

        return $_data;
    }
}