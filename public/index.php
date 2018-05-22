<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用入口文件
$env = strtolower(getenv('RUN_ENV'));

if(!$env){
    $env = 'pro';
}
define('ENV',$env);

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',true);

// 定义应用目录
define('APP_PATH','../Application/');

// 定义Runtime
define('RUNTIME_PATH','../Runtime/');

// 定义应用公共目录
define('COMMON_PATH','../Data/');

// 定义Conf
define('CONF_PATH',COMMON_PATH.'/Conf/');

// 定义模板目录
define('TMPL_PATH','../Template/');

// UEditor编辑器图片上传路径
define('UPLOAD_PATH','public/uploads/');

// 引入ThinkPHP入口文件
require '../ThinkPHP/ThinkPHP.php';

// 亲^_^ 后面不需要任何代码了 就是如此简单