<?php
use Slim\Middleware\TempEmailMiddleware;
use Slim\Middleware\IsAuthMiddleware;
use Slim\Middleware\IsGuestMiddleware;
use Slim\Middleware\IsAdminMiddleware;

$app->redirect('/', 'public/search', 301);

// Authorized users only
$app->group('', function() use ( $app ) {
    $app->post('/reset_username', controllers\UserController::class . ':reset_username');
    $app->post('/reset_password', controllers\UserController::class . ':reset_password');
    $app->delete('/delete/{id}', controllers\UserController::class . ':delete_user');
    $app->post('/reset_email', controllers\UserController::class . ':reset_email');
    $app->post('/set_new_pass', controllers\UserController::class . ':set_new_password');
    $app->get('/get_favorites', controllers\UserController::class . ':get_favorite');
    $app->delete('/delete_favorite/{id}', controllers\UserController::class . ':delete_favorite');
    $app->get('/logout', controllers\UserController::class . ':log_out');
    $app->post('/add_favorite', controllers\UserController::class . ':add_favorite');

})->add( new IsAuthMiddleware() );

//Checking temporal email
$app->group('', function() use ( $app ) {
    $app->get('/email_confirm', controllers\UserController::class. ':email_confirmation');
    $app->get('/resend', controllers\UserController::class . ':resend_email');
})->add( new TempEmailMiddleware() );

//Guests only
$app->group('', function() use ( $app ){
    $app->post('/login', controllers\UserController::class . ':log_in');
    $app->post('/signup', controllers\UserController::class . ':sign_up');
    $app->post('/forgot_password', controllers\UserController::class . ':forgot_password');
    $app->get('/auth_with_google', controllers\UserController::class . ':sign_up');
})->add( new IsGuestMiddleware() );

//Parsing
$app->post('/add-url', controllers\AdminController::class . ':add_url'); 
$app->get('/dump_web', models\Web_example::class . ':webLoaded'); 

//Search
$app->get('/lit-search/{word}', controllers\SearchController::class . ':get_lit');
$app->get('/web-search/{word}', controllers\SearchController::class . ':get_web');

//Pages rendering
$app->get('/{path}/{page}', controllers\SearchController::class . ':render');
