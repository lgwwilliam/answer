<?php
/**
 * Created by PhpStorm.
 * User: wenqidong
 * Date: 2018/5/17
 * Time: 下午3:41
 */
namespace Home\Controller\Common;

use Think\Controller;

class BaseController extends Controller{

    const CODE_OK = 0;
    const CODE_FAIL = 1;
    const GAME_END = 2;

    protected $user;

    function _initialize(){

    }

    public static function apiResponse($data,$code=self::CODE_OK,$message=''){
        $res = [
            'code'=>$code,
            'data'=>$data,
            'message'=>$message
        ];
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($res,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK));
    }

}