<?php
    if ( $logged ) {
        header( 'Location: search' );
        exit();
    } else {
?>
<form action="" method="post">
    <?php require_once 'error_message.php'; ?>
    <input type="text" placeholder="Enter your email" id="new_pass" name="email">
    <button type="submit">Reset password</button>
</form>
<?php
    }
?>