<?php
namespace Anax\Tags;
 
/**
 * A controller for votes
 *
 */
class TagsController implements \Anax\DI\IInjectionAware{
    use \Anax\DI\TInjectable;

	/**
	 * Initialize the controller.
	 * @return void
	 */
	public function initialize(){
	    $this->tags = new \Anax\Tags\Tag();
	    $this->tags->setDI($this->di);
	}

	public function indexAction() {
		$tags = $this->tags->findAll();
        foreach ($tags as $tag) {
            self::getNumberOfQuestions($tag);
        }

        $title = "Alla taggar";
        $this->theme->setTitle($title);
        $this->views->add(
            'tag/all', 
            [
                'title' => $title,
                'tags' => $tags,
            ]
        );
	}

	public function addTagsToPost($tags, $postId) {
		$this->tags = new \Anax\Tags\Tag();
	    $this->tags->setDI($this->di);
		foreach($tags as $tag) {
            $dbTag = $this->tags->findByName($tag);
            if(!$dbTag) {
            	$noBlanks = str_replace(' ', '', $tag);
            	$capital = ucfirst($noBlanks);
            	$newTag = [
            		"name" => str_replace(' ', '', $capital)
            	];
				$this->tags->create($newTag);
				$tagId = $this->tags->id;
            } else {
            	$tagId = $dbTag->id;
            }
            $this->Post_TagsController->add($postId, $tagId);
        }
	}

	/**
     * Gets number of answers for a post
     */
	private function getNumberOfQuestions($tag) {
        $tag->nrOfQuestions = $this->tags->findNrOfQuestions($tag->id);
	}

}