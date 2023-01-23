<?php

// Database settings

return [
    //Mysql data
    'database' => [
        'host' => '127.0.0.1',
        'db_name' => 'wordy',
        'user' => 'root',
        'password' => '',
    ],
    //Google auth data
    'google' => [
        'clientID' => '483338206732-u121esjg6sumjg4ufnr5qnk667t38843.apps.googleusercontent.com',
        'clientSecret' => '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',
        'redirectUri' => 'http://localhost/wordy/auth_with_google',
    ],
    //Pass data for email authentication
    'mail' => [
        'username' => 'wordy.nerdy25@gmail.com',
        'password' => 'Trake1524'
    ],
    //Samples for email letters
    'mail_samples' => [
        'register' => "Hi there! <br> You have recently created an account on Wordy.
                        Please note that your <b>password</b> is the same as your e-mail address.
                        You can change it anytime!
                        Please confirm it by clicking the link down below: ",
        'forgot_password' => "Hi there! <br> You have recently <b>changed</b> your password on Wordy.
                        Please confirm it by clicking the link down below: ",
        'new_email' => "Hi there! <br> You have recently <b>changed</b> your email on Wordy.
                        Please confirm it by clicking the link down below: ",
    ]
];
