<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wordy</title>
</head>
<body>
    <a href="search"><h1>Hurray Wordy!</h1></a>
    <div class="container">
        <input type="button" value="Dump books!" id="dump_books">
        <?php
            if( !empty($_SESSION['auth_logged_in']) ){
                $logged = $_SESSION['auth_logged_in'];
            } else {
                $logged = false;
            }
            if(!empty($args['page'])){
                require_once 'pages/' . $args['page'] . '.php';
            } else {
                echo 'No page';
            }
        ?>
    </div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="public/assets/visual.js"></script>
<script src="public/assets/ajax.js"></script>
</body>
</html>