<?php

namespace Anax\Posts;
 
/**
 * Model for Posts.
 *
 */
class Post extends \Anax\MVC\CDatabaseModel{

    /**
     * finds a question
     * @param  [type] $postId [description]
     * @return [type]         [description]
     */
    public function find($postId) {
        $posts = $this->query("
            lova15_post.id AS id, 
            lova15_post.title AS title, 
            lova15_post.body AS body, 
            lova15_post.created AS created, 
            lova15_post.parent AS parent,
            lova15_user.id AS user_id,
            lova15_user.name AS user_name,
            lova15_user.email AS user_email
        ")
        ->join("user", "lova15_post.user_id = lova15_user.id")
        ->where("lova15_post.id = ?")
        ->groupBy("lova15_post.id")
        ->execute([$postId]);
        if(isset($posts[0])) {
            return $posts[0];
        }
        return null;
    }

    /**
     * finds a question
     * @param  [type] $postId [description]
     * @return [type]         [description]
     */
	public function findQuestion($postId) {
        $posts = $this->query("
            lova15_post.id AS id, 
            lova15_post.title AS title, 
            lova15_post.body AS body, 
            lova15_post.created AS created, 
            lova15_post.parent AS parent, 
            lova15_user.id AS user_id, 
            lova15_user.name AS user_name, 
            lova15_user.email AS user_email,
            Count(lova15_vote.vote) as voteCount,
            SUM(lova15_vote.vote) as voteScore, 
            GROUP_CONCAT(distinct lova15_tag.name SEPARATOR ',') as tags
        ")
        ->join('user', 'lova15_post.user_id = lova15_user.id')
        ->leftJoin('vote', 'lova15_post.id = lova15_vote.post_id')
        ->leftJoin('post_tag', 'lova15_post.id = lova15_post_tag.post_id')
        ->leftJoin('tag', 'lova15_post_tag.id = lova15_tag.id')
        ->where("lova15_post.id = ?")
        ->andWhere("lova15_post.parent IS NULL")
        ->groupBy("lova15_post.id")
        ->execute([$postId]);

        foreach ($posts as $post) {
            if(!isset($post->voteScore)) {
                $post->voteScore = 0;
            }
            if(isset($post->tags)) {
                $post->tags = explode(',', $post->tags);
            }
            if(isset($post->votes)){
                $post->votes = explode(',', $post->votes);
            }      
        }
        if(isset($posts[0])) {
            return $posts[0];
        }
        return null;
    }

    /**
     * find a questions answers
     */
    public function findAnswers($postId) {
        $posts = $this->query("
            lova15_post.id AS id, 
            lova15_post.title AS title, 
            lova15_post.body AS body, 
            lova15_post.created AS created, 
            lova15_post.parent AS parent, 
            lova15_user.id AS user_id, 
            lova15_user.name AS user_name, 
            lova15_user.email AS user_email,
            Count(lova15_vote.vote) as voteCount,
            SUM(lova15_vote.vote) as voteScore, 
            GROUP_CONCAT(distinct lova15_tag.name SEPARATOR ',') as tags
        ")
        ->join('user', 'lova15_post.user_id = lova15_user.id')
        ->leftJoin('vote', 'lova15_post.id = lova15_vote.post_id')
        ->leftJoin('post_tag', 'lova15_post.id = lova15_post_tag.post_id')
        ->leftJoin('tag', 'lova15_post_tag.id = lova15_tag.id')
        ->where("lova15_post.parent = {$postId}")
        ->groupBy("lova15_post.id")
        ->orderBy("voteScore DESC")
        ->execute();

        foreach ($posts as $post) {
            if(!isset($post->voteScore)) {
                $post->voteScore = 0;
            }
            if(isset($post->tags)) {
                $post->tags = explode(',', $post->tags);
            }
            if(isset($post->votes)){
                $post->votes = explode(',', $post->votes);
            }      
        }
        return $posts;
    }

    /**
     * finds a question
     * @param  [type] $postId [description]
     * @return [type]         [description]
     */
    public function findQuestions($orderBy) {
        $this->query("
            lova15_post.id      AS id,
            lova15_post.title   AS title,
            lova15_post.body    AS body,
            lova15_post.created AS created,
            lova15_user.name    AS user_name,
            lova15_user.id      AS user_id,
            lova15_user.email   AS user_email,
            GROUP_CONCAT(distinct lova15_tag.name SEPARATOR ',') AS tags,
            Count(lova15_vote.vote) as voteCount,
            SUM(lova15_vote.vote) as voteScore
        ")
        ->join('user', 'lova15_post.user_id = lova15_user.id')
        ->join('post_tag', 'lova15_post.id = lova15_post_tag.post_id')
        ->join('tag', 'lova15_tag.id = lova15_post_tag.tag_id')
        ->leftJoin('vote', 'lova15_post.id = lova15_vote.post_id')
        ->groupBy('id')
        ->where('lova15_post.parent IS NULL')
        ->orderBy("{$orderBy} DESC");
        
        $posts = $this->execute();

        foreach ($posts as $post) {
            if(!isset($post->voteScore)) {
                $post->voteScore = 0;
            }
            if(isset($post->tags)) {
                $post->tags = explode(',', $post->tags);
            }
            if(isset($post->votes)){
                $post->votes = explode(',', $post->votes);
            }      
        }
        return $posts;
    }

    /**
     * finds a question
     * @param  [type] $postId [description]
     * @return [type]         [description]
     */
    public function findLatest($limit = 5) {
        $this->query("
            lova15_post.id      AS id,
            lova15_post.title   AS title,
            lova15_post.body    AS body,
            lova15_post.created AS created,
            lova15_user.name    AS user_name,
            lova15_user.id      AS user_id,
            lova15_user.email   AS user_email,
            GROUP_CONCAT(distinct lova15_tag.name SEPARATOR ',') AS tags,
            Count(lova15_vote.vote) as voteCount,
            SUM(lova15_vote.vote) as voteScore
        ")
        ->join('user', 'lova15_post.user_id = lova15_user.id')
        ->join('post_tag', 'lova15_post.id = lova15_post_tag.post_id')
        ->join('tag', 'lova15_tag.id = lova15_post_tag.tag_id')
        ->leftJoin('vote', 'lova15_post.id = lova15_vote.post_id')
        ->groupBy('id')
        ->where('lova15_post.parent IS NULL')
        ->orderBy("created DESC")
        ->limit($limit);
        
        $posts = $this->execute();

        foreach ($posts as $post) {
            if(!isset($post->voteScore)) {
                $post->voteScore = 0;
            }
            if(isset($post->tags)) {
                $post->tags = explode(',', $post->tags);
            }
            if(isset($post->votes)){
                $post->votes = explode(',', $post->votes);
            }      
        }
        return $posts;
    }

    public function findQuestionsByTag($tagName, $orderBy) {
        $this->query("
            lova15_post.id      AS id,
            lova15_post.title   AS title,
            lova15_post.body    AS body,
            lova15_post.created AS created,
            lova15_user.name    AS user_name,
            lova15_user.id      AS user_id,
            lova15_user.email   AS user_email,
            GROUP_CONCAT(distinct lova15_tag.name SEPARATOR ',') AS tags,
            Count(lova15_vote.vote) as voteCount,
            SUM(lova15_vote.vote) as voteScore
        ")
        ->join('user', 'lova15_post.user_id = lova15_user.id')
        ->join('post_tag', 'lova15_post.id = lova15_post_tag.post_id')
        ->join('tag', 'lova15_tag.id = lova15_post_tag.tag_id')
        ->leftJoin('vote', 'lova15_post.id = lova15_vote.post_id')
        ->groupBy('id')
        ->having('FIND_IN_SET(?, tags)')
        ->orderBy("{$orderBy} DESC");

        $posts = $this->execute([$tagName]);

        foreach ($posts as $post) {
            if(!isset($post->voteScore)) {
                $post->voteScore = 0;
            }
            if(isset($post->tags)) {
                $post->tags = explode(',', $post->tags);
            }
            if(isset($post->votes)){
                $post->votes = explode(',', $post->votes);
            }   
        }
        return $posts;
    }

    /**
     * find a questions answers
     */
    public function findNrOfAnswers($postId) {
        $posts = $this->query("
            Count(lova15_post.id) AS answerCount
        ")
        ->where("lova15_post.parent = {$postId}")
        ->execute();
        if(!isset($posts[0]->answerCount)) {
            return 0;
        }
        return $posts[0]->answerCount;
    }

    public function findQuestionsByUser($userId, $limit = 5, $orderBy = "created"){
        $this->query("
            lova15_post.id      AS id,
            lova15_post.title   AS title,
            lova15_post.body    AS body,
            lova15_post.created AS created,
            GROUP_CONCAT(distinct lova15_tag.name SEPARATOR ',') AS tags,
            Count(lova15_vote.vote) as voteCount,
            SUM(lova15_vote.vote) as voteScore
        ")
        ->join('post_tag', 'lova15_post.id = lova15_post_tag.post_id')
        ->join('tag', 'lova15_tag.id = lova15_post_tag.tag_id')
        ->leftJoin('vote', 'lova15_post.id = lova15_vote.post_id')
        ->groupBy('id')
        ->where('lova15_post.parent IS NULL')
        ->andWhere("lova15_post.user_id = {$userId}")
        ->orderBy("{$orderBy} DESC")
        ->limit($limit);
        
        $posts = $this->execute();

        foreach ($posts as $post) {
            if(!isset($post->voteScore)) {
                $post->voteScore = 0;
            }
            if(isset($post->tags)) {
                $post->tags = explode(',', $post->tags);
            }
        }
        return $posts;
    }
    
    public function findAnswersByUser($userId, $limit = 5, $orderBy = "created"){
        $this->query("
            lova15_post.id      AS id,
            lova15_post.body    AS body,
            lova15_post.parent AS parent,
            lova15_post.created AS created,
            Count(lova15_vote.vote) as voteCount,
            SUM(lova15_vote.vote) as voteScore
        ")
        ->leftJoin('vote', 'lova15_post.id = lova15_vote.post_id')
        ->groupBy('id')
        ->where('lova15_post.parent IS NOT NULL')
        ->andWhere("lova15_post.user_id = {$userId}")
        ->orderBy("{$orderBy} DESC")
        ->limit($limit);
        $posts = $this->execute();     
        foreach ($posts as $post) {
            if(!isset($post->voteScore)) {
                $post->voteScore = 0;
            }  
        }
        return $posts;
    }
}