<?php
// Server requests
$app->get('/logout', controllers\UserController::class . ':log_out');
$app->delete('/delete/{id}', controllers\UserController::class . ':delete_user');
$app->get('/email_confirm', controllers\UserController::class. ':email_confirmation');
$app->get('/auth_with_google', controllers\UserController::class . ':sign_up')->add( $middleware );
$app->post('/signup', controllers\UserController::class . ':sign_up')->add( $middleware );
$app->post('/login', controllers\UserController::class . ':log_in')->add( $middleware );
$app->post('/reset_username', controllers\UserController::class . ':reset_username');
$app->post('/reset_password', controllers\UserController::class . ':reset_password');
$app->post('/reset_email', controllers\UserController::class . ':reset_email');
$app->get('/resend', controllers\UserController::class . ':resend_email');
$app->post('/forgot_password', controllers\UserController::class . ':forgot_password');
$app->post('/set_new_pass', controllers\UserController::class . ':set_new_password');

//Views rendering
$app->get('/{page}', controllers\HomeController::class . ':render');
$app->post('/dump', models\Book::class . ':booksLoaded'); 
$app->get('/lit-search/{word}', controllers\SearchController::class . ':get_lit');
$app->get('/web-search/{word}', controllers\SearchController::class . ':get_web');


