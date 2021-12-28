<?php
    if ( $logged ) {
        header( 'Location: search' );
        exit();
    } else {
?>
<form action="signup" method="post">
    <input type="text" name="email">
    <input type="text" name="password">
    <input type="text" name="username">
    <input type="submit" name="submit" id="register">
</form>

<a href="auth">Auth</a>
<a href="auth_google">Register with Google?</a>
<?php
    }
?>