<div class="auth form">
<?php require_once 'error_message.php'; ?>
<form action="" method="post" class="auth__form form">
    <?php require_once 'error_message.php'; ?>
    <input type="text" name="email" placeholder="Email" class="auth__input input-email">
    <input type="text" name="password" placeholder="Password" class="auth__input input-password">
    <input type="text" name="username" placeholder="Username" class="auth__input input-username">
    <button type="submit" name="submit" id="register"class="auth__btn btn-register">Register</button>
</form>

<a href="/wordy/guest/auth" class="auth__link">Auth</a>
<a href="/wordy/auth_with_google" class="auth__link">Register with Google?</a>
</div>
