<?php
namespace models;

use \Delight\Auth\Auth as AuthLib;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Google\Client;
use Google\Service\Oauth2 as ServiceOauth2;

class User extends Model
{
    public $auth;
    public function __construct() {
        parent::__construct();
        //Auth package
        $this->auth = new AuthLib( $this->db->connect(),null,null,false );
    }

    public function register( RequestInterface $request, ResponseInterface $response, $args ) : ResponseInterface {
        try {
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
                    echo 'Confirm your email please';
                }
                

            });
            //Checking if there are any warnings
            if( !empty( $args['message'] )){
                echo $args['message'];
            }
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
        return $response;
    }

    public function delete_user( RequestInterface $request, ResponseInterface $response, $args ) : ResponseInterface {
        try {
            $this->auth->admin()->deleteUserById($args['id']);
            $this->auth->destroySession();
        }
        catch (\Delight\Auth\UnknownIdException $e) {
            die('Unknown ID');
        }
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

    //Authentication with Google Account
    public function auth_with_google( RequestInterface $request, ResponseInterface $response, $args ) : ResponseInterface {
        $client = new Client();
        $client->setClientId( $this->config['google']['clientID'] );
        $client->setClientSecret( $this->config['google']['clientSecret'] );
        $client->setRedirectUri( $this->config['google']['redirectUri'] );
        $client->addScope( "email" );
        $client->addScope( "profile" );

        if( isset( $_GET['code'] ) ){
            $token = $client->fetchAccessTokenWithAuthCode( $_GET['code'] );

            $client->setAccessToken( $token['access_token'] );
            $google_oauth = new ServiceOauth2($client);
            $google_account_info = $google_oauth->userinfo->get();
            $email = $google_account_info->email;
            $name = $google_account_info->name;
            //Storing data in POST in order to create a user
            $_POST['username'] = $name;
            $_POST['email'] = $email;
            $_POST['password'] = $email;
            //Warning the user
            $args['message'] = 'Your password is ' . $email . ' ! Please make sure to change it';
            $this->register( $request, $response, $args);
        } else {
            header('Location:' . $client->createAuthUrl());
            exit();
        }
        return $response;
    }

    public function reset_username( RequestInterface $request, ResponseInterface $response, $args ) : ResponseInterface {
        if( !empty($_POST['new_username'])&& strlen($_POST['new_username'])>7 ){
            //Check that username is unique
            if( empty($this->id(['username' => $_POST['new_username']]))){
                //debug( $this->id( ['username' => $_POST['new_username']]));
                $userId = $this->auth->getUserId();
                //Update username
                $this->update( $userId, ['username' => $_POST['new_username']]);
                //Update session
                $_SESSION['auth_username'] = $_POST['new_username'];
            } else {
                return $response->withStatus(422, 'Username is possesed');
            }
        } 
        return $response;
    }

    public function reset_password( RequestInterface $request, ResponseInterface $response, $args ) : ResponseInterface {
        try {
            $this->auth->changePassword($_POST['old_password'], $_POST['new_password']);
        
            echo 'Password has been changed';
        }
        catch (\Delight\Auth\NotLoggedInException $e) {
            return $response->withStatus( 403, 'Not logged in' );
        }
        catch (\Delight\Auth\InvalidPasswordException $e) {
            return $response->withStatus( 403, 'Invalid password' );
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            return $response->withStatus( 403, 'Too many requests' );
        }

        return $response;
    }

    public function reset_email( RequestInterface $request, ResponseInterface $response, $args ) : ResponseInterface {
        try {
            if ($this->auth->reconfirmPassword($_POST['password'])) {
                $this->auth->changeEmail($_POST['new_email'], function ($selector, $token) {
                    $to = '<' . $_POST['new_email'] . '>';
                    $message = 'Dear ' . $this->auth->getUsername() . "\r\n".
                    'You have recently registered at Wordy, please confirm your password: ' . "\r\n" . 
                    'http:\\localhost\wordy\email_confirm?selector=' . \urlencode($selector) . '&token=' . \urlencode($token) . "\r\n" 
                    . 'Thank you for choosing Wordy !' . "\r\n";
                    $header = 'From:<trake1524@gmail.com>' . "\r\n" 
                    . 'Reply-to: <trake1524@gmail.com>';

                    $send = mail( $to, 'Please confirm your registration on Wordy', $message, $header);
                });
                $response->getBody()->write( 'Confirm your new email' );
                return $response;
            }
            else {
                $response->getBody()->write( 'Wrong pass :(' );
                return $response;
            }
        }
        catch (\Delight\Auth\InvalidEmailException $e) {
            die('Invalid email address');
        }
        catch (\Delight\Auth\UserAlreadyExistsException $e) {
            die('Email address already exists');
        }
        catch (\Delight\Auth\EmailNotVerifiedException $e) {
            die('Account not verified');
        }
        catch (\Delight\Auth\NotLoggedInException $e) {
            die('Not logged in');
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            die('Too many requests');
        }
    }

    public function forgot_password() {

    }
}