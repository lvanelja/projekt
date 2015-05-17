<?php

namespace Anax\Votes;
 
/**
 * Model for Votes.
 *
 */
class Vote extends \Anax\MVC\CDatabaseModel{

    public function hasVoted($postId, $userId) {
        $votes = $this->query("*")
        ->where("post_id = {$postId}")
        ->andWhere("user_id = {$userId}")
        ->execute();
        return (count($votes) > 0);
    }
	
    public function vote($postId, $userId, $vote) {
		$now = date('Y-m-d H:i:s');
		$newVote = [
            'post_id' => $postId,
            'user_id' => $userId,
            'vote' => $vote,
            'created' => $now,
        ];
        return $this->save($newVote);
	}
}