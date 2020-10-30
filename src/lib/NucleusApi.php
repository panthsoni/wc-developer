<?php
namespace panthsoni\lib;


class NucleusApi
{
    /**
     * 请求方法列表（开发者模式）
     * @var array
     */
    protected static $developerMethodList = [
        'get_base_access_token' => [
            'request_way' => 'GET',
            'request_uri' => '/cgi-bin/token?grant_type=client_credential&appid=[appid]&secret=[secret]'
        ],

        /*菜单*/
        'create_menu' => [
            'request_way' => 'POST',
            'request_uri' => '/cgi-bin/menu/create?access_token=[access_token]'
        ],
        'get_menu' => [
            'request_way' => 'GET',
            'request_uri' => '/cgi-bin/get_current_selfmenu_info?access_token=[access_token]'
        ],
        'del_menu' => [
            'request_way' => 'GET',
            'request_uri' => '/cgi-bin/menu/delete?access_token=[access_token]'
        ],
        'create_self_menu' => [
            'request_way' => 'POST',
            'request_uri' => '/cgi-bin/menu/addconditional?access_token=[access_token]'
        ],
        'del_self_menu' => [
            'request_way' => 'POST',
            'request_uri' => '/cgi-bin/menu/delconditional?access_token=[access_token]'
        ],
        'get_self_menu' => [
            'request_way' => 'GET',
            'request_uri' => '/cgi-bin/menu/get?access_token=[access_token]'
        ],

        /*客服*/
        'create_account' => [
            'request_way' => 'POST',
            'request_uri' => '/customservice/kfaccount/add?access_token=[access_token]'
        ],
        'update_account' => [
            'request_way' => 'POST',
            'request_uri' => '/customservice/kfaccount/update?access_token=[access_token]'
        ],
        'del_account' => [
            'request_way' => 'POST',
            'request_uri' => '/customservice/kfaccount/del?access_token=[access_token]'
        ],
        'upload_headimg_account' => [
            'request_way' => 'POST',
            'request_uri' => '/customservice/kfaccount/uploadheadimg?access_token=[access_token]&kf_account=[kf_account]'
        ],
        'get_account' => [
            'request_way' => 'GET',
            'request_uri' => '/cgi-bin/customservice/getkflist?access_token=[access_token]'
        ],
        'send_account_message' => [
            'request_way' => 'POST',
            'request_uri' => '/cgi-bin/message/custom/send?access_token=[access_token]'
        ],
        'typing_account' => [
            'request_way' => 'POST',
            'request_uri' => '/cgi-bin/message/custom/typing?access_token=[access_token]'
        ],

        /*模板消息*/
        'set_industry' => [
            'request_way' => 'POST',
            'request_uri' => '/cgi-bin/template/api_set_industry?access_token=[access_token]'
        ],
        'get_industry' => [
            'request_way' => 'POST',
            'request_uri' => '/cgi-bin/template/get_industry?access_token=[access_token]'
        ],
        'add_template' => [
            'request_way' => 'POST',
            'request_uri' => '/cgi-bin/template/api_add_template?access_token=[access_token]'
        ],
        'get_template' => [
            'request_way' => 'GET',
            'request_uri' => '/cgi-bin/template/get_all_private_template?access_token=[access_token]'
        ],
        'del_template' => [
            'request_way' => 'POST',
            'request_uri' => '/cgi-bin/template/del_private_template?access_token=[access_token]'
        ],
        'send_template_message' => [
            'request_way' => 'POST',
            'request_uri' => '/cgi-bin/message/template/send?access_token=[access_token]'
        ],

        /*网页授权*/
        'user_authorize' => [
            'request_way' => 'GET',
            'request_uri' => 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=[appid]&redirect_uri=[redirect_uri]&response_type=code&scope=[scope]&state=[state]#wechat_redirect'
        ],
        'get_authorize_access_token' => [
            'request_way' => 'GET',
            'request_uri' => '/sns/oauth2/access_token?appid=[appid]&secret=[secret]&code=[code]&grant_type=authorization_code'
        ],
        'refresh_authorize_access_token' => [
            'request_way' => 'GET',
            'request_uri' => '/sns/oauth2/refresh_token?appid=[appid]&grant_type=refresh_token&refresh_token=[refresh_token]'
        ],
        'get_user_info' => [
            'request_way' => 'GET',
            'request_uri' => '/sns/userinfo?access_token=[access_token]&openid=[openid]&lang=[lang]'
        ],
        'check_auth' => [
            'request_way' => 'GET',
            'request_uri' => '/sns/auth?access_token=[access_token]&openid=[openid]'
        ],

        /*用户管理*/
        'create_tag' => [
            'request_way' => 'POST',
            'request_uri' => '/cgi-bin/tags/create?access_token=[access_token]'
        ],
        'get_tag' => [
            'request_way' => 'GET',
            'request_uri' => '/cgi-bin/tags/get?access_token=[access_token]'
        ],
        'update_tag' => [
            'request_way' => 'POST',
            'request_uri' => '/cgi-bin/tags/update?access_token=[access_token]'
        ],
        'del_tag' => [
            'request_way' => 'POST',
            'request_uri' => '/cgi-bin/tags/delete?access_token=[access_token]'
        ],
        'get_tag_openid' => [
            'request_way' => 'POST',
            'request_uri' => '/cgi-bin/user/tag/get?access_token=[access_token]'
        ],

        /*账号管理*/
        'qrcode_create' => [
            'request_way' => 'POST',
            'request_uri' => '/cgi-bin/qrcode/create?access_token=[access_token]'
        ],
    ];
}