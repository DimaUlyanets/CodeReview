<?php


namespace App\ElasticSearch;

use Elasticsearch\ClientBuilder;

class ElasticSearch
{
    private $client;

    function __construct(){
        $this->client = ClientBuilder::create()->setHosts([env("ELASTIC_SEARCH_HOST")])->build();
    }

    public function fullSearch($itemToSearch){
        $params = [
            "index" => "classes,groups,lessons,organisations,tags,users",
            "type" => "class,group,lesson,organisation,tag,user",
            "body" => [
                "query" => [
                    "bool" => [
                        "should" => [
                            "match_phrase_prefix" => [
                                "name" => $itemToSearch
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $searchResult = $this->client->search($params);
        return $searchResult["hits"]["hits"];
    }

    public function quickSearch($itemToSearch){
        $params = [
            "index" => "classes,groups,lessons,organisations,tags,users",
            "type" => "class,group,lesson,organisation,tag,user",
            "body" => [
                "query" => [
                    "bool" => [
                        "should" => [
                           "match_phrase_prefix" => [
                               "name" => $itemToSearch
                           ]
                        ]
                    ]
                ]
            ]
        ];
        $searchResult = $this->client->search($params);
        return $searchResult["hits"]["hits"];
    }

}