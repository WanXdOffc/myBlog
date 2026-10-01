<?php

return [
    'feeds' => [
        'main' => [
            'items'       => [\App\Models\Post::class, 'getFeedItems'],
            'url'         => '/feed',
            'title'       => 'TechJournal RSS Feed',
            'description' => 'Sensational software engineering, Laravel architecture, and tech articles.',
            'language'    => 'id-ID',
            'image'       => '',
            'format'      => 'atom',
            'view'        => 'feed::atom',
            'type'        => '',
            'contentType' => '',
        ],
    ],
];
