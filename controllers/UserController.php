<?php
namespace controllers;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class UserController extends Controller
{
    protected $user;
    public $mail_func;
    public function __construct()
    {
        parent::__construct();

        //New User model
        $this->user = new $this->model();

        //Mail (provisional solution)
        $this->mail_func = function ( $to, $subject, $body, $link = '' ) {
            $phpmailer = new MailController();
            if ( $phpmailer->send( $to, $subject, $body, $link) ) {
                return true;
            } else {
                return false;
            }
        };
    }
    public function sign_up ( RequestInterface $request, ResponseInterface $response, $args ) : ResponseInterface {
        $method = explode('/', $request->getUri()->getPath());
        $method = end($method);
        if ( $res = $this->user->$method( $this->mail_func ) === true ) {
            $response->getBody()->write( 'Confirm your email!' );
            return $response->withHeader( 'Location', "http://localhost/wordy/auth?status=confirm")->withStatus( 302 );
        } else {
            $res = $this->user->$method( $this->mail_func);
            return $response->withStatus(404, $res);
        }
    }
    //TODO: log in with username or email!
    public function log_in ( RequestInterface $request, ResponseInterface $response, $args ) : ResponseInterface {
        if ( !empty($_POST['username'])&& !empty($_POST['password']) ){
            if ( $res = $this->user->login() ){
                return $response->withStatus(200);
            } else {
                return $response->withStatus(404, $res );
            }
        } else {
            $response->getBody()->write('You cannot submit an empty form!');
            return $response;
        }
    }
    public function log_out (RequestInterface $request, ResponseInterface $response, $args ) : ResponseInterface {
        if ( $this->user->logout() ){
            return $response->withStatus(200);
        } else {
            return $response->withStatus(404, 'Something went wrong');
        }
    }
    public function delete_user (RequestInterface $request, ResponseInterface $response, $args ) : ResponseInterface {
        $id = $args['id'];
        if ( $id > 0 ){
            if ( $res = $this->user->deleteUser( $id ) ){
                return $response->withStatus(200);
            } else {
                return $response->withStatus(404, $res );
            }
        }
    }
    public function email_confirmation ( RequestInterface $request, ResponseInterface $response, $args ) : ResponseInterface {
        if ( $res = $this->user->emailConfirmation() ) {
            if ( isset( $_GET['redirect']) ) {
                setcookie('selector', $_GET['selector'], time()+1800);
                setcookie('token', $_GET['token'], time()+1800);
                return $response->withHeader('Location', $_GET['redirect'])->withStatus(302);
            } else {
                return $response->withStatus(200);
            }
        } else {
            return $response->withStatus(404, $res);
        }
    }

    //Reset user info
    public function reset_username ( RequestInterface $request, ResponseInterface $response, $args ) : ResponseInterface {
        if ( !empty($_POST['new_username'])){
            if( $res = $this->user->resetUsername() === true ){
                return $response->withStatus(200);
            } else {
                return $response->withStatus(404, 'Some errors on the server');
            }
        } else {
            return $response->withStatus(422, 'Some fields are empty');
        }
    }
    public function reset_email ( RequestInterface $request, ResponseInterface $response, $args ) : ResponseInterface {
        if ( !empty($_POST['new_email'])&&!empty($_POST['password'])){
            if( $res = $this->user->resetEmail( $this->mail_func ) ){
                return $response->withStatus(200);
            } else {
                return $response->withStatus(404, $res);
            }
        } else {
            return $response->withStatus(422, 'Some fields are empty');
        }
    }
    public function reset_password ( RequestInterface $request, ResponseInterface $response, $args ) : ResponseInterface {
        if ( !empty($_POST['old_password'])&&!empty($_POST['new_password'])){
            if( $res = $this->user->resetPassword() ){
                return $response->withStatus(200);
            } else {
                return $response->withStatus(404, $res);
            }
        } else {
            return $response->withStatus(422, 'Some fields are empty');
        }
    }
    public function resend_email ( RequestInterface $request, ResponseInterface $response, $args ) : ResponseInterface {
        if ( isset($_SESSION['temp_email']) ) {
            if( $res = $this->user->resendEmail( $_SESSION['temp_email'], $this->mail_func ) === true ){
                return $response->withStatus( 200, 'Email sent' );
            } else {
                return $response->withStatus( 500, $res );
            }
        } else {
            return $response->withHeader('Location', 'search')->withStatus( 403, 'No email supplied' );
        }
    }
    public function forgot_password ( RequestInterface $request, ResponseInterface $response, $args ) : ResponseInterface {
        if ( !empty($_POST['email']) ){
            if( $res = $this->user->forgotPassword( $this->mail_func ) === true ){
                $_SESSION['temp_email'] = $_POST['email'];
                $args['token'] = $_GET['token'];
                $args['selector'] = $_GET['selector'];
                return $response->withHeader('Location', 'confirm')->withStatus(302);
            } else {
                return $response->withStatus(404, $res);
            }
        } else {
            return $response->withStatus(422, 'Some fields are empty');
        }
    }
    public function set_new_password ( RequestInterface $request, ResponseInterface $response, $args ) : ResponseInterface {
        if ( isset($_POST['new_password'])&& isset($_COOKIE['selector']) && isset($_COOKIE['token']) ){
            if ( $res = $this->user->setNewPassword() ){
                return $response->withHeader('Location', 'auth')->withStatus(302);
            } else {
                return $response->withStatus(404, $res);
            }
        } else {
            return $response->withStatus(422, 'Some fields are empty');
        }
    }
    public function add_favorite ( RequestInterface $request, ResponseInterface $response, $args ) : ResponseInterface {
        debug( $_POST );
    }
}