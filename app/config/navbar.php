<?php
/**
 * Config-file for navigation bar.
 */
$navbar = [

    // Use for styling the menu
    'class' => 'collapse navbar-collapse',
 
    // Here comes the menu strcture
    'items' => [
        
     // Home menu item
        'Hem' => [
            'text'  => '<i class="fa fa-home fa-fw"></i> Hem', 
            'url'   => '',  
            'title' => 'Hem'
        ],
    
    // Posts menu item
        'posts' => [
            'text'  => 'Frågor', 
            'url'   => 'posts',  
            'title' => 'Inlägg'
        ],

        // Tags menu item
        'tags' => [
            'text'  => 'Taggar', 
            'url'   => 'tags',  
            'title' => 'Taggar'
        ],

        // Tags menu item
        'users' => [
            'text'  => 'Användare', 
            'url'   => 'users',  
            'title' => 'Användare'
        ],

        // About menu item
        'about' => [
            'text'  => 'Om', 
            'url'   => 'om',  
            'title' => 'Om sidan'
        ],
    ],
 
    // Callback tracing the current selected menu item base on scriptname
    'callback' => function($url) {
        if ($url == $this->di->get('request')->getRoute()) {
            return true;
        }
    },

    // Callback to create the urls
    'create_url' => function($url) {
        return $this->di->get('url')->create($url);
    },
];

if(!$this->di->UsersController->isLoggedIn()) {
    $navbar["items"]["login"] = 
        [
            'text'  => 'Logga in', 
            'url'   => 'users/login',  
            'title' => 'Logga in'
        ];
    $navbar["items"]["register"] = 
        [
            'text'  => 'Registrera', 
            'url'   => 'users/register',  
            'title' => 'Registrera'
        ];
} else {

    $img_url = "http://www.gravatar.com/avatar/" . md5(strtolower(trim($this->di->UsersController->getCurrentUser()->email))) . ".jpg?s=18";
    $navbar["items"]["user"] = 
        [
            'text'  => '<img src="'.$img_url.'" class="img-circle" width="18" /> '. $this->di->UsersController->getCurrentUser()->name, 
            'url'   => '#',  
            'title' => '',

            // Here we add the submenu, with some menu items, as part of a existing menu item
            'submenu' => [

                'items' => [

                    // This is a menu item of the submenu
                    'profil'  => [
                        'isSub' => true,
                        'text'  => 'Profil',   
                        'url'   => 'users/profile/' . $this->di->UsersController->getCurrentUser()->id,  
                        'title' => 'Profl',
                    ],

                    // This is a menu item of the submenu
                    'settings'  => [
                        'isSub' => true,
                        'text'  => 'Redigera inställningar',   
                        'url'   => 'users/edit',  
                        'title' => 'Redigera inställningare',
                    ],
                    // This is a menu item of the submenu
                    'logout'  => [
                        'isSub' => true,
                        'text'  => '<i class="fa fa-sign-out fa-fw"></i> Logga ut',   
                        'url'   => 'users/logout',  
                        'title' => 'Logga ut',
                    ],
                ],
            ],
        ];
}

return $navbar;