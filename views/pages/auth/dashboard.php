<?php
if( $logged ) {
    $username = $_SESSION['auth_username'];
    $email = $_SESSION['auth_email'];
    $id = $_SESSION['auth_user_id'];
?>
    <div class="dashboard form">
        <span class="dashboard__span-message"></span>
        <div class="dashboard__block" id="dashboard-username">
            <p>Username: <?php echo $username; ?></p>
            <button type="button" id="ch_username"class="dashboard__btn btn-change"><i class="fas fa-pencil-alt"></i></button>
        </div>
        <div class="dashboard__block"id="dashboard-email">
            <p>Email: <?php echo $email; ?></p>
            <button type="button" id="ch_email" class="dashboard__btn btn-change"><i class="fas fa-pencil-alt"></i></button>
        </div>
        <div class="dashboard__block" id="dashboard-password">
            <p>Password:</p>
            <button type="button" id="ch_pass" class="dashboard__btn btn-change"><i class="fas fa-pencil-alt"></i></button>
        </div>
        <button type="button" id="delete_user" class="dashboard__btn btn-delete"value="<?php echo $id ?>">Delete my account</button>
    </div>
<?php
} else {
    echo '404 Forbidden';
}
?>
