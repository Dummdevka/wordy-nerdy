<form action="/wordy/set_new_pass" method="post">
    <?php require_once 'error_message.php'; ?>
    <input class="password" name="new_password" type="text" placeholder="New password">
    <button class="submit">Set</button>
</form>

<?php

var_dump( $_COOKIE );