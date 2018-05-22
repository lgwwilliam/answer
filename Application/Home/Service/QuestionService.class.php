<?php
/**
 * Created by PhpStorm.
 * User: wenqidong
 * Date: 2018/5/17
 * Time: 下午3:37
 */

namespace Home\Service;

class QuestionService
{

    public static function table()
    {
        return M('Question');
    }

    public static function getQuestionById($id)
    {
        return self::table()->where(['id' => $id])->find();
    }

    public static function getAllQuestions()
    {
        if ($cache = S('all-questions')) {
            return unserialize($cache);
        } else {
            if ($questions = self::table()->select()) {
                S('all-questions', serialize($questions));
                return $questions;
            } else {
                return [];
            }
        }
    }

}