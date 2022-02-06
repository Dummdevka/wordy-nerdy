<?php
    $logged = $_SESSION['auth_logged_in'] ?? false;
    $temp_email = $_SESSION['auth_temp_email'] ?? false;
    $admin = isset($_SESSION['auth_roles']) && $_SESSION['auth_roles'] === 1;
    $username = $_SESSION['auth_username'] ?? 'Wonderful Person';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/wordy/public/assets/css/main.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="/wordy/public/assets/main.js"></script>
    <script src="https://kit.fontawesome.com/9ac41c9e98.js" crossorigin="anonymous"></script>
    <title>Wordy</title>
</head>
<body>
    <nav>
    <div class="nav__logo">
            <a href="/wordy/public/search" class="nav__logo-link"><h1>Hurray Wordy!</h1></a>
        </div>
        <!-- For small screens -->
        <div class="nav__user-small">
        <?php if( $logged ) { ?>
            <span class="nav__user-small_arrow" id="small-menu-arrow"></span>
            <div class="nav__user-small_menu" id="small-menu">
                <ul>
                    <li><a href="/wordy/logout" class="nav__user-small_link">Logout</a></li>
                    <li><a href="/wordy/auth/dashboard" class="nav__user-small_link">Dashboard</a></li>
                    <li><a href="/wordy/auth/favorites" class="nav__user-small_link">Favorites</a></li>
                </ul>
                <?php } else { ?>
                    <a href="/wordy/guest/auth">
                        <button type="button" name="login"
                        id="login-btn" class="btn-link">
                            Login
                        </button>
                    </a>
                <?php } ?>
            </div>
        </div>
        
        <!-- For authorized users -->
        <div class="nav__user-normal">
            <?php
                if( $logged ){
                    ?>
                    <p class="nav__greeting"> Hi, <?php echo $username ?> ! Ready to explore the world of words? Jump in!</p>
                    <?php } ?>
                    <div class="nav__btns">
                        <?php if( $logged ){ ?>
                        <a class="nav__link-dashboard" href="/wordy/auth/dashboard">
                            <button type="button" class="nav__btn btn-link">                 
                                <i class="fas fa-address-card"></i> My profile 
                            </button>
                        </a>
                        <a class="nav__link" href="/wordy/auth/favorites">
                            <button type="button" class="nav__btn btn-link">Favorites</button>
                        </a>
                        <?php
                        if( $admin ) { ?>
                                <a href="/wordy/admin/dashboard">
                                 <button type="button" class="nav__btn btn-link">Admin dashboard</button>
                                </a>
                            <?php }?>
                            <a href="/wordy/guest/logout">
                                <button type="button" name="logout" id="logout-btn" class="btn-link">Log out</button>
                            </a>
                        <?php } else {?>
                    <a href="/wordy/guest/auth">
                        <button type="button" name="login" id="login-btn" class="btn-link">Log in</button>
                    </a>
                    <?php } ?>
                    </div>
        </div>
    </nav>
        <!-- Include the page -->
        <?php
            if(!empty($args['page']) && !empty($args['path'])){
                require_once 'pages/' . $args['path'] . DS . $args['page'] . '.php';
            } else {
                echo 'No page';
            }
        ?>
        
<script src="/wordy/public/assets/scripts/visual.js"></script>
<script src="/wordy/public/assets/scripts/ajax.js"></script>

</body>
</html>