<?php

return [
    'disk_name' => 'public',

    'max_file_size' => 1024 * 1024 * 10, // 10 MB

    'media_model' => \App\Models\Media::class,

    'media_collection_name' => 'default',

    'image_sizes' => [
        'thumb' => '128',
        'small' => '320',
        'medium' => '640',
        'large' => '1024',
    ],

    'image_manipulation' => [
        'enable' => true,
        'quality' => 90,
        'format' => 'webp',
    ],

    'responsive_images' => [
        'enable' => true,
        'sizes' => [
            'small' => '320',
            'medium' => '640',
            'large' => '1024',
        ],
    ],

    'custom_properties' => [
        'image' => [
            'primary' => true,
            'gallery' => false,
        ],
    ],
];