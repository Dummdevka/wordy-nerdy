<?php
    if ( $logged ) {
        header( 'Location: search' );
        exit();
    } else { 
        
?>
<form action="signup" method="post">
    <input type="text" name="email" placeholder="Email">
    <input type="text" name="password" placeholder="Password">
    <input type="text" name="username" placeholder="Username">
    <input type="submit" name="submit" id="register">
</form>

<a href="auth">Auth</a>
<a href="auth_with_google">Register with Google?</a>
<?php
    }
?>