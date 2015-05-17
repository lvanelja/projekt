<?php

require __DIR__.'/config_with_app.php'; 

// Sets default timezone
date_default_timezone_set('CET');

//Sessionen på
$app->session();

/* Enable database */
$di->setShared('db', function() { 
    $db = new \Mos\Database\CDatabaseBasic(); 
    $db->setOptions(require ANAX_APP_PATH . 'config/database_mysql.php'); 
    $db->connect(); 
    return $db; 
}); 

/* Enable Users-controller */
$di->setShared("UsersController", function() use ($di) { 
    $userController = new \Anax\Users\UsersController(); 
    $userController->setDI($di); 
    return $userController; 
});
$app->UsersController->initialize();

/* Enable forms */
$di->set('form', '\Mos\HTMLForm\CForm'); 
$di->set('FormController', function () use ($di) {
    $controller = new \Anax\HTMLForm\FormController();
    $controller->setDI($di);
    return $controller;
});

/* Enable Posts-controller */
$di->setShared('PostsController', function() use ($di) { 
    $controller = new \Anax\Posts\PostsController(); 
    $controller->setDI($di); 
    return $controller; 
}); 

/* Enable Comments-controller */
$di->setShared('CommentsController', function() use ($di) { 
    $controller = new \Anax\Comments\CommentsController(); 
    $controller->setDI($di); 
    return $controller; 
}); 

/* Enable Votes-controller */
$di->setShared('VotesController', function() use ($di) { 
    $controller = new \Anax\Votes\VotesController(); 
    $controller->setDI($di); 
    return $controller; 
});

/* Enable Tags-controller */
$di->setShared('TagsController', function() use ($di) { 
    $controller = new \Anax\Tags\TagsController(); 
    $controller->setDI($di); 
    return $controller; 
});

/* Enable Tags-controller */
$di->setShared('Post_TagsController', function() use ($di) { 
    $controller = new \Anax\Post_Tags\Post_TagsController(); 
    $controller->setDI($di); 
    return $controller; 
});

/* Enable flashmessages */
$di->setShared('FlashMessages', function() use ($di){
    $FlashMessages = new \PBjuhr\FlashMessages\FlashMessages_ANAX($di, "FlashMessages");
    return $FlashMessages;
});

// Snyggare url:er
$app->url->setUrlType(\Anax\Url\CUrl::URL_CLEAN);

//Skapa min egen config-fil för temat
$app->theme->configure(ANAX_APP_PATH . 'config/theme.php');

//Byt config-fil för navbaren
$app->navbar->configure(ANAX_APP_PATH . 'config/navbar.php');

$app->router->add('', function() use ($app, $di) {
	
    $app->theme->setTitle("Start");

    $posts = new \Anax\Posts\Post();
    $posts->setDI($di);
    $posts = $posts->findLatest(10);

    $tags = new \Anax\Tags\Tag();
    $tags->setDI($di);
    $tags = $tags->findWithPostCount("postCount", 10);

    $users = new \Anax\Users\User();
    $users->setDI($di);
    $users = $users->findActive();
 
    $app->views->add(
        'default/frontpage', 
        [
            'title'   => 'Welcome!',
            'welcomeMessage' => $app->fileContent->get('welcome.html'),
            'posts' => $posts,
            'tags' => $tags,
            'users' => $users,
        ]
    );
});


$app->router->add('om', function() use ($app) {
    
    $title = "Om sidan";
    $app->theme->setTitle($title);
 
    $content = $app->fileContent->get('om.html');

    $app->views->add(
        'default/page', 
        [
            'title'   => $title,
            'content' => $content,
        ]
    );

});
 

$app->router->add('setup', function() use ($app) {
    require ANAX_APP_PATH . 'config/database_setup.php'; 
    $app->FlashMessages->addInfo("Database setup completed.");
    $app->response->redirect($app->url->create(''));
});

$app->router->handle();

$app->views->add(
    'error/flashmessages',
    [
        "messages" => $app->FlashMessages->findAll()
    ]
    , 'flashmessages'
);
$app->FlashMessages->clean();

$app->theme->render();