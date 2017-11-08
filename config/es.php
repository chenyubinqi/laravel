<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Elasticsearch Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the Elasticsearch connections below you wish
    | to use as your default connection for all work. Of course.
    |
    */

    'default' => env('ELASTIC_CONNECTION', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Elasticsearch Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the Elasticsearch connections setup for your application.
    | Of course, examples of configuring each Elasticsearch platform.
    |
    */

    'connections' => [

        'default' => [

            'servers' => [

                [
                    "host" => env("ELASTIC_HOST", "127.0.0.1"),
                    "port" => env("ELASTIC_PORT", 9200),
                    'user' => env('ELASTIC_USER', ''),
                    'pass' => env('ELASTIC_PASS', ''),
                    'scheme' => env('ELASTIC_SCHEME', 'http'),
                ]

            ],

            'index' => env('ELASTIC_INDEX', 'product'),

            // Elasticsearch handlers
            // 'handler' => new MyCustomHandler(),
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Elasticsearch Indices
    |--------------------------------------------------------------------------
    |
    | Here you can define your indices, with separate settings and mappings.
    | Edit settings and mappings and run 'php artisan es:index:update' to update
    | indices on elasticsearch server.
    |
    | 'my_index' is just for test. Replace it with a real index name.
    |
    */

    'indices' => [

        'product_v1' => [

            "aliases" => [
                "product"
            ],

            'settings' => [
                "number_of_shards" => env('ELASTIC_NUMBER_OF_SHARDS', '5'),
                "number_of_replicas" => env('ELASTIC_NUMBER_OF_REPLICAS', '1'),
            ],

            'mappings' => [
                'pms_product' => [
                    'properties' => [
                        'product_name' => [
                            'analyzer' => 'ik_max_word',
                            'type' => 'text',
                            'fields' => [
                                'keyword' => [
                                    'type' => 'keyword',
                                    'ignore_above' => 256,
                                ]
                            ],
                        ],
                        'product_name_en' => [
                            'analyzer' => 'english',
                            'type' => 'text'
                        ],
                        'source' => [
                            'type' => 'long'
                        ],
                        'main_category' => [
                            'type' => 'long'
                        ],
                        'formula' => [
                            'type' => 'keyword'
                        ],
                        'status' => [
                            'type' => 'long'
                        ],
                        'cas_no' => [
                            'type' => 'keyword'
                        ],
                        'product_code' => [
                            'type' => 'keyword'
                        ],
                        'en_synonyms' => [
                            'type' => 'text',
                            'fields' => [
                                'keyword' => [
                                    'type' => 'keyword',
                                    'ignore_above' => 256,
                                ]
                            ],
                        ],
                        'product_category' => [
                            'type' => 'long'
                        ],
                        'product_parent_category' => [
                            'type' => 'long'
                        ],
                        'sort' => [
                            'type' => 'long'
                        ],
                        'create_time' => [
                            'type' => 'date',
                            "format" => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis"
                        ],
                    ]
                ]
            ]

        ]
    ]

];
