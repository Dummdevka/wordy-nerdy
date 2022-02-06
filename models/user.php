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
    public function __construct()
    {
        parent::__construct();
        //Auth package
        $this->auth = new AuthLib($this->db->connect(), null, null, false);
    }

    //Is used to re-use try-catch for Auth Library
    public function catchErrors(callable $callback)
    {
        try {
            return $callback();
        } catch (\Delight\Auth\UnknownUsernameException $e) {
            return 'Unknown Username';
        } catch (\Delight\Auth\InvalidPasswordException $e) {
            return 'Invalid Password';
        } catch (\Delight\Auth\EmailNotVerifiedException $e) {
            return 'Email Not Verified';
        } catch (\Delight\Auth\TooManyRequestsException $e) {
            return 'Too Many Requests';
        } catch (\Delight\Auth\NotLoggedInException $e) {
            return 'Not logged in';
        } catch (\Delight\Auth\UnknownIdException $e) {
            return 'Unknown user id';
        } catch (\Delight\Auth\InvalidSelectorTokenPairException $e) {
            return 'Invalid selector';
        } catch (\Delight\Auth\TokenExpiredException $e) {
            return 'Token expired';
        } catch (\Delight\Auth\UserAlreadyExistsException $e) {
            return 'User already exists';
        } catch (\Delight\Auth\InvalidEmailException $e) {
            return 'Invalid email';
        } catch (\Delight\Auth\ConfirmationRequestNotFound $e) {
            return 'No earlier request found that could be re-sent';
        } catch (EmailNotSentException $e) {
            return $e->getMessage();
        }
    }
    public function signup($mail_func) {
        return $this->catchErrors(function () use ($mail_func) {
            $userId = $this->auth->register(
                $_POST['email'],
                $_POST['password'],
                $_POST['username'],
                function ($selector, $token) use ($mail_func) {
                    $mail_func(
                        $_POST['email'],
                        'Registration on Wordy',
                        $this->config['mail_samples']['register'],
                        'http://localhost/wordy/email_confirm?&redirect=guest/auth&selector=' . \urlencode($selector). '&token=' . \urlencode($token)
                    );
                }
            );
            $_SESSION['temp_email'] = $_POST['email'];
            return true;
        });
    }

    public function login() {
        if ($this->auth->isLoggedIn()) {
            return 'The user is logged in';
        } else {
            return $this->catchErrors(function () {
                //Remebering the user
                if (!empty($_POST['remember']) && $_POST['remember'] == 1) {
                    $rememberDuration = (int) (60 * 60 * 24 * 365);
                } else {
                    $rememberDuration = null;
                }

                //Log the user in
                $this->auth->loginWithUsername($_POST['username'], $_POST['password'], $rememberDuration);
                return true;
            });
        }
    }

    public function logout() {
        return $this->catchErrors(function () {
            $this->auth->logOutEverywhere();
            return true;
        });
    }

    public function deleteUser($id) {
        return $this->catchErrors(function () use ( $id ) {
            $this->auth->admin()->deleteUserById( $id );
            $this->auth->destroySession();
            return true;
        });
    }

    public function emailConfirmation() {
        return $this->catchErrors(function () {
            //For forgot password function
            if (isset($_GET['redirect']) && strcmp($_GET['redirect'], 'new_pass') === 0) {
                $this->auth->canResetPasswordOrThrow($_GET['selector'], $_GET['token']);
                $_POST['seletor'] = $_GET['selector'];
                $_POST['token'] = $_GET['token'];
            } else {
                //Confirm email
                $this->auth->confirmEmail($_GET['selector'], $_GET['token']);
            }
            if (isset($_SESSION['temp_email'])) {
                unset($_SESSION['temp_email']);
            }
            return true;
        });
    }

    //Authentication with Google Account
    public function auth_with_google($mail_func) {
        $client = new Client();
        $client->setClientId($this->config['google']['clientID']);
        $client->setClientSecret($this->config['google']['clientSecret']);
        $client->setRedirectUri($this->config['google']['redirectUri']);
        $client->addScope("email");
        $client->addScope("profile");

        if (isset($_GET['code'])) {
            $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

            $client->setAccessToken($token['access_token']);
            $google_oauth = new ServiceOauth2($client);
            $google_account_info = $google_oauth->userinfo->get();
            $email = $google_account_info->email;
            $name = $google_account_info->name;
            //Storing data in POST in order to create a user
            $_POST['username'] = $name;
            $_POST['email'] = $email;
            $_POST['password'] = $email;
            //Warning the user
            return $this->signup($mail_func);
        } else {
            header('Location:' . $client->createAuthUrl());
            exit();
        }
    }
    public function resetUsername() {
        //Check that username is unique
        if (empty($this->id(['username' => $_POST['new_username']]))) {
            $userId = $this->auth->getUserId();
            //Update username
            $this->update($userId, ['username' => $_POST['new_username']]);
            //Update session
            $_SESSION['auth_username'] = $_POST['new_username'];
            return true;
        } else {
            return false;
        }
    }
    public function resetPassword() {
        return $this->catchErrors( function() {
            $this->auth->changePassword($_POST['old_password'], $_POST['new_password']);
            return true;
        });
    }

    public function resetEmail($mail_func) {
        return $this->catchErrors(function () use ($mail_func) {
            if ($this->auth->reconfirmPassword($_POST['password'])) {
                $this->auth->changeEmail($_POST['new_email'], function ($selector, $token) use ($mail_func) {
                    $mail_func(
                        $_POST['new_email'],
                        'Wordy Email Reset',
                        $this->config['mail_samples']['new_email'],
                        'http://localhost/wordy/email_confirm?selector=' . \urlencode($selector) . '&token=' . \urlencode($token) . '&redirect=auth/dashboard');
                    }
                );
                return true;
            } else {
                return false;
            }
        });
    }

    public function resendEmail($email, $mail_func) {
        return $this->catchErrors( function() use ($email, $mail_func) {
            $this->auth->resendConfirmationForEmail($email, function ($selector, $token) use ($email, $mail_func) {
                $mail_func(
                    $email,
                    'Registration on Wordy',
                    $this->config['mail_samples']['register'],
                    'http://localhost/wordy/email_confirm?redirect=/wordy/guest/auth&selector=' . \urlencode($selector) . '&token=' . \urlencode($token)
                );
            });
            return true;
        });
    }

    public function forgotPassword ( $mail_func ) {
        return 'Lalala';

        return $this->catchErrors( function() use ($mail_func) {
            $this->auth->forgotPassword($_POST['email'], function ($selector, $token) use ($mail_func) {
                
                $mail_func(
                    $_POST['email'],
                    'Forgot password on Wordy',
                    $this->config['mail_samples']['forgot_password'],
                    'http://localhost/wordyemail_confirm?redirect=guest/new_pass&selector=' . \urlencode($selector) . '&token=' . \urlencode($token)
                );
            });
            return true;
        });
    }

    public function setNewPassword()
    {
        return $this->catchErrors( function() {
            $this->auth->resetPassword(
                $_COOKIE['selector'],
                $_COOKIE['token'],
                $_POST['new_password']
            );
            setcookie('token', null, time() - 1);
            setcookie('selector', null, time() - 1);
            return true;
        });
    }

    //Favorites
    public function addFavorite(int $user_id, string $sentence) {
        $vals = compact('user_id', 'sentence');
        if ($this->db->create('favorites', 'user_id, sentence', $vals)) {
            return true;
        } else {
            return false;
        }
    }
    public function getFavorite(int $user_id) {
        $vals = compact('user_id');
        $list = $this->db->get('favorites', 'id, sentence', $vals);
        return $list;
    }
    public function deleteFavorite(int $id) {
        return $this->db->delete('favorites', $id);
    }
}
