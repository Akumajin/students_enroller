<?php

use Slim\Http\Request;
use Slim\Http\Response;
// Routes

// Login page
$app->get('/login', function ($request, $response, $args) {
    return $this->view->render($response, 'login.html', [
        'page_title' => 'Login'
    ]);
})->setName('login');

$app->post('/login', function ($request, $response, $args) {
    $data = $request->getParsedBody();
    $user_data = [];
    $user_data['username'] = filter_var($data['lg_username'], FILTER_SANITIZE_STRING);
    $user_data['password'] = filter_var($data['lg_password'], FILTER_SANITIZE_STRING);
    
    $user = new User($this->db);
    $dd = $user->getUserByUsernameAndPassword($user_data['username'],$user_data['password']);
    /*
    if (!empty($user) and password_verify($user_data['password'], $user->password)){
        $_SESSION["id"] = $user->id;
        return json_encode((object) array('success'=>true, 'fullname' => $user->full_name ));
    };
    */
    return json_encode((object) array('success'=>false, 'fullname' => $dd));
});

// Signup page
$app->get('/signup', function ($request, $response, $args) {
    return $this->view->render($response, 'signup.html', [
        'page_title' => 'Signup'
    ]);
})->setName('login');

// Home page
$app->get('/', function ($request, $response, $args) {
    if(!$_SESSION){
		return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('login'));
	};
    return $this->view->render($response, 'index.html', [
        'page_title' => 'Dashboard'
    ]);
})->setName('home');