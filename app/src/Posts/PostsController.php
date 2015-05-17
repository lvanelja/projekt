<?php

namespace Anax\Posts;
 
/**
 * A controller for posts and admin related events.
 *
 */
class PostsController implements \Anax\DI\IInjectionAware{
   use \Anax\DI\TInjectable;

    /**
	 * Initialize the controller.
	 *
	 * @return void
	 */
	public function initialize(){
	    $this->posts = new \Anax\Posts\Post();
	    $this->posts->setDI($this->di);
	}

    /**
     * Shows all the posts
     * @return void
     */
    public function newAction() {
        $this->UsersController->requireLogin("Du måste logga in innan du kan skriva inlägg!");

        $form = self::createPostForm();
        self::recieveCreateForm($form);
        
        $title = "Skriv en ny fråga";
        $this->theme->setTitle($title);

        $this->views->add(
            'default/page', 
            [
                'title' => $title,
                'content' => $form->getHTML(),
            ]
        );
    }

    /**
     * Shows all questions
     */
    public function indexAction() {

        $posts = $this->posts->findQuestions(self::getOrderBy());
        foreach ($posts as $post) {
            self::getNrOfAnswers($post);
        }

        $title = "Alla frågor";
        $this->theme->setTitle($title);
        $this->views->add(
            'post/all', 
            [
                'title' => $title,
                'posts' => $posts,
            ]
        );
    }

    private function getOrderBy() {
        $orderBy = "created";
        if(isset($_GET["orderby"])) {
            switch ($_GET["orderby"]) {
                case "voteScore":
                    $orderBy = "voteScore";
                    break;
                case "voteCount":
                    $orderBy = "voteCount";
                    break;                
                default:
                    $orderBy = "created";
                    break;
            }
        }
        return $orderBy;
    }
    /**
     * Shows all questions
     */
    public function tagAction($tagName = "tagthatwontexist") {

        $tagName = urldecode($tagName);
        $posts = $this->posts->findQuestionsByTag($tagName, self::getOrderBy());
        foreach ($posts as $post) {
            self::getNrOfAnswers($post);
        }

        $title = "Frågor med tagg: {$tagName}";
        $this->theme->setTitle($title);
        $this->views->add(
            'post/all', 
            [
                'title' => $title,
                'posts' => $posts,
            ]
        );
    }

	/**
     * View a post.
     */
    public function viewAction($id = -1){
        $question = $this->posts->findQuestion($id);

        if($question == null){
            $answer = $this->posts->find($id);
            if(isset($answer->parent)) {
                $question = $this->posts->findQuestion($answer->parent);
                $id = $question->id;
            } else {
                $this->FlashMessages->addError("Kunde inte hitta detta inlägg.");
                $this->response->redirect($this->url->create(""));
            } 
            if($question == null) {
                $this->FlashMessages->addError("Kunde inte hitta detta inlägg.");
                $this->response->redirect($this->url->create(""));
            }
        }

        $answer_form = self::createAnswerForm($question->id);
        self::recieveAnswerForm($answer_form);

        // Add user, tags and votes for question
        self::getPostComments($question);

        // Add answers
        $answers = $this->posts->findAnswers($question->id); 
        
        foreach($answers as $answer) {
            self::getPostComments($answer);
        }
        
        $this->theme->setTitle($question->title);
        $this->views->add(
            'post/view', 
            [
                'post' => $question,
                'answers' => $answers,
                'answer_form' => $answer_form->getHTML()
            ]
        );
    }

    private function recieveAnswerForm($answer_form) {
        $status = $answer_form->check();
        if ($status === true) {
            date_default_timezone_set('UTC');
            $now = date('Y-m-d H:i:s');
            $userId = $this->UsersController->getCurrentUser()->id;
            $parentId = $answer_form->value('parentId');
            $newPost = [
                'user_id' => $userId,
                'body' => $answer_form->value('body'),
                'created' => $now,
                'parent' => $parentId,
            ];

            if($this->posts->create($newPost)){
                $this->FlashMessages->addSuccess("Svaret är skickat!");
                $this->response->redirect($this->url->create("posts/view/{$parentId}"));
            }
        }
    }

    private function recieveCreateForm($form) {
        $status = $form->check();

        if ($status === true) {
            $now = date('Y-m-d H:i:s');
            $userId = $this->UsersController->getCurrentUser()->id;
            $newPost = [
                'user_id' => $userId,
                'title' => $form->value('title'),
                'body' => $form->value('body'),
                'created' => $now,
            ];

            if($this->posts->save($newPost)){
                $tags = explode(',', $form->value('tags'));
                $this->TagsController->addTagsToPost($tags, $this->posts->id);

                $this->FlashMessages->addSuccess("Frågan skapades!");
                $this->response->redirect($this->url->create("posts/view/{$this->posts->id}"));
            } else {
                $this->FlashMessages->addError("Något gick fel, frågan sparades ej.");
            }

        }
    }

    /** 
     * get all the info for one post
     */
    private function getPostComments($post) {
        $commentClass = new \Anax\Comments\Comment();
        $commentClass->setDI($this->di);
        $allComments = [];
        foreach ($commentClass->findByPost($post->id) as $comment) {
            $allComments[] = $comment;
        }
        $post->comments = $allComments;

        return $post;
    }

    /**
     * Gets number of answers for a post
     */
    private function getNrOfAnswers($post) {
        $post->answerCount = $this->posts->findNrOfAnswers($post->id);
    }

    /**
	 * Creates a form to edit/create post 
	 * @return form, the CForm that was created
	 */
	private function createPostForm(){
		$form = $this->di->form->create([], [ 
            'title' => [ 
                'class'      => 'form-control',
                'label'      => 'Titel',
                'type'       => 'text', 
                'title'      => 'Titel', 
                'validation' => ['not_empty'] 
            ], 
            'body' => [ 
                'class'      => 'form-control',
                'type'       => 'textarea', 
                'label'      => 'Innehåll', 
                'title'      => 'Innehåll',
                'validation' => ['not_empty'] 
            ], 
            'tags' => [ 
                'class'      => 'form-control',
                'type'       => 'text', 
                'label'      => 'Taggar (Separera taggar med ett ",")', 
                'title'      => 'Taggar', 
                'validation' => ['not_empty'] 
            ], 
            'submit' => [ 
                'class'      => 'btn btn-primary',
                'type'       => 'submit', 
                'value'      => 'Skicka inlägg', 
                'callback'   => function ($form) { 
                    return true; 
                } 
            ] 
        ]); 
		return $form;
	}

    /**
     * Creates a form to edit/create post 
     * @return form, the CForm that was created
     */
    private function createAnswerForm($parentId){
        $form = $this->di->form->create([], [ 
            'body' => [ 
                'class'      => 'form-control',
                'value'      => null,
                'type'       => 'textarea', 
                'validation' => ['not_empty'] 
            ], 
            'parentId' => [
                'type'       => 'hidden',
                'value'      => $parentId,
                'validation' => ['not_empty'] 
            ], 
            'submit' => [ 
                'class'      => 'btn btn-primary',
                'type'       => 'submit', 
                'value'      => 'Skicka svar', 
                'callback'   => function ($form) { 
                    return true; 
                } 
            ] 
        ]); 
        return $form;
    }
}