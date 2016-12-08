<?php

namespace App\ElasticSearch;
use Elasticsearch;

class TopicSearch{

    private $client;

    function __construct(){
        $this->client = Elasticsearch\ClientBuilder::create()->setHosts([env("ELASTIC_SEARCH_HOST")])->build();
    }

    public function addToIndex($id, $name, $followers = 0){
        $params = [
            "index" => "tags",
            "type" => "tag",
            "id" => $id,
            "body" => [
                "id"=>$id,
                "name" => $name,
                "followers" => $followers
            ]
        ];
        $this->client->index($params);
    }

    public function updateIndex($id, $followers){
        $params = [
            "index" => "tags",
            "type" => "tag",
            "id" => $id,
            "body" => [
                "id"=>$id,
                "followers" => $followers
            ]
        ];
        $this->client->update($params);
    }

    public function deleteIndex($id){
        $params = [
            "index" => "tags",
            "type" => "tag",
            "id" => $id
        ];
        $this->client->delete($params);
    }
}