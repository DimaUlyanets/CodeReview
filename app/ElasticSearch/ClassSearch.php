<?php

namespace App\ElasticSearch;
use Elasticsearch;

class ClassSearch{

    private $client;

    function __construct(){
        // This can probably be registered as an instance in service provider rather than being declared in every class
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
               "doc" => [
                  "name" => $name,
                  "thumbnail" => $thumbnail
               ],
               "upsert" => [
                  "name" => $name,
                  "thumbnail" => $thumbnail
               ]
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