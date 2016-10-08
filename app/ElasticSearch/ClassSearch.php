<?php

namespace App\ElasticSearch;
use Elasticsearch;

class ClassSearch implements IElasticSearch{

    private $client;

    function __construct(){
        $this->client = Elasticsearch\ClientBuilder::create()->setHosts([env("ELASTIC_SEARCH_HOST")])->build();
    }

    public function addToIndex($id, $name, $thumbnail){
        $params = [
            "index" => "classes",
            "type" => "class",
            "id" => $id,
            "body" => [
                "Name" => $name,
                "Thumbnail" => $thumbnail
              ]
        ];
        $this->client->index($params);
    }

    public function updateIndex($id, $name, $thumbnail){
        $params = [
            "index" => "classes",
            "type" => "class",
            "id" => $id,
            "body" => [
                "Name" => $name,
                "Thumbnail" => $thumbnail
            ]
        ];
        $this->client->update($params);
    }

    public function deleteIndex($id){
        $params = [
            "index" => "classes",
            "type" => "class",
            "id" => $id
        ];
        $this->client->delete($params);
    }
}