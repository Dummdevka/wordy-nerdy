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
    public function __construct () {
        parent::__construct();
        //Auth package
        $this->auth = new AuthLib( $this->db->connect(),null,null,false );
    }

    public function signup ( $mail_func ) {
        try {
            $userId = $this->auth->register( $_POST['email'], 
                $_POST['password'], 
                $_POST['username'], 
                function( $selector, $token ) use ($mail_func) {
                    
                    $mail_func( $_POST['email'], 
                    'Registration on Wordy', 
                    $this->config['mail_samples']['register'],
                    'http:\\localhost\wordy\email_confirm?&redirect=auth&selector='
                    . \urlencode($selector) 
                    . '&token=' . \urlencode($token));
                });
            $_SESSION['temp_email'] = $_POST['email'];
            return true;

        } catch(\Delight\Auth\InvalidEmailException $e) {
            exit('Invalid email');
        } catch(\Delight\Auth\InvalidPasswordException $e) {
            exit('Invalid password');
        } catch(\Delight\Auth\UserAlreadyExistsException $e) {
            exit( 'User exists' );
        } catch(\Delight\Auth\TooManyRequestsException $e) {
            exit('Too many requests');
        }
    }
    public function login () {
        if ($this->auth->isLoggedIn()) {
            return 'The user is logged in';
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
                return $e->getMessage();
            } catch(\Delight\Auth\InvalidPasswordException $e) {
                return $e->getMessage();
            } catch(\Delight\Auth\EmailNotVerifiedException $e) {
                return $e->getMessage();
            } catch(\Delight\Auth\TooManyRequestsException $e) {
                return $e->getMessage();
            }
            return true;
        }
    }

    public function logout() {
        
        try {
            $this->auth->logOutEverywhere();
            return true;
        } catch( \Delight\Auth\NotLoggedInException $e ){
            return false;
        }
    }

    public function deleteUser( $id ) {
        try {
            $this->auth->admin()->deleteUserById($id);
            $this->auth->destroySession();
        }
        catch (\Delight\Auth\UnknownIdException $e) {
            return $e->getMessage();
        }
        return true;
    }

    public function emailConfirmation() {
        
        try {
            //For forgot password function
            if ( isset($_GET['redirect'])&&strcmp($_GET['redirect'], 'new_pass') === 0) {
                $this->auth->canResetPasswordOrThrow($_GET['selector'], $_GET['token']);
                $_POST['seletor'] = $_GET['selector'];
                $_POST['token'] = $_GET['token'];
            } else {
                //Confirm email
                $this->auth->confirmEmail( $_GET['selector'], $_GET['token'] );
            }
                if ( isset( $_SESSION['temp_email'] )) {
                    unset( $_SESSION['temp_email'] );
                }
            return true;
        } catch (\Delight\Auth\InvalidSelectorTokenPairException $e) {
            exit ( 'Invalid url' );
        }
        catch (\Delight\Auth\TokenExpiredException $e) {
            exit ( 'Token expired' );
        }
        catch (\Delight\Auth\UserAlreadyExistsException $e) {
            exit ( 'User already exists' );
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            exit ( 'Too many requests' );
        }
    } 

    //Authentication with Google Account
    public function auth_with_google( $mail_func ) {
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
            return $this->signup( $mail_func );
        } else {
            header('Location:' . $client->createAuthUrl());
            exit();
        }
    }
    public function resetUsername () {
            //Check that username is unique
            if( empty($this->id(['username' => $_POST['new_username']]))){
                $userId = $this->auth->getUserId();
                //Update username
                $this->update( $userId, ['username' => $_POST['new_username']]);
                //Update session
                $_SESSION['auth_username'] = $_POST['new_username'];
                return true;
            } else {
                return false;
            }
    }
    public function resetPassword () {
        try {
            $this->auth->changePassword($_POST['old_password'], $_POST['new_password']);
        
            return true;
        }
        catch (\Delight\Auth\NotLoggedInException $e) {
            return $e->getMessage();
        }
        catch (\Delight\Auth\InvalidPasswordException $e) {
            return $e->getMessage();
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            return $e->getMessage();
        }
    }

    public function resetEmail ( $mail_func ) {
        try {
            if ($this->auth->reconfirmPassword($_POST['password'])) {
                $this->auth->changeEmail($_POST['new_email'], function ($selector, $token) use ($mail_func) {
                    $mail_func ( $_POST['new_email'], 
                     'Wordy Email Reset',
                     $this->config['mail_samples']['new_email'], 
                     'http:\\localhost\wordy\email_confirm?selector='
                     . \urlencode($selector) 
                     . '&token=' . \urlencode($token)) . '&redirect=dashboard';
                });
                return true;
            }
            else {
                return false;
            }
        }
        catch (\Delight\Auth\InvalidEmailException $e) {
            return $e->getMessage();
        }
        catch (\Delight\Auth\UserAlreadyExistsException $e) {
            return $e->getMessage();
        }
        catch (\Delight\Auth\EmailNotVerifiedException $e) {
            return $e->getMessage();
        }
        catch (\Delight\Auth\NotLoggedInException $e) {
            return $e->getMessage();
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            return $e->getMessage();
        }
    }
    public function resendEmail ( $email, $mail_func ) {
        try {
            $this->auth->resendConfirmationForEmail($email, function ($selector, $token) use ( $email, $mail_func ) {
                $mail_func( $email, 
                    'Registration on Wordy', 
                    $this->config['mail_samples']['register'],
                    'http:\\localhost\wordy\email_confirm?redirect=auth&selector='
                    . \urlencode($selector) 
                    . '&token=' . \urlencode($token));
            });
            return true;
        }
        catch (\Delight\Auth\ConfirmationRequestNotFound $e) {
            exit('No earlier request found that could be re-sent');
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            exit('There have been too many requests -- try again later');
        }
    }
    public function forgotPassword( $mail_func ) {
        try {
            $this->auth->forgotPassword($_POST['email'], function ($selector, $token) use ( $mail_func ) {
                $mail_func( $_POST['email'], 
                'Forgot password on Wordy', 
                $this->config['mail_samples']['forgot_password'],
                'http:\\localhost\wordy\email_confirm?redirect=new_pass&selector='
                . \urlencode($selector) 
                . '&token=' . \urlencode($token));
            });
            return true;
        }
        catch (\Delight\Auth\InvalidEmailException $e) {
            exit('Invalid email address');
        }
        catch (\Delight\Auth\EmailNotVerifiedException $e) {
            exit('Email not verified');
        }
        catch (\Delight\Auth\ResetDisabledException $e) {
            exit('Password reset is disabled');
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            exit('Too many requests');
        } 
    } 
    public function setNewPassword () {
        try {
            $this->auth->resetPassword($_COOKIE['selector'], 
             $_COOKIE['token'], 
             $_POST['new_password']);
            setcookie('token', null, time() - 1); 
            setcookie('selector', null, time() - 1); 
            return true;
        }
        catch (\Delight\Auth\InvalidSelectorTokenPairException $e) {
            die('Invalid token');
        }
        catch (\Delight\Auth\TokenExpiredException $e) {
            die('Token expired');
        }
        catch (\Delight\Auth\ResetDisabledException $e) {
            die('Password reset is disabled');
        }
        catch (\Delight\Auth\InvalidPasswordException $e) {
            die('Invalid password');
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            die('Too many requests');
        }
    }
}