<?php


namespace Anax\Users;
 
/**
 * A controller for users and admin related events.
 *
 */
class UsersController implements \Anax\DI\IInjectionAware{
    use \Anax\DI\TInjectable;
 	
 	private $currentUser;
 	private $currentUserKey = "currentUser";

	/**
	 * Initialize the controller.
	 * @return void
	 */
	public function initialize(){
	    $this->users = new \Anax\Users\User();
	    $this->users->setDI($this->di);
	    $this->currentUser = self::getLoggedInUser();
	}

	/**
	 * Gets the logged in user from the db
	 */
	public function getLoggedInUser() {
		$this->users = new \Anax\Users\User();
	    $this->users->setDI($this->di);

		$user = $this->users->find($this->session->get($this->currentUserKey, []));
		if($user != null) {
			return $user;
		}
		return null;
	}

	/**
	 * Checks whether the user is logged in
	 * @return boolean
	 */
	public function isLoggedIn() {
		if($this->session->has($this->currentUserKey)) {
			return true;
		}
		return false;
	}

	public function requireLogin($msg = "Du måste vara inloggad för att nå denna funktion") {
		if(!self::isLoggedIn()) {
			$this->FlashMessages->addWarning($msg);
			$this->response->redirect($this->url->create("users/login"));
		}
	}

	public function indexAction() {
		$users = $this->users->findAll();
		$title = "Alla användare";
		$this->theme->setTitle($title);
		$this->views->add(
	        'user/all', 
	        [
	            'title' => $title,
	            'users' => $users,
	        ]
	    );
	}

	/**
	 * Action for login
	 * @return [type] [description]
	 */
	public function loginAction() {

		$form = self::createLoginForm();
		$status = $form->check();

	    if ($status === true) {
	    	$email = $form->value('email');
	    	$password = $form->value('password');

		    $user = $this->users->findByEmail($email);
		    if ($user != null) {
		    	if (!password_verify($password, $user->password)) {
				    $this->FlashMessages->addError("Fel lösenord.");
				} else {
					$this->session->set($this->currentUserKey, $user->id);
					$this->FlashMessages->addSuccess("Välkommen {$user->name}!");
					$this->response->redirect($this->url->create("users/profile/{$user->id}"));
				}
		    } else {
		    	$this->FlashMessages->addError("E-post är ej registrerad.");
		    }
	    }

	    if(self::isLoggedIn()) {
			$this->FlashMessages->addInfo("Du är redan inloggad!");
			$this->response->redirect($this->url->create("users/profile/".self::getCurrentUser()->id));
		}

	    $this->theme->setTitle("Logga in");
	    $this->views->add(
	        'default/page', 
	        [
	            'title' => "Logga in",
	            'content' => $form->getHTML(),
	        ]
	    );
	}

	/**
	 * Logs out the user
	 */
	public function logoutAction() {
		$this->session->set($this->currentUserKey, null);
		$this->FlashMessages->addInfo("Du är nu utloggad. Välkommen åter!");
		$url = $this->url->create("");
		$this->response->redirect($url);
	}

	/**
	 * Gets the current user
	 * @return [type] [description]
	 */
	public function getCurrentUser() {
		return $this->currentUser;
	}

	/**
	 * Presents a users profile
	 * @param int $id of user to display
	 */
	public function profileAction($id = -1){
	    $user = $this->users->find($id);

	    if($user == null) { 
	    	$this->FlashMessages->addError("Could not find user");
	    	$url = $this->url->create("");
			$this->response->redirect($url);
	    }

	    $postClass = new \Anax\Posts\Post();
	    $postClass->setDI($this->di);
	    $questions = $postClass->findQuestionsByUser($id, 5, "created");
	    $answers = $postClass->findAnswersByUser($id, 5);

	    $commentClass = new \Anax\Comments\Comment();
        $commentClass->setDI($this->di);
	    $comments = $commentClass->findCommentsByUser($id, 5, "created");
	 
	    $this->theme->setTitle($user->name . " profil");
	    $this->views->add('user/profile', [
	        'user' => $user,
	        'questions' => $questions,
	        'answers' => $answers,
	        'comments' => $comments
	    ]);
	}


	/**
	 * Add new user.
	 *
	 * @return void
	 */
	public function registerAction(){

		$this->users = new \Anax\Users\User();
	    $this->users->setDI($this->di);

		$form = self::createRegisterForm();
		$status = $form->check();

	    if ($status === true) {
	    	date_default_timezone_set('UTC');
	    	$now = date('Y-m-d H:i:s');
		 	$newUser = [
		        'email' => $form->value('email'),
		        'name' => $form->value('name'),
		        'password' => password_hash($form->value('password'), PASSWORD_DEFAULT),
		        'created' => $now,
		    ];

		    $response = $this->users->save($newUser);
		    if($response == 1){
		    	$this->FlashMessages->addSuccess("Registreringen lyckades! Vänligen logga in nedan.");
		 		$url = $this->url->create("users/login");
		    	$this->response->redirect($url);
		    } else {
		    	$this->FlashMessages->addError("Registreringen misslyckades. Försök igen.");
		    }
	    }

	    $this->theme->setTitle("Register user");
	    $this->views->add(
	        'default/page', 
	        [
	            'title' => "Registrera användare",
	            'content' => $form->getHTML(),
	        ]
	    );
	}

	/**
	 * Edits a user
	 * @return void
	 */
	public function editAction(){

		self::requireLogin("Du måste logga in för att redigera din användare");

	    $form = self::createEditForm($this->currentUser);

		$status = $form->check();

	    if ($status === true) {
	  	    $now = date('Y-m-d H:i:s');
		 	$newUser = [
		        'email' => $form->value('email'),
		        'profile' => $form->value('profile'),
		        'name' => $form->value('name'),
		    ];

		    if($this->users->save($newUser)){
		    	$this->FlashMessages->addSuccess("Ändringar sparade");
		    }
	    }

	    $title = "Redigera användare";
	    $this->theme->setTitle($title);
	    $links = self::getEditLinks();
	    $links[0]["active"] = true;
	    $this->views->add(
	        'default/page', 
	        [
	            'title' => $title. ": " .$this->currentUser->name,
	            'content' => $form->getHTML(),
	            'links' => $links
	        ]
	    );
	}

	/**
	 * Edits a user
	 * @param integer $id of user to edit.
	 * @return void
	 */
	public function changepasswordAction(){

		self::requireLogin("Du måste logga in för att redigera din användare");
	    
	    $form = self::createChangePasswordForm();
		$status = $form->check();

	    if ($status === true) {
	    	$oldPassword = $form->value("oldPassword");
	    	if (!password_verify($oldPassword, $this->currentUser->password)) {
			    $this->FlashMessages->addError("Du angav fel nuvarande lösenord.");
			} else {
			 	$newUser = [
			        "password" => password_hash($form->value("newPassword"), PASSWORD_DEFAULT),
			    ];
			    if($this->users->save($newUser)){
			    	$this->FlashMessages->addSuccess("Ditt nya lösenord är sparat!");
			    }
			}
	    }

	    $title = "Ändra lösenord";
	    $this->theme->setTitle($title);
	    $links = self::getEditLinks();
	    $links[1]["active"] = true;
	    $this->views->add(
	        'default/page', 
	        [
	            'title' => $title,
	            'content' => $form->getHTML(),
	            'links' => $links
	        ]
	    );
	}

	private function getEditLinks() {
		return [
			[
		        "href" => "users/edit",
		        "text" => "Redigera inställningar",
		        "active" => null
		    ],
		    [
		        "href" => "users/changepassword",
		        "text" => "Byt lösenord",
		        "active" => null
		    ]
		];
	}

	/**
	 * Creates a login user form 
	 * @return form, the CForm that was created
	 */
	private function createLoginForm(){		
		$form = $this->di->form->create([], [ 
            'email' => [ 
            	'class'		 => 'form-control',
                'type'       => 'email', 
                'label'      => 'E-post', 
                'validation' => ['not_empty', 'email_adress'] 
            ], 
            'password' => [ 
            	'class'		 => 'form-control',
                'type'       => 'password', 
                'label'      => 'Lösenord' 
            ], 
            'submit' => [ 
                'type'       => 'submit', 
                'value'      => 'Logga in', 
                'class'		 => 'btn btn-primary pull-left',
                'callback'   => function ($form) { 
                    return true; 
                } 
            ] 
        ]); 
		return $form;
	}

	/**
	 * Creates a register user form
	 * 
	 * @param  $user_details, user array if editing
	 * 
	 * @return form, the CForm that was created
	 */
	private function createRegisterForm(){
		
		$submit_text = "Registrera";

		$form = $this->di->form->create([], [ 
            'name' => [ 
            	'class'		 => 'form-control',
                'type'       => 'text', 
                'label'      => 'Namn', 
                'validation' => ['not_empty'] 
            ], 
            'email' => [ 
            	'class'		 => 'form-control',
                'type'       => 'email', 
                'label'      => 'E-post', 
                'validation' => ['not_empty', 'email_adress'] 
            ], 
            'password' => [ 
            	'class'		 => 'form-control',
                'type'       => 'password', 
                'label'      => 'Lösenord' 
            ], 
            'submit' => [ 
                'type'       => 'submit', 
                'value'      => 'Registrera', 
                'class'		 => 'btn btn-primary pull-left',
                'callback'   => function ($form) { 
                    return true; 
                } 
            ] 
        ]); 
		return $form;
	}

	/**
	 * Creates a register user form
	 * 
	 * @param  $user_details, user array if editing
	 * 
	 * @return form, the CForm that was created
	 */
	private function createEditForm($user){

		$form = $this->di->form->create([], [ 
            'name' => [ 
            	'class'		 => 'form-control',
                'type'       => 'text', 
                'label'      => 'Namn', 
                'value'      => $user->name, 
                'validation' => ['not_empty'] 
            ], 
            'email' => [ 
            	'class'		 => 'form-control',
                'type'       => 'email', 
                'label'      => 'E-post', 
                'value'      => $user->email, 
                'validation' => ['not_empty', 'email_adress'] 
            ],
            'profile' => [ 
            	'class'		 => 'form-control',
                'type'       => 'textarea', 
                'label'      => 'Presentation', 
                'value'      => $user->profile
            ],
            'submit' => [ 
            	'class'		 => 'btn btn-primary pull-left',
                'type'       => 'submit', 
                'value'      => 'Spara ändringar', 
                'callback'   => function ($form) { 
                    return true; 
                } 
            ] 
        ]); 
		return $form;
	}

	/**
	 * Creates a register user form
	 * 
	 * @param  $user_details, user array if editing
	 * 
	 * @return form, the CForm that was created
	 */
	private function createChangePasswordForm(){
		
		$submit_text = "Registrera";

		$form = $this->di->form->create([], [ 
            'newPassword' => [ 
            	'class'		 => 'form-control',
                'type'       => 'password', 
                'label'      => 'Nytt lösenord',
                'validation' => ['not_empty'] 
            ],
            'oldPassword' => [ 
            	'class'		 => 'form-control',
                'type'       => 'password', 
                'label'      => 'Gammalt lösenord',
                'validation' => ['not_empty'] 
            ], 
            'submit' => [ 
                'type'       => 'submit', 
                'value'      => 'Spara nytt lösenord', 
                'class'		 => 'btn btn-primary pull-left',
                'callback'   => function ($form) { 
                    return true; 
                } 
            ] 
        ]); 
		return $form;
	}
}