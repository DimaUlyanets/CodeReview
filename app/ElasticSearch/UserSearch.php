<?php

namespace App\ElasticSearch;
use Elasticsearch;

class UserSearch{

    private $client;

    function __construct(){
        $this->client = Elasticsearch\ClientBuilder::create()->setHosts([env("ELASTIC_SEARCH_HOST")])->build();
    }

    public function addToIndex($id, $name, $thumbnail){
        $params = [
            "index" => "users",
            "type" => "user",
            "id" => $id,
            "body" => [
                "id"=> $id,
                "name" => $name,
                "thumbnail" => $thumbnail
            ]
        ];
        $this->client->index($params);
    }

    public function updateIndex($id, $name, $thumbnail){
        $params = [
            "index" => "users",
            "type" => "user",
            "id" => $id,
            "body" => [
                "id" => $id,
                "name" => $name,
                "thumbnail" => $thumbnail
            ]
        ];
        $this->client->update($params);
    }

    public function deleteIndex($id){
        $params = [
            "index" => "users",
            "type" => "user",
            "id" => $id
        ];
        $this->client->delete($params);
    }
}