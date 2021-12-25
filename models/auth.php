<?php

use \Delight\Auth\Auth as AuthLib;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Auth extends Model
{
    public $auth;
    public function __construct() {
        parent::__construct();

        $this->auth = new AuthLib( $this->db->connect() );
    }

    public function register( Request $request, Response $response, $args ) : ResponseInterface {
        try {
            $userId = $this->auth->register( $_POST['email'], $_POST['password'], $_POST['username'] );
            echo 'We have signed up a new user with the ID ' . $userId;
        } catch(\Delight\Auth\InvalidEmailException $e) {
            exit('Invalid email');
        } catch(\Delight\Auth\InvalidPasswordException $e) {
            exit('Invalid password');
        } catch(\Delight\Auth\UserAlreadyExistsException $e) {
            exit('User already exists');
        } catch(\Delight\Auth\TooManyRequestsException $e) {
            exit('Too many requests');
        }
        return $response;
    }

    public function log_in( Request $request, Response $response, $args ) : ResponseInterface {
        if ($this->auth->isLoggedIn()) {
            echo 'User is signed in';
            exit();
        }
        else {
        try {
            $this->auth->loginWithUsername( $_POST['username'], $_POST['password']);
        } catch(\Delight\Auth\UnknownUsernameException $e) {
            echo 'Unknown username';
        } catch(\Delight\Auth\InvalidPasswordException $e) {
            echo 'Wrong password';
        } catch(\Delight\Auth\EmailNotVerifiedException $e) {
            echo 'Verify your email please!';
        } catch(\Delight\Auth\TooManyRequestsException $e) {
            echo 'Too many requests';
        }
        return $response;
    }
    }
}