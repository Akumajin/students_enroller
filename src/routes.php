<?php

use Slim\Http\Request;
use Slim\Http\Response;
// Routes

// Setup database
$app->get('/setup', function (Request $request, Response $response, array $args) {
    $database = new Database($this->db);
    $database->createDbIfNotExist();
    return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('login'));
});

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
    $is_duplicated = false;
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
    if ($result == "duplicate_entry"){
        $is_duplicated = true;
    } else if ($result == "success"){
        $fetched_user = $user->getUserByUsername($user_data['username']);
        if (!empty($fetched_user)){
            $_SESSION["id"] = $fetched_user["id"];
            $_SESSION["username"] = $fetched_user["username"];
            $_SESSION["full_name"] = $fetched_user["full_name"];
            return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('home'));
        };
    }
    return $this->view->render($response, 'signup.html', [
        'page_title' => 'Signup',
        'errors' => $empty_fields,
        'id_duplicated' => $is_duplicated
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
    $unit = new Unit($this->db);
    $result = $unit->getUserUnits($_SESSION['id']);
    $total_credits = 0;
    foreach ($result as $value) $total_credits += $value['credits'];
    return $this->view->render($response, 'reports.html', [
        'page_title' => 'Reports',
        'full_name' => $_SESSION['full_name'],
        'units' => $result,
        'credits' => $total_credits
    ]);
})->setName('reports');

// Enroll page
$app->get('/', function ($request, $response, $args) {
    if(!$_SESSION){
		return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('login'));
    };
    $_SESSION['_token'] = bin2hex(openssl_random_pseudo_bytes(16));
    $unit = new Unit($this->db);
    $result = $unit->getAllUnitsByUser($_SESSION['id']);
    
    return $this->view->render($response, 'index.html', [
        'page_title' => 'Enroll',
        'full_name' => $_SESSION['full_name'],
        'token' => $_SESSION['_token'],
        'units' => $result
    ]);
})->setName('home');

// Enroll To Unit From

$app->post('/enroll', function ($request, $response, $args) {
    $data = $request->getParsedBody();
    $target_unit = $data['unit_id'];
    $token = $data['token'];
    if ($token !== $_SESSION['_token']){
        return 'bad token';
    }
    $unit = new Unit($this->db);
    $result = $unit->enrollUser($_SESSION['id'], $target_unit);
    return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('home'));
});

// Leave Unit From

$app->post('/leave', function ($request, $response, $args) {
    $data = $request->getParsedBody();
    $target_unit = $data['unit_id'];
    $token = $data['token'];
    if ($token !== $_SESSION['_token']){
        return 'bad token';
    }
    $unit = new Unit($this->db);
    $result = $unit->cancelUser($_SESSION['id'], $target_unit);
    return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('home'));
});