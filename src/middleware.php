<?php
// Application middleware
// e.g: $app->add(new \Slim\Csrf\Guard);

$database = new Database($container->db);
$database->createDbIfNotExist();