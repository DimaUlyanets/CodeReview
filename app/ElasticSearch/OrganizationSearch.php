<?php

namespace App\ElasticSearch;
use Elasticsearch;

class OrganizationSearch implements IElasticSearch{

    private $client;

    function __construct() {
        $this->client = Elasticsearch\ClientBuilder::create()->setHosts([env("ELASTIC_SEARCH_HOST")])->build();
    }

    public function addToIndex($id, $name, $thumbnail){
        $params = [
            "index" => "organizations",
            "type" => "organization",
            "id" => $id,
            "body" => [
                "id" => $id,
                "Name" => $name,
                "Thumbnail" => $thumbnail
            ]
        ];
        $this->client->index($params);
    }

    public function updateIndex($id, $name, $thumbnail){
        $params = [
            "index" => "organizations",
            "type" => "organization",
            "id" => $id,
            "body" => [
                "id" => $id,
                "Name" => $name,
                "Thumbnail" => $thumbnail
            ]
        ];
        $this->client->update($params);
    }

    public function deleteIndex($id){
        $params = [
            "index" => "organizations",
            "type" => "organization",
            "id" => $id
        ];
        $this->client->delete($params);
    }
}