<?php

require_once 'vendor/autoload.php';

$app = new Silex\Application();

$app->get('/songs', function() use ($app) {
    $result = (object) ["title" => "Some  song", "artist" => "Some artist"];
    return $app->json([$result, $result]);
});

$app->get('/songs/{songId}', function($songId) use ($app) {
    $result = (object) ["title" => "Some $songId song", "artist" => "Some other artist"];
    return $app->json($result);
});

$app->post('/songs/{songId}', function($songId) use ($app) {
    return "Posted";
});

$app->run();
