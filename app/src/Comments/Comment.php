<?php

namespace Anax\Comments;
 
/**
 * Model for Comments.
 *
 */
class Comment extends \Anax\MVC\CDatabaseModel{

	/**
     * find a questions answers
     */
    public function findByPost($postId) {
        $comments = $this->query("
            lova15_comment.id AS id, 
            lova15_comment.body AS body, 
            lova15_comment.created AS created, 
            lova15_user.id AS user_id, 
            lova15_user.name AS user_name, 
            lova15_user.email AS user_email
        ")
        ->join('user', 'lova15_comment.user_id = lova15_user.id')
        ->where("lova15_comment.post_id = {$postId}")
        ->groupBy("lova15_comment.id")
        ->execute();

        return $comments;
    }

    public function findCommentsByUser($userId, $limit = 5, $orderBy = "lova15_comment.created") {
        $comments = $this->query("
            *
        ")
        ->where("lova15_comment.user_id = {$userId}")
        ->groupBy("lova15_comment.id")
        ->limit($limit)
        ->orderBy("lova15_comment.created DESC")
        ->execute();

        return $comments;
    }

}