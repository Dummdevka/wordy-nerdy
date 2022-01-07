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
        'https://reala.co/',
    ],
    //Google auth data
    'google' => [
        'clientID' => '483338206732-muqtamlkanrn0svqsdb6pud2ek6qsh8m.apps.googleusercontent.com',
        'clientSecret' => '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',
        'redirectUri' => 'http://localhost/wordy/auth_google',
    ],
    //Pass data for email authentication
    'mail' => [
        'username' => 'trake1524@gmail.com',
        'password' => 'Idinachuii'
    ],
    //Samples for email letters
    'mail_samples' => [
        'register' => 'Hi there! You have recently created an account on Wordy.
                        Please confirm it by clicking the link down below: ',
        'new_pass' => 'Hi there! You have recently changed your password on Wordy.
                        Please confirm it by clicking the link down below: ',
        'new_email' => 'Hi there! You have recently changed your email on Wordy.
                        Please confirm it by clicking the link down below: ',
    ]
];