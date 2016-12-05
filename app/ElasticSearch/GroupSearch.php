<?php

namespace App\ElasticSearch;
use Elasticsearch;

class GroupSearch {

    private $client;

    function __construct() {
        $this->client = Elasticsearch\ClientBuilder::create()->setHosts([env("ELASTIC_SEARCH_HOST")])->build();
    }

    public function addToIndex($id, $name, $thumbnail, $orgId, $type){
        $params = [
            "index" => "groups",
            "type" => "group",
            "id" => $id,
            "body" => [
                "id" => $id,
                "name" => $name,
                "thumbnail" => $thumbnail,
                "orgId" => $orgId,
                "type" => $type
            ]
        ];
        $this->client->index($params);
    }

    public function updateIndex($id, $name, $thumbnail, $orgId, $type){
        $params = [
            "index" => "groups",
            "type" => "group",
            "id" => $id,
            "body" => [
                "doc" => [
                    "name" => $name,
                    "thumbnail" => $thumbnail,
                    "orgId" => $orgId,
                    "type" => $type
                ],
                "upsert" => [
                    "name" => $name,
                    "thumbnail" => $thumbnail,
                    "orgId" => $orgId,
                    "type" => $type
                ]
            ]
        ];
        $this->client->update($params);
    }

    public function deleteIndex($id){
        $params = [
            "index" => "groups",
            "type" => "group",
            "id" => $id
        ];
        $this->client->delete($params);
    }
}