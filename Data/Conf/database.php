<?php
$db = [
    'local' =>[
        'DB_TYPE'               =>  'mysql',     // 数据库类型
        'DB_HOST'               =>  '127.0.0.1', // 服务器地址
        'DB_NAME'               =>  'quickanswer',          // 数据库名
        'DB_USER'               =>  'root',      // 用户名
        'DB_PWD'                =>  'root',          // 密码
        'DB_PORT'               =>  '3308',        // 端口
        'DB_PREFIX'             =>  't_',    // 数据库表前缀
    ],
    'pro'=>[
        'DB_TYPE'               =>  'mysql',     // 数据库类型
        'DB_HOST'               =>  '127.0.0.1', // 服务器地址
        'DB_NAME'               =>  'quickanswer',          // 数据库名
        'DB_USER'               =>  'quickanswer',      // 用户名
        'DB_PWD'                =>  'Quickanswer@2018',          // 密码
        'DB_PORT'               =>  '3306',        // 端口
        'DB_PREFIX'             =>  't_',    // 数据库表前缀
    ]
];

switch (ENV){
    case 'local':
        return $db['local'];
        break;
    case 'pro':
        return $db['pro'];
        break;
    default:
//        return $db['pro'];
}