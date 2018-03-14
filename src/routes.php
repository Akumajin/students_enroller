<?php

use Slim\Http\Request;
use Slim\Http\Response;
// Routes

// Login page
$app->get('/login', function (Request $request, Response $response, array $args) {
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
    $result = $user->getUserByUsername($user_data['username']);
    if (!empty($result)){
        if (password_verify($user_data['password'], $result->password)){
            $_SESSION["id"] = $result->id;
            return json_encode((object) array('success'=>true, 'fullname' => $result->full_name ));
        }
    };
    return json_encode((object) array('success'=>false, 'fullname' => ""));
});

// Signup page
$app->get('/signup', function ($request, $response, $args) {
    return $this->view->render($response, 'signup.html', [
        'page_title' => 'Signup'
    ]);
})->setName('signup');

$app->post('/signup', function ($request, $response, $args) {
    $data = $request->getParsedBody();
    $errors = array('invalid_username' => false, 'invalid_fullname' => false, 'invalid_password' => false);
    $user_data = [];
    $user_data['username'] = filter_var($data['sgn_username'], FILTER_SANITIZE_STRING);
    $user_data['fullname'] = filter_var($data['sgn_fullname'], FILTER_SANITIZE_STRING);
    $user_data['password'] = filter_var($data['sgn_password'], FILTER_SANITIZE_STRING);
    if ($user_data['username'] == "") $errors["invalid_username"] = true;
    if ($user_data['fullname'] == "") $errors["invalid_fullname"] = true;
    if ($user_data['password'] == "") $errors["invalid_fullname"] = true;
    if ($errors["invalid_username"] == true or $errors["invalid_fullname"] == true or $errors["invalid_fullname"] == true) {
        return $this->view->render($response, 'signup.html', [
            'page_title' => 'Signup-with',
            'errors' => $errors
        ]);
    };
    $user = new User($this->db);
    $result = $user->insertUser($user_data);
    //if ($result != false)){
        //$_SESSION["id"] = $result->id;
        //return json_encode((object) array('success'=>true, 'fullname' => $result->full_name ));
    //};
    return $this->view->render($response, 'signup.html', [
        'page_title' => 'Signup',
        'errors' => $errors
    ]);
});

// Home page
$app->get('/', function ($request, $response, $args) {
    if(!$_SESSION){
		return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('login'));
	};
    return $this->view->render($response, 'index.html', [
        'page_title' => 'Dashboard'
    ]);
})->setName('home');