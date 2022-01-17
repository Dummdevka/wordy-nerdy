<?php
    if ( $logged ) {
        header( 'Location: search' );
        exit();
    } else {
?>
<form action="/wordy/forgot_password" method="post">
    <input type="text" placeholder="Enter your email" id="new_pass" name="email">
    <button type="submit">Reset password</button>
</form>
<?php
    }
?>