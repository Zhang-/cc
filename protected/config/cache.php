<?php
    return [
    		'dev' =>
    			[
		            'redis' =>
		                [
		                    'user' =>
		                        [
		                            'host' => '121.42.155.121',
		                            'port' => 6379,
		                            'db'   => 0
		                        ],
		                    'driver' =>
		                        [
		                            'host' => '121.42.155.121',
		                            'port' => 6379,
		                            'db'   => 1
		                        ],
		                ],
		            ],
		    'test' =>
    			[
		            'redis' =>
		                [
		                    'user' =>
		                        [
		                            'host' => '127.0.0.1',
		                            'port' => 6379,
		                            'db'   => 0
		                        ],
		                    'driver' =>
		                        [
		                            'host' => '127.0.0.1',
		                            'port' => 6379,
		                            'db'   => 1
		                        ],
		                ],
		            ],
		    'prod' =>
    			[
		            'redis' =>
		                [
		                    'user' =>
		                        [
		                            'host' => '127.0.0.1',
		                            'port' => 6379,
		                            'db'   => 0
		                        ],
		                    'driver' =>
		                        [
		                            'host' => '127.0.0.1',
		                            'port' => 6379,
		                            'db'   => 1
		                        ],
		                ],
		            ],
        ];