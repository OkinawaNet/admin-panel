<?php
return array(
    'mail' => array(
        'transport' => array(
            'options' => array(
                'name'              => 'smtp.gmail.com',
                'host'              => 'smtp.gmail.com',
                'port'              => '587',
                'connection_class'  => 'plain',
                'connection_config' => array(
                    'username' => '',
                    'password' => '',
                    'ssl'      => 'tls',
                ),
            ),
        ),
    ),
);
