<?php
/**
 * Created by PhpStorm.
 * User: wenqidong
 * Date: 2018/5/17
 * Time: 下午3:42
 */

namespace Home\Controller\Common;

use GatewayClient\Gateway;
use Home\Model\AnswerModel;
use Home\Model\GameModel;
use Home\Service\AnswerService;
use Home\Service\GameService;
use Home\Service\QuestionService;

require_once '../GatewayClient/Gateway.php';

class ApiController extends BaseController{

    public function getAllQuestions(){
        $data = QuestionService::getAllQuestions();
        parent::apiResponse($data);
    }

    public function getPlayer(){
        $player = session('player');
        if(!$player){
            self::apiResponse([],parent::CODE_FAIL,'玩家未登陆');
        }else{
            parent::apiResponse($player);
        }
    }

    public function playerLogin(){
        $players = C('PLAYER');
        $userId = I('post.user_id');
        $password = I('post.password');
        if(!$userId || !$password){
            parent::apiResponse('',parent::CODE_FAIL,'用户名或密码不能为空');
        }
        if(key_exists($userId,$players)){
            $player = $players[$userId];
            if(md5($player['password']) == md5($password)){
                session('player',$player);
                parent::apiResponse('success');
            }else{
                parent::apiResponse('',parent::CODE_FAIL,'登陆密码错误');
            }
        }else{
            parent::apiResponse('',parent::CODE_FAIL,'无效用户名');
        }
    }

    public function getPlayers(){
        $players = C('PLAYER');
        $data = [];
        foreach ($players as $key=>$player){
            if($player['team'] == 'a'){
                $data['player_a'] = $player;
            }elseif ($player['team'] == 'b'){
                $data['player_b'] = $player;
            }
        }
        if(count($data)!=2){
            parent::apiResponse('',parent::CODE_FAIL,'队员数据错误');
        }
        parent::apiResponse($data);
    }

    public function getAnotherPerson(){
        if(!session('player')){
            parent::apiResponse('',parent::CODE_FAIL,'当前用户未登陆');
        }
        $players = C('PLAYER');
        unset($players[session('player.id')]);
        foreach ($players as $key=>$player){
            self::apiResponse($player);
        }
        self::apiResponse('',parent::CODE_FAIL,'服务出错');
    }

    public function getCreateGame(){
        $questionCount = 5;
        $game = GameService::create($questionCount);
        if(!$game){
            parent::apiResponse('',parent::CODE_FAIL,'创建失败');
        }
        parent::apiResponse($game);
    }

    public function getWinner(){
        $gameId = I('game_id');
        if(!$gameId){
            parent::apiResponse('',parent::CODE_FAIL,'参数错误');
        }
        $game = GameService::getGameById($gameId);
        if(!$game){
            parent::apiResponse('',parent::CODE_FAIL,'参数错误');
        }
        $players = C("PLAYER");
        $team_a = [];
        $team_b = [];
        foreach ($players as $player){
            if($player['team'] == 'a'){
                $team_a = $player;
            }else{
                $team_b = $player;
            }
        }
        if($game['team_a_score']>$game['team_b_score']){
            $winner['name'] = $team_a['name'];
            $winner['avatar'] = $team_a['avatar'];
            $winner['score']  = $game['team_a_score'];
        }else if($game['team_a_score']<$game['team_b_score']){
            $winner['name'] = $team_b['name'];
            $winner['avatar'] = $team_b['avatar'];
            $winner['score'] = $game['team_b_score'];
        }else{
            $winner['name'] = $team_a['name'];
            $winner['avatar'] = $team_a['avatar'];
            $winner['score']  = $game['team_a_score'];
        }
        parent::apiResponse($winner);
    }

    // send socket
    public function startGame(){
        $gameId = I('game_id');
        if(!$gameId){
            parent::apiResponse('',parent::CODE_FAIL,'参数错误');
        }
        $game = GameService::getGameById($gameId);
        if($game['state']!=GameModel::GAME_READY){
            parent::apiResponse('',parent::CODE_FAIL,'游戏状态异常，请刷新重试');
        }
        $update['state'] = GameModel::GAME_START;
        if($state = GameService::updateGame($gameId,$update)){
            // 通知答题端与大屏开始倒计时，准备答题
            $game['state'] = GameModel::GAME_START;
            $this->pushToAll('start',$game);
        }else{
            parent::apiResponse('',parent::CODE_FAIL,'开启游戏失败');
        }
    }

    // send socket
    public function startQuestion(){
        $gameId = I('game_id');
        $questionId = I('question_id');
        if(!$gameId || !$questionId){
            parent::apiResponse('',parent::CODE_FAIL,'参数错误');
        }
        $game = GameService::getGameById($gameId);
        if(!$game){
            parent::apiResponse('',parent::CODE_FAIL,'参数错误');
        }
        if($game['state'] == GameModel::GAME_END){
            parent::apiResponse('',parent::GAME_END,'游戏已结束');
        }
        if($game['state'] == GameModel::GAME_READY){
            parent::apiResponse('',parent::CODE_FAIL,'游戏未开始');
        }
        if($game['remain_num']<=0){
            $update['state'] = GameModel::GAME_END;
            if($state = GameService::updateGame($gameId,$update)){
                //通知大屏游戏已结束
                $game['state'] = GameModel::GAME_END;
                $this->pushToAll('over',$game);
            }

        }else{
            $question = QuestionService::getQuestionById($questionId);
            $update['remain_num'] = ['exp',"remain_num-1"];
            if($state = GameService::updateGame($gameId,$update)){
                // 将gameId, questionId推送给答题端
                $this->pushToAll('question',$question);
            }
        }
    }

    // send socket
    // 在第8秒之前答完都是满分200分，然后时间每多一秒答题，分数都减少20分，最低分是100分，也就是时间剩余三秒之后分数都是100了
    public function doAnswer(){
        $gameId = I('game_id');
        $questionId = I('question_id');
        $answer = I('answer',0,'intval');
        $seconds = I('time_sec');
        if(!session('player')){
            self::apiResponse('',parent::CODE_FAIL,'登陆状态失效,请重新登陆');
        }
        if(!$questionId || !$gameId ){
            parent::apiResponse('',parent::CODE_FAIL,'参数错误');
        }
        if(AnswerService::hasAnswer($gameId,$questionId,session('player.team'))){
            parent::apiResponse('',parent::CODE_FAIL,'您已答过此题，不可重复答题');
        }
        $question = QuestionService::getQuestionById($questionId);
        $model = M();
        $log['game_id'] = $gameId;
        $log['team_code'] = session('player.team');
        $log['question_id'] = $questionId;
        $log['answer'] = $answer;
        if($question && $question['a_answer']==$answer){
            $log['result'] = AnswerModel::ANSWER_CORRECT;
            $log['seconds'] = $seconds;
            $log['score'] = $this->getScore($seconds);
        }else{
            $log['result']= AnswerModel::ANSWER_WRONG;
            $log['seconds'] = $seconds;
            $log['score'] = 0;
        }
        $model->startTrans();
        $addLog = AnswerService::add($log);
        if(!$addLog){
            $model->rollback();
            self::apiResponse('',parent::CODE_FAIL,'记录答案出错');
        }
        $score= $log['score'];
        if(session('player.team')=='a'){
            $update['team_a_score'] = ['exp',"team_a_score+$score"];
            $state = GameService::updateGame($gameId,$update);
            if(!$state){
                $model->rollback();
                self::apiResponse('',parent::CODE_FAIL,'更新分数出错');
            }
        }
        if(session('player.team')=='b'){
            $update['team_b_score'] =  ['exp',"team_b_score+$score"];
            $state = GameService::updateGame($gameId,$update);
            if(!$state){
                $model->rollback();
                self::apiResponse('',parent::CODE_FAIL,'更新分数出错');
            }
        }
        $model->commit();
        $game = GameService::getGameById($gameId);
        // 推送所有端答题情况
        $this->pushToAll('game',$game);
        parent::apiResponse($game);

    }

    public function ready(){
        $player = session('player');
        if(!$player){
            parent::apiResponse('',parent::CODE_FAIL,'登陆失效，请重新登陆');
        }
        $this->pushToAll('ready',['team'=>$player['team']]);
    }

    public function getAnswerResult(){
        $gameId = I('game_id');
        $questionId = I('question_id');
        $data = AnswerService::getGameQuestionResult($gameId,$questionId);
        $question = QuestionService::getQuestionById($questionId);
        $data['correct'] = $question['a_answer'];
        $this->pushToAll('result',$data);
        parent::apiResponse($data);
    }

    private function getScore($seconds){
        if($seconds<3){
            return 200;
        }elseif ($seconds==4){
            return 180;
        }elseif ($seconds==5){
            return 160;
        }elseif ($seconds==6){
            return 140;
        }elseif ($seconds==7){
            return 120;
        }else{
            return 100;
        }
    }

    public function bindClient(){
        $client_id=I('post.client_id');
        $terminal =I('post.terminal');
        Gateway::bindUid($client_id,$terminal);
        echo 'OK';
    }

    // type=game|question|start|over
    public function pushToAll($type,$data){
        Gateway::sendToAll(json_encode(['type'=>$type,'data'=>$data]));
    }

    public function pushToTerminal($terminal,$type,$data){
        Gateway::sendToUid($terminal,json_encode(['type'=>$type,'data'=>$data]));
    }

}