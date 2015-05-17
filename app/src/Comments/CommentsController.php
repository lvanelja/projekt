<?php


namespace Anax\Comments;
 
/**
 * A controller for comments and admin related events.
 *
 */
class CommentsController implements \Anax\DI\IInjectionAware{
    use \Anax\DI\TInjectable;

    /**
	 * Initialize the controller.
	 *
	 * @return void
	 */
	public function initialize(){
	    $this->comments = new \Anax\Comments\Comment();
	    $this->comments->setDI($this->di);
	}

    /**
     * Edit a comment
     *
     * @param int $id of comment to edit
     * 
     * @return void
     */
    public function newAction($postId = -1){   

        $this->UsersController->requireLogin("Du måste logga in för att kommentera");
        
        $post = new \Anax\Posts\Post();
        $post->setDI($this->di); 
        $post = $post->find($postId);

        if(!$post) {
            $this->FlashMessages->addError("Kunde inte hitta inlägg att kommentera.");
            $this->response->redirect($this->url->create("posts/all"));
        }

        $form = self::createCommentForm();
        self::receiveCommentForm($form, $post);
        
        $title = "Kommentera";
        if($post->title) {
            $title .= ": {$post->title}";
        } 
        $this->theme->setTitle($title);
        $this->views->add(
            'default/page', 
            [
                'title' => $title,
                'content' => $form->getHTML(),
            ]
        );
    }

    private function receiveCommentForm($form, $post) {
        $status = $form->check();

        if ($status === true) {
            $now = date('Y-m-d H:i:s');
            $userId = $this->UsersController->getCurrentUser()->id;
            $newComment = [
                'user_id' => $userId,
                'post_id' => $post->id,
                'body' => $form->value('body'),
                'created' => $now,
            ];

            if($this->comments->save($newComment)){
                $this->FlashMessages->addSuccess("Kommentaren skickades!");
                if(isset($post->parent)) {
                    $this->response->redirect($this->url->create("posts/view/{$post->parent}"));
                }
                $this->response->redirect($this->url->create("posts/view/{$post->id}"));
            }
        }
    }

    /**
	 * Creates a form to edit/create comment 
	 * 
	 * @param  $comment_details, comment array if editing
	 * 
	 * @return form, the CForm that was created
	 */
	private function createCommentForm($comment_details = NULL){
		
		$submit_text = "Skicka kommentar";
		if($comment_details){
			$submit_text = "Spara ändringar";
		}

		$form = $this->di->form->create([], [ 
            'body' => [ 
                'class'      => 'form-control',
                'type'       => 'textarea', 
                'label'      => 'Kommentar', 
                'value'      => $comment_details["content"], 
                'validation' => ['not_empty'] 
            ], 
            'submit' => [ 
                'class'      => 'btn btn-primary',
                'type'       => 'submit', 
                'value'      => $submit_text, 
                'callback'   => function ($form) { 
                    return true; 
                } 
            ] 
        ]); 
		return $form;
	}

}