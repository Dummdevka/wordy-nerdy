<?php
    if ( $logged ) {
        header( 'Location: search' );
        exit();
    } else {
?>
<input type="text" name="password" id="password" placeholder="password">
<input type="text" name="username" id="username" placeholder="username">
<input type="checkbox" name="remember" id="remember_me" default="0">
<input type="button" name="auth_button" id="auth_button">

<a href="forgot_pass">Forgot password?</a>
<a href="register">Sign up</a>
 <?php
    }
?>