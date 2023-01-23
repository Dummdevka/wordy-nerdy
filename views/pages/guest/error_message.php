<span class="auth__span-message">
    <?php
    if (isset($args['message'])) {
    ?>
        <p class="error_message">
            <?php echo $args['message']; ?>
        </p>
    <?php
    }
    ?>
</span>