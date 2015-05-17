<?php

namespace Anax\Users;
 
/**
 * Model for Users.
 *
 */
class User extends \Anax\MVC\CDatabaseModel {
 	
	/** 
	* finds user by email
	*/
	public function findByEmail($email){
		$users = $this->query("*
		")
		->where("email = ?")
		->execute([$email]); 
		if(isset($users[0])) {
			return $users[0];
		}
		return null;
	}

	/** 
	* finds 10 most active users
	*/
	public function findActive(){
		$users = $this->query("
			lova15_user.id  	AS id,
			lova15_user.name 	AS name,
			lova15_user.email 	AS email,
			Count(lova15_post.id) AS postCount
		")
		->join("post", "lova15_user.id = lova15_post.user_id")
		->groupBy("id")
		->orderBy("postCount DESC")
		->limit(10)
		->execute(); 
		return $users;
	}

}