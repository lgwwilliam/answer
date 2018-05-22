<?php
/**
 * Created by PhpStorm.
 * User: wenqidong
 * Date: 2018/5/17
 * Time: 下午4:17
 */
namespace Home\Model;

use Think\Model;

class GameModel extends Model{

    const GAME_READY = 1;
    const GAME_START = 2;
    const GAME_END = 3;

}