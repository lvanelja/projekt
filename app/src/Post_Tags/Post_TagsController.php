<?php
namespace Anax\Post_Tags;
 
/**
 * A controller for votes
 *
 */
class Post_TagsController implements \Anax\DI\IInjectionAware{
    use \Anax\DI\TInjectable;

	/**
	 * Initialize the controller.
	 * @return void
	 */
	public function initialize(){
	    $this->post_tags = new \Anax\Post_Tags\Post_Tag();
	    $this->post_tags->setDI($this->di);
	}

	public function add($postId, $tagId) {
        $this->post_tags = new \Anax\Post_Tags\Post_Tag();
        $this->post_tags->setDI($this->di);
        $newObject = [
            "post_id" => $postId,
            "tag_id" => $tagId
        ];
        $this->post_tags->create($newObject);
    }

}