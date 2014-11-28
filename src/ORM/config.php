<?php

return [
    'database' => [
        'mysql'  => [
            'host'  => 'localhost',
            'db'    => 'orm',
            'user'  => 'root',
            'pass'  => 'root'
        ],
        'sqlite' => [
            'memory' => 'sqlite::memory',
            'file'   => 'sqlite::database.sqlite'
        ]
    ],
];