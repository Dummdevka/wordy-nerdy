<?php
    if ( !$temp_email ) {
        header( 'Location: search' );
        exit();
    } else {
?>
    <div class="confirm form">
    <p>Confirm your email!</p>
    <span class="confirm__span-message"></span>
    <button type="button" id="resend_button" class="confirm__btn">Resend</button>
    </div>
<?php
    }
?>