<?php

use \Delight\Auth\Auth as AuthLib;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Auth extends Model
{
    public $auth;
    public function __construct() {
        parent::__construct();

        $this->auth = new AuthLib( $this->db->connect(),null,null,false );
    }

    public function register( RequestInterface $request, ResponseInterface $response, $args ) : ResponseInterface {
        try {
            //$selector = rand(1000, 8888);
            //$token = str_shuffle( $_POST['username']);
            $userId = $this->auth->register( $_POST['email'], $_POST['password'], $_POST['username'], function( $selector, $token ){
                $to = '<' . $_POST['email'] . '>';
                $message = 'Dear ' . $_POST['username'] . "\r\n".
                'You have recently registered at Wordy, please confirm your password: ' . "\r\n" . 
                'http:\\localhost\wordy\email_confirm?selector=' . $selector . '&token=' . $token . "\r\n" 
                . 'Thank you for choosing Wordy !' . "\r\n";
                $header = 'From:<example@gmail.com>' . "\r\n" 
                . 'Reply-to: <example@gmail.com>';

                $send = mail( $to, 'Please confirm your registration on Wordy', $message, $header);
                if ($send) {
                    var_dump($send);
                    echo 'Confirm your email please';
                }

            });
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

    public function log_in( RequestInterface $request, ResponseInterface $response, $args ) : ResponseInterface {
        if ($this->auth->isLoggedIn()) {
            echo 'User is signed in';
            exit();
        }
        else {
        try {
            //Remebering the user
            if( !empty($_POST['remember']) && $_POST['remember'] == 1 ){
                $rememberDuration = (int) (60*60*24*365);
            } else {
                $rememberDuration = null;
            }
            //Log the user in
            $this->auth->loginWithUsername( $_POST['username'], $_POST['password'], $rememberDuration);  
        } catch(\Delight\Auth\UnknownUsernameException $e) {
            echo 'Unknown username';
        } catch(\Delight\Auth\InvalidPasswordException $e) {
            echo 'Wrong password';
        } catch(\Delight\Auth\EmailNotVerifiedException $e) {
            echo 'Verify your email please!';
        } catch(\Delight\Auth\TooManyRequestsException $e) {
            echo 'Too many requests';
        }
        header('Location:' . 'http://localhost/wordy/search');
        return $response;
    }
    }

    public function logout( RequestInterface $request, ResponseInterface $response, $args ) : ResponseInterface {
        
        try {
            $this->auth->logOutEverywhere();
            echo 'Logged out';
        } catch( \Delight\Auth\NotLoggedInException $e ){
            exit('Not logged in');
        }
        return $response;
    }

    public function delete_user( RequestInterface $request, ResponseInterface $response, $args ) : ResponseInterface {
        try {
            $this->auth->admin()->deleteUserById($args['id']);
            $auth->destroySession();
        }
        catch (\Delight\Auth\UnknownIdException $e) {
            die('Unknown ID');
        }
        //$response->getBody()->write('ok');
        return $response;
    }
}

//Return a page