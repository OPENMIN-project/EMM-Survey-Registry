<?php

namespace App;

use ScoutElastic\IndexConfigurator;
use ScoutElastic\Migratable;

class SurveyIndexConfigurator extends IndexConfigurator
{
    use Migratable;

    protected $name = "ethmig_surveys";
    /**
     * @var array
     */
    protected $settings = [
        "index"    => [
            "requests" => [
                "cache" => [
                    "enable" => false
                ]
            ],
        ],
        'analysis' => [
            'analyzer'  => [
                'ethmig_analyzer' => [
                    'stopwords' => '_english_',
                    'analyzer'  => 'english',
                    'tokenizer' => 'my_ngram',
                    'filter'    => 'lowercase'
                ],
                "rebuilt_english" => [
                    "tokenizer" => "standard",
                    "filter"    => [
                        "english_possessive_stemmer",
                        "lowercase",
                        "english_stop",
                        "english_keywords",
                        "english_stemmer"
                    ]
                ]
            ],
            "filter"    => [
                "english_stop"               => [
                    "type"      => "stop",
                    "stopwords" => "_english_"
                ],
                "english_keywords"           => [
                    "type"     => "keyword_marker",
                    "keywords" => ["example"]
                ],
                "english_stemmer"            => [
                    "type"     => "stemmer",
                    "language" => "english"
                ],
                "english_possessive_stemmer" => [
                    "type"     => "stemmer",
                    "language" => "possessive_english"
                ]
            ],
            'tokenizer' => [
                'my_ngram' => [
                    'type'        => 'ngram',
                    'min_gram'    => 3,
                    'max_gram'    => 4,
                    'token_chars' => [
                        'letter',
                        'digit'
                    ]
                ]
            ]
        ]
    ];
}
