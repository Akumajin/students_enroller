<?php

use Slim\Http\Request;
use Slim\Http\Response;
// Routes

// Login page
$app->get('/login', function (Request $request, Response $response, array $args) {
    
    if($_SESSION){
		return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('home'));
	};
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
    if (!empty($result) and password_verify($user_data['password'], $result["password"])){
            $_SESSION["id"] = $result["id"];
            $_SESSION["username"] = $result["username"];
            $_SESSION["full_name"] = $result["full_name"];
            return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('home'));
    };
    return $this->view->render($response, 'login.html', [
        'page_title' => 'Login',
        'error' => 'Username or password was entered incorrectly.'
    ]);
});

// Signup page
$app->get('/signup', function ($request, $response, $args) {
    return $this->view->render($response, 'signup.html', [
        'page_title' => 'Signup'
    ]);
})->setName('signup');

$app->post('/signup', function ($request, $response, $args) {
    $data = $request->getParsedBody();
    $empty_fields = false;
    $user_data = [];
    $user_data['username'] = filter_var($data['sgn_username'], FILTER_SANITIZE_STRING);
    $user_data['fullname'] = filter_var($data['sgn_fullname'], FILTER_SANITIZE_STRING);
    $user_data['password'] = filter_var($data['sgn_password'], FILTER_SANITIZE_STRING);
    if (
        $user_data['username'] == "" or
        $user_data['fullname'] == "" or
        $user_data['password'] == "") $empty_fields = true;
    if ($empty_fields) {
        return $this->view->render($response, 'signup.html', [
            'page_title' => 'Signup-with',
            'empty_fields' => $empty_fields
        ]);
    };
    $user = new User($this->db);
    $result = $user->insertUser($user_data);
    if ($result == true){
        $result = $user->getUserByUsername($user_data['username']);
        if (!empty($result)){
                $_SESSION["id"] = $result["id"];
                $_SESSION["username"] = $result["username"];
                $_SESSION["full_name"] = $result["full_name"];
                return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('home'));
        };
    };
    return $this->view->render($response, 'signup.html', [
        'page_title' => 'Signup',
        'errors' => $empty_fields
    ]);
});

// Logout
$app->get('/logout', function ($request, $response, $args) {
    session_destroy();
	return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('login'));
});

// Reports page
$app->get('/reports', function ($request, $response, $args) {
    if(!$_SESSION){
		return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('login'));
	};
    return $this->view->render($response, 'reports.html', [
        'page_title' => 'Reports',
        'full_name' => $_SESSION['full_name']
    ]);
})->setName('reports');

// Enroll page
$app->get('/', function ($request, $response, $args) {
    if(!$_SESSION){
		return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('login'));
	};
    return $this->view->render($response, 'index.html', [
        'page_title' => 'Enroll',
        'full_name' => $_SESSION['full_name']
    ]);
})->setName('home');