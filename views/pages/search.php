<div class="search">
    <!-- For authorized users -->
    <?php
        if( $logged ){
            $username = $_SESSION['auth_username'];
            ?>
            <p id="user_greeting"> Hi, <?php echo $username ?> ! Ready to explore the world of words? Jump in!</p>
            <a href="dashboard">
                <input type="button" id="profile_dashboard" value="My profile">
            </a>
            <?php
        }
    ?>
    <input type="button" name="<?php echo $logged ? 'logout' : 'login'; ?>" 
    id="<?php echo $logged ? 'logout' : 'login'; ?>" 
    value="<?php echo $logged ? 'Log out' : 'Log in'; ?>">
    
    
    <input type="text" id="word_input" placeholder="Look for...">
    <button type="button" id="lit_search" class="query_word">Literature</button>
    <button type="button" id="web_search" class="query_word">Web</button>
</div>
<div class="result">
    <div class="result_panel">
    </div>
    <div class="result_list"></div>
</div>
<script src="public/assets/refresh.js"></script>
