<?php
    if ( $logged ) {
        header( 'Location: search' );
        exit();
    } else {
        if ( isset( $_GET['status']) && strcmp($_GET['status'], 'confirm') == 0 && isset( $_SESSION['temp_email'])){
            echo '<p class="verify_message">Please verify your email: ' . $_SESSION['temp_email'] .' . Please note that your password is equal to your e-mail.</p>';
            echo '<input type="button" class="resend_button" value="Resend confirmation letter">';
        }
?>
<!-- <form action="login" method="post"> -->
    <span class="error_message"></span>
    <input type="text" name="password" id="password" placeholder="Password">
    <input type="text" name="username" id="username" placeholder="Username">
    <label for="remember_me">Remember me</label>
    <input type="checkbox" name="remember" id="remember_me" default="0">
    <input type="submit" name="auth_button" id="auth_button" value="Log in!">
<!-- </form> -->

<a href="forgot_pass">Forgot password?</a>
<a href="register">Sign up</a>
 <?php
    }
?>