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
        if(isset($_SERVER['HTTP_ORGANIZATIONID'])){
            $organizationId = $_SERVER['HTTP_ORGANIZATIONID'];
            $params = [
                "index" => "classes,groups,lessons",
                "type" => "class,group,lesson",
                "body" => [
                    "query" => [
                        "bool" => [
                            "must" => [ "match" => [ "OrganizationId" => $organizationId
                            ]],
                            "should" => [
                                "match_phrase_prefix" => [
                                    "Name" => $itemToSearch
                                ],
                            ],
                            "minimum_should_match" => 1
                        ]
                    ]
                ]
            ];

        }else {
            $params = [
                "index" => "classes,groups,lessons",
                "type" => "class,group,lesson",
                "body" => [
                    "query" => [
                        "bool" => [
                            "should" => [
                                "match_phrase_prefix" => [
                                    "Name" => $itemToSearch
                                ]
                            ]
                        ]
                    ]
                ]
            ];
        }
        $searchResult = $this->client->search($params);
        return $searchResult["hits"]["hits"];
    }

    public function quickSearch($itemToSearch){
        $params = [
            "index" => "classes,groups,lessons",
            "type" => "class,group,lesson",
            "body" => [
                "query" => [
                    "bool" => [
                        "should" => [
                           "match_phrase_prefix" => [
                               "Name" => $itemToSearch
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