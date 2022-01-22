    <div class="auth form">
    <span class="auth__span-message form">
    <?php
        if ( isset( $_GET['status']) && strcmp($_GET['status'], 'confirm') == 0 && isset( $_SESSION['temp_email'])){
            echo '<p class="success_message">Please verify your email: ' . $_SESSION['temp_email'] .'<i class="fas fa-envelope-open"></i></p>';
            echo '<button type="button" class="auth__btn button-resend">Resend confirmation letter</button>';
        }
    ?>
    </span>
    <input type="text" name="password" id="password" class="auth__input input-password" placeholder="Password">
    <input type="text" name="username" id="username" class="auth__input input-username" placeholder="Username">
    <div class="auth__remember">
        <label class="auth__label" for="remember_me">Remember me</label>
        <input type="checkbox" name="remember" id="remember_me" class="auth__checkbox" default="0">
    </div>
    <button type="button" name="auth_button" id="auth_button" class="auth__btn">Log in!</button>
    <div class="auth__links">
        <a href="/wordy/guest/forgot_pass" class="auth__link-pass">Forgot password?</a>
        <a href="/wordy/guest/register" class="auth__link-signup" >Sign up</a>
    </div>
</div>
 