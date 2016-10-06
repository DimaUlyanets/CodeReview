<?php


namespace App\ElasticSearch;

use Elasticsearch\ClientBuilder;

class ElasticSearch
{

    private $client;

    function __construct()
    {

        $this->client = ClientBuilder::create()->build();

    }

    public function fullSearch($itemToSearch)
    {
        $params = [
            'index' => 'classes,groups,lessons',
            'type' => 'class,group,lesson',
            'body' => [
                'query' => [
                    'bool' => [
                        "should" => [
                            ["match" => ["Name" => $itemToSearch]],
                            ["match" => ["Thumbnail" => $itemToSearch]],
                        ]
                    ]
                ]
            ]
        ];

        $searchResult = $this->client->search($params);
        return $searchResult['hits']['hits'];

    }

    public function quickSearch($itemToSearch)
    {
        $params = [
            'index' => 'classes,groups,lessons',
            'type' => 'class,group,lesson',
            'body' => [
                "from" => 0, "size" => 2,
                'query' => [
                    'bool' => [
                        "should" => [
                            ["match" => ["Name" => $itemToSearch]],
                            ["match" => ["Thumbnail" => $itemToSearch]],
                        ]
                    ]
                ]
            ]
        ];

        $searchResult = $this->client->search($params);
        return $searchResult['hits']['hits'];


    }

}