<?php
namespace Anax\Votes;
 
/**
 * A controller for votes
 *
 */
class VotesController implements \Anax\DI\IInjectionAware{
    use \Anax\DI\TInjectable;

	/**
	 * Initialize the controller.
	 * @return void
	 */
	public function initialize(){
	    $this->votes = new \Anax\Votes\Vote();
	    $this->votes->setDI($this->di);
	}

	public function upVoteAction($postId = -1) {
        self::vote($postId, 1);
    }

    public function downVoteAction($postId = -1) {
        self::vote($postId, -1);
    }

    private function vote($postId, $score) {
        $posts = new \Anax\Posts\Post();
        $posts->setDI($this->di);
        $this->UsersController->requireLogin("Du måste logga in för att rösta på inlägg.");
        $hasVoted = $this->votes->hasVoted($postId, $this->UsersController->getCurrentUser()->id);
        $post = $posts->find($postId);
        if (!$post) {
            $this->FlashMessages->addWarning("Kunde inte hitta inlägget som du försökte rösta på.");
            $this->response->redirect($this->url->create(""));
        } else if ($hasVoted) {
            $this->FlashMessages->addWarning("Du har redan röstat på detta inlägg.");
            self::sendBack($post);
        } else if ($this->UsersController->getCurrentUser()->id === $post->user_id) {
            $this->FlashMessages->addWarning("Du kan inte rösta på dina egna inlägg.");
            self::sendBack($post);
        } else if($this->votes->vote($postId, $this->UsersController->getCurrentUser()->id, $score)) {
            $this->FlashMessages->addSuccess("Din röst sparades!");
        } else {
            $this->FlashMessages->addError("Rösten kunde inte sparas av okänd anledning.");
            self::sendBack($post);
        }
    }

    private function sendBack($post){
        if(isset($post->parent)) {
            $this->response->redirect($this->url->create("posts/view/{$post->parent}"));
        } else {
            $this->response->redirect($this->url->create("posts/view/{$post->id}"));
        }
    }
}