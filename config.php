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
    //Wordpress src
    'websites_url' => [
        //Literature
        'https://www.janefriedman.com/',
        'http://bookriot.com/',
        'https://therumpus.net/',

        //Fashion
        'https://thedaileigh.com/',
        'http://thefashionguitar.com/',
        'https://girlwithcurves.com/',

        //Nature
        'http://natureinmind.ie/',
        'https://timswww.com.au/',
        'https://www.explorenature.org/'
    ],
    //Google auth data
    'google' => [
        'clientID' => '483338206732-u121esjg6sumjg4ufnr5qnk667t38843.apps.googleusercontent.com',
        'clientSecret' => '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',
        'redirectUri' => 'http://localhost/wordy/auth_with_google',
    ],
    //Pass data for email authentication
    'mail' => [
        'username' => 'username',
        'password' => 'password'
    ],
    //Samples for email letters
    'mail_samples' => [
        'register' => 'Hi there! You have recently created an account on Wordy.
                        Please note that your password is the same as your e-mail account.
                        You can change it anytime!
                        Please confirm it by clicking the link down below: ',
        'forgot_password' => 'Hi there! You have recently changed your password on Wordy.
                        Please confirm it by clicking the link down below: ',
        'new_email' => 'Hi there! You have recently changed your email on Wordy.
                        Please confirm it by clicking the link down below: ',
    ]



    
];
