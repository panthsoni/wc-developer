<?php


namespace panthsoni\lib;


class SingleValidate extends NucleusValidate
{
    protected $rule = [
        'appid|公众号appid' => 'length:0,50',
        'secret|公众号密钥' => 'length:0,50',
        'access_token|公众号普通access_token' => 'length:0,255',
        'button|菜单按钮参数' => 'length:0,100',
        'industry_id1|公众号模板消息所属行业编号' => 'number',
        'industry_id2|公众号模板消息所属行业编号' => 'number',
        'template_id_short|模板库编号' => 'length:0,50',
        'template_id|公众帐号下模板消息ID' => 'length:0,50',
        'template_message_content|模板消息内容' => 'length:0,255',
        'account_message_content|模板消息内容' => 'length:0,255',
        'menuid|菜单id'=>'length:0,20',
        'kf_account|客服帐号'=>'length:0,50',
        'nickname|昵称'=>'length:0,20',
        'password|密码'=>'length:0,30',
        'touser|用户openid'=>'length:0,32',
        'command|输入状态'=>'length:0,15',
        'redirect_uri|回调地址'=>'length:0,255',
        'scope|应用授权作用域'=>'in:snsapi_base,snsapi_userinfo',
        'state|state参数'=>'length:0,128',
        'code|授权code'=>'length:0,50',
        'refresh_token|刷新access_token'=>'length:0,100',
        'openid|用户openid'=>'length:0,50',
        'lang|语言'=>'length:0,10',
        'tag|标签'=>'length:0,20',
        'tagid|标签id'=>'number',
        'next_openid|下一个openid'=>'length:0,50',
        'is_safe|是否安全模式'=>'length:0,5',
        'signature|签名'=>'length:0,100',
        'timestamp|时间戳'=>'length:0,100',
        'nonce|随机数'=>'length:0,100',
        'echostr|随机字符串'=>'length:0,100',
        'token|token码'=>'length:0,100',
        'encoding_aes_key|随机秘钥'=>'length:0,100',
        'msg_type|消息类型'=>'in:text,voice,video,music,news,image,transfer_customer_service',
        'to_user_name|接收用户openid'=>'length:0,50',
        'from_user_name|发送者'=>'length:0,50',
        'content|内容'=>'length:0,255',
        'media_id|媒体id'=>'length:0,50',
        'title|标题'=>'length:0,50',
        'description|描述'=>'length:0,255',
        'music_url|音乐链接'=>'length:0,255',
        'hq_music_url|hq音乐链接'=>'length:0,255',
        'article_counts|文章个数'=>'between:1,8',
        'pic_url|图片链接'=>'length:0,255',
        'url|跳转链接'=>'length:0,255',
        'article_content|文章内容'=>'array',
        'msg_signature|消息签名'=>'length:0,100',
        'expire_seconds|过期时间'=>'number',
        'action_name|二维码类型'=>'in:QR_SCENE,QR_STR_SCENE',
        'action_info|二维码详细信息'=>'array',
        'action|长链接转短链接'=>'in:long2short',
        'long_url|需要转换的长链接'=>'length:0,255',
    ];

    public $scene = [
        /*获取基础access_token*/
        'get_base_access_token'=>['appid'=>'require|length:0,50','secret'=>'require|length:0,50'],

        /*菜单接口*/
        'create_menu'=>['access_token'=>'require|length:0,255','button'=>'require|length:0,100'],
        'get_menu'=>['access_token'=>'require|length:0,255'],
        'del_menu'=>['access_token'=>'require|length:0,255'],
        'create_self_menu'=>['access_token'=>'require|length:0,255','button'=>'require|length:0,100'],
        'del_self_menu'=>['access_token'=>'require|length:0,255','menuid'=>'require|length:0,20'],
        'get_self_menu'=>['access_token'=>'require|length:0,255'],

        /*客服接口*/
        'create_account'=>['access_token'=>'require|length:0,255','kf_account'=>'require|length:0,50','nickname'=>'require|length:0,50','password'=>'require|length:0,50'],
        'update_account'=>['access_token'=>'require|length:0,255','kf_account'=>'require|length:0,50','nickname'=>'require|length:0,50','password'=>'require|length:0,50'],
        'del_account'=>['access_token'=>'require|length:0,255','kf_account'=>'require|length:0,50','nickname'=>'require|length:0,50','password'=>'require|length:0,50'],
        'upload_account'=>['access_token'=>'require|length:0,255','kf_account'=>'require|length:0,50'],
        'get_account'=>['access_token'=>'require|length:0,255'],
        'send_account_message'=>['access_token'=>'require|length:0,255','account_message_content'=>'require|length:0,255'],
        'typing_account'=>['access_token'=>'require|length:0,255','touser'=>'require|length:0,32','command'=>'require|length:0,15'],

        /*模板消息*/
        'set_industry'=>['access_token'=>'require|length:0,255','industry_id1'=>'require|number','industry_id2'=>'require|number'],
        'get_industry'=>['access_token'=>'require|length:0,255'],
        'add_template'=>['access_token'=>'require|length:0,255','template_id_short'=>'require|length:0,50'],
        'get_template'=>['access_token'=>'require|length:0,255'],
        'del_template'=>['access_token'=>'require|length:0,255','template_id'=>'require|length:0,50'],
        'send_template_message'=>['access_token'=>'require|length:0,255','template_message_content'=>'require|length:0,255'],

        /*网页授权*/
        'user_authorize'=>['appid'=>'require|length:0,20','redirect_uri'=>'require|length:0,255','scope'=>'require|in:snsapi_base,snsapi_userinfo','state'=>'length:0,128'],
        'get_authorize_access_token'=>['appid'=>'require|length:0,20','secret'=>'require|length:0,50','code'=>'require|length:0,50'],
        'refresh_authorize_access_token'=>['appid'=>'require|length:0,20','refresh_token'=>'require|length:0,100'],
        'get_user_info'=>['access_token'=>'require|length:0,255','openid'=>'require|length:0,50','lang'=>'require|length:0,10',],
        'check_auth'=>['access_token'=>'require|length:0,255','openid'=>'require|length:0,50'],

        /*用户管理*/
        'create_tag'=>['access_token'=>'require|length:0,255','tag'=>'require|length:0,20'],
        'update_tag'=>['access_token'=>'require|length:0,255','tag'=>'require|length:0,20'],
        'del_tag'=>['access_token'=>'require|length:0,255','tag'=>'require|length:0,20'],
        'get_tag_openid'=>['access_token'=>'require|length:0,255','tagid'=>'require|number','next_openid'=>'require|length:0,50'],

        /*帐号管理*/
        'qrcode_create'=>['access_token'=>'require|length:0,255','expire_seconds'=>'require|number','action_name'=>'require|in:QR_SCENE,QR_STR_SCENE','action_info'=>'require|array'],
        'short_url'=>['access_token'=>'require|length:0,255','action'=>'require|in:long2short','long_url'=>'require|length:0,255'],

        /*监听*/
        'listen'=>[
            'signature'=>'require|length:0,100',
            'timestamp'=>'require|length:0,100',
            'nonce'=>'require|length:0,100',
            'token','echostr','appid','encoding_aes_key','msg_signature'
        ],

        /*消息回复*/
        'replyMessage'=>[
            'msg_type'=>'require|in:text,voice,video,music,news,image,transfer_customer_service',
            'to_user_name'=>'require|length:0,50',
            'from_user_name'=>'require|length:0,50',
            'token','appid','encoding_aes_key'
        ],
        'text'=>['content'=>'require|length:0,255'],
        'voice'=>['media_id'=>'require|length:0,50'],
        'video'=>['media_id'=>'require|length:0,50','title'=>'require|length:0,50','description'=>'require|length:0,255'],
        'music'=>['media_id'=>'require|length:0,50','title','description','music_url','hq_music_url'],
        'image'=>['media_id'=>'require|length:0,50'],
        'news'=>['article_counts'=>'require|between:1,8','article_content'=>'require|array'],
        'news_item'=>['title'=>'require|length:0,50','description'=>'require|length:0,50','pic_url'=>'require|length:0,255','url'=>'require|length:0,255',],
        'transfer_customer_service'=>['kf_account']
    ];
}