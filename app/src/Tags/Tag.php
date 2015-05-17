<?php

namespace Anax\Tags;
 
/**
 * Model for Tags.
 *
 */
class Tag extends \Anax\MVC\CDatabaseModel{

	public function findByName($tagName) {
		$tags = $this->query("*")
		->where("lova15_tag.name = '{$tagName}'")
		->execute();
		if(isset($tags[0])) {
			return $tags[0];
		}
		return null;
	}

	public function findWithPostCount($orderBy = "name", $limit = null) {
		$this->query("
			lova15_tag.id AS id,
			lova15_tag.name AS name,
			Count(lova15_post_tag.id) AS postCount
		")
		->join("post_tag", "lova15_tag.id = lova15_post_tag.tag_id")
		->orderBy("{$orderBy} DESC")
		->groupBy("id");

		if(isset($limit)) {
			$this->limit($limit);
		}
		
		$tags = $this->execute();
		return $tags;
	}

	public function findNrOfQuestions($tagId) {
		$result = $this->query("
			Count(lova15_post_tag.post_id) AS questionsCount
		")
		->from("post_tag")
		->where("lova15_post_tag.tag_id = {$tagId}")
		->execute();

		return $result[0]->questionsCount;
	}

	public function addToPostAndTag($postId, $tagId) {
		$this->insert(
		    'post_tag',
		    ['post_id' => 35, 'tag_id' => 50]
		);
		echo $this->getSQL();
	}
}