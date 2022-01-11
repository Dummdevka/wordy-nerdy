<?php
namespace controllers;

use Delight\Auth\NotLoggedInException;
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
            $res = $this->user->login();
            //debug( $res );
            // exit();
            if ( $res === true ){
                //Redirect
                return $response->withStatus(200);
            } else {
                return $response->withStatus(400, $res);
            }
        } else {
            return $response->withStatus(400, 'You cannot submit an empty form!');
        }
    }
    public function log_out (RequestInterface $request, ResponseInterface $response, $args ) : ResponseInterface {
        if ( $this->user->logout() ){
            //Redirect back
            return $response->withHeader('Location', 'search')->withStatus(200);
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
        //Check that user is logged in
        if ( $_SESSION['auth_user_id'] > 0 && isset($_POST['sentence'])) {
            if ( $this->user->addFavorite( $_SESSION['auth_user_id'], $_POST['sentence'] )) {
                return $response->withStatus(201, 'Success');
            } else {
                return $response->withStatus(409, 'Error to load data');
            }
        } else {
            return $response->withStatus(403, 'Not logged in!');
        }
    }
    public function get_favorite ( RequestInterface $request, ResponseInterface $response, $args ) : ResponseInterface {
        if ( $_SESSION['auth_user_id'] > 0 ) {
            if ( !empty($this->user->getFavorite( $_SESSION['auth_user_id']) )) {
                $response->getBody()->write(json_encode($this->user->getFavorite( $_SESSION['auth_user_id'])));
                return $response->withStatus(200, 'Success');
            } else {
                $response->getBody()->write(json_encode("No sentences could be found. Let's explore!"));
                return $response->withStatus(200);
            }
        } else {
            return $response->withStatus(403, 'Not logged in!');
        }
    }
    public function delete_favorite ( RequestInterface $request, ResponseInterface $response, $args ) : ResponseInterface {
        if ( $this->user->deleteFavorite( $args['id'] )) {
            return $response->withStatus(200, 'Success');
        } else {
            return $response->withStatus(404, 'Error deleting');
        }
    }
}