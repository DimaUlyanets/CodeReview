<?php

namespace App\ElasticSearch;
use Elasticsearch;

class ClassSearch{

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
                "id"=>$id,
                "name" => $name,
                "thumbnail" => $thumbnail
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
                "id"=>$id,
                "name" => $name,
                "thumbnail" => $thumbnail
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