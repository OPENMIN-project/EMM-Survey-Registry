<?php

namespace App;

use ScoutElastic\SearchRule;

class SurveySearch extends SearchRule
{
    public function buildQueryPayload()
    {
        return [
            'must' => [
                //@see https://opendistro.github.io/for-elasticsearch-docs/docs/elasticsearch/full-text/#simple-query-string
                'simple_query_string' => [
                    'query' => $this->builder->query,
                    'analyzer' => 'standard'
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function buildHighlightPayload()
    {
        return [
            "pre_tags"  => ["<strong>"],
            "post_tags" => ["</strong>"],
            'fields'    => [
                'title' => [
                    'matched_fields' => ['answers.f_1_3', 'answers.f_1_3.raw'],
                    'type'           => 'plain'
                ]
            ]
        ];
    }
}
