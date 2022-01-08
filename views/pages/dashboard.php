<?php
if( $logged ) {
    $username = $_SESSION['auth_username'];
    $email = $_SESSION['auth_email'];
    $id = $_SESSION['auth_user_id'];

    ?>
    <div class="profile_info">
        <div class="profile_username">
            <p>Username: <?php echo $username; ?></p>
            <input type="button" id="ch_username" value="Change it">
        </div>
        <div class="profile_email">
            <p>Email: <?php echo $email; ?></p>
            <input type="button" id="ch_email" value="Change it">
        </div>
        <div class="profile_password">
            <p>Password:</p>
            <input type="button" id="ch_pass" value="Change it">
        </div>
        <button type="button" id="delete_user" value="<?php echo $id ?>">Delete my account</button>
    </div>
<?php
} else {
    echo '404 Forbidden';
}
?>
