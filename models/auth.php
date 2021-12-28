<?php

use \Delight\Auth\Auth as AuthLib;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Google\Client;
use GoogleAuthController;

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
        
        return $response->withStatus(200);
    }

    public function delete_user( RequestInterface $request, ResponseInterface $response, $args ) : ResponseInterface {
        try {
            $this->auth->admin()->deleteUserById($args['id']);
            $this->auth->destroySession();
        }
        catch (\Delight\Auth\UnknownIdException $e) {
            die('Unknown ID');
        }
        //$response->getBody()->write('ok');
        return $response;
    }

    public function email_confirmation( RequestInterface $request, ResponseInterface $response, $args ) : ResponseInterface {
        try {
            $this->auth->confirmEmail( $_GET['selector'], $_GET['token'] );

            echo 'Email verified';
        } catch (\Delight\Auth\InvalidSelectorTokenPairException $e) {
            exit('Invalid token');
        }
        catch (\Delight\Auth\TokenExpiredException $e) {
            exit('Token expired');
        }
        catch (\Delight\Auth\UserAlreadyExistsException $e) {
            exit('Email address already exists');
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            exit('Too many requests');
        }
        return $response;
    } 

    public function reset_password( RequestInterface $request, ResponseInterface $response, $args ) : ResponseInterface {
        return $response;
    }
    public function auth_with_google( RequestInterface $request, ResponseInterface $response, $args ) : ResponseInterface {
        $client = new Client();
        $client->setClientId( $this->config['google']['clientID'] );
        $client->setClientSecret( $this->config['google']['clientSecret'] );
        $client->setRedirectUri( $this->config['google']['redirectUri'] );

        $client->addScope( "email" );
        $client->addScope( "profile" );

        header('Location:' . $client->createAuthUrl());
        exit();
        
        //return $response;
    }
    public function proceed_google( RequestInterface $request, ResponseInterface $response, $args ) : ResponseInterface {
        $client = new Client();

        if( isset($_GET['code']) ) {
            $token = $client->fetchAccessTokenWithAuthCode( $_GET['code'] );
            $client->setAccessToken( $token['access_token'] );

            $google_oauth = new GoogleAuthController();
            $google_account_info = $google_oauth->userinfo->get();
            $email = $google_account_info->email;
            $username = $google_account_info->username;
        }

        debug( $email, $username );
        return $response;
        
    }
}
