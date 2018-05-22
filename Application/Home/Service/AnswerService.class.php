<?php
/**
 * Created by PhpStorm.
 * User: wenqidong
 * Date: 2018/5/17
 * Time: 下午4:24
 */

namespace Home\Service;

class AnswerService{

    public static function table(){
        return D('Question');
    }

    public static function add($data){
        $data['create_time'] = time();
        return self::table()->add($data);
    }

    public static function getGameAnswerResultByGameId($gameId){
        return self::table()->where(['game_id'=>$gameId])->select();
    }

    public static function getGameAnswerResultByTeamCode($gameId,$teamCode){
        return self::table()->where(['game_id'=>$gameId,'team_code'=>$teamCode])->select();
    }

    public static function hasAnswer($gameId,$questionId,$teamCode){
        $where = [
            'game_id'=>$gameId,
            'question_id'=>$questionId,
            'team_code'=>$teamCode
        ];
        return self::getCountByWhere($where);
    }

    public static function getCountByWhere($where){
        return self::table()->where($where)->count();
    }

    public static function getGameQuestionResult($gameId,$questionId){
        $where = [
            'game_id'=>$gameId,
            'question_id'=>$questionId,
            'team_code' =>'a'
        ];
        $result = [];
        $a = self::table()->where($where)->find();
        $result['team_a'] = $a?:[];
        $where['team_code'] = 'b';
        $b = self::table()->where($where)->find();
        $result['team_b'] = $b?:[];
        return $result;
    }

}