<?php
/**
 * Created by PhpStorm.
 * User: wenqidong
 * Date: 2018/5/17
 * Time: 下午4:16
 */
namespace Home\Service;

use Home\Model\GameModel;

class GameService{

    public static function table(){
        return D("Game");
    }

    public static function create($questionCount = 5){
        $data = [
            'total_question' => $questionCount,
            'remain_num' =>$questionCount,
            'state' => GameModel::GAME_READY,
            'team_a_score'=>0,
            'team_b_score'=>0,
            'create_time'=>time(),
            'update_time'=>time()
        ];
        $newId = self::table()->add($data);
        if($newId){
            return self::getGameById($newId);
        }else{
            return false;
        }
    }

    public static function getGameById($gameId){
        return self::table()->where(['game_id'=>$gameId])->find();
    }

    public static function updateGame($gameId,$data){
        $data['update_time'] = time();
        return self::table()->where(['id'=>$gameId])->save($data);
    }

}