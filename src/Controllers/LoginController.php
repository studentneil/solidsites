<?php

namespace Solidsites\Controllers;

use Silex\Application;
use Solidsites\Models\Post;
use Symfony\Component\HttpFoundation\Request;

class LoginController
{
    public function loginAction(Request $request, Application $app)
    {
        $args_array = array(
            'error' => $app['security.last_error']($request),
            'last_username' => $app['session']->get('_security.last_username'),
        );
        return $app['twig']->render('frontend/login.html.twig', $args_array);
    }
    public function dashboardAction(Application $app)
    {
        $user = $app['security.token_storage']->getToken()->getUser()->getUsername();
        $app['session']->set('user', array('username' => $user));
        $posts = Post::where('author', '=', 'niall')->get();
        $templateName = 'backend/dashboard';
        $args_array = array(
            'user' => $app['session']->get('user'),
            'posts' => $posts

        );
        return $app['twig']->render($templateName.'.html.twig', $args_array);
    }
}
