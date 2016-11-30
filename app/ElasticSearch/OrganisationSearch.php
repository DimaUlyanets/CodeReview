<?php

namespace App\ElasticSearch;
use Elasticsearch;

class OrganisationSearch implements IElasticSearch{

    private $client;

    function __construct() {
        $this->client = Elasticsearch\ClientBuilder::create()->setHosts([env("ELASTIC_SEARCH_HOST")])->build();
    }

    public function addToIndex($id, $name, $thumbnail){
        $params = [
            "index" => "organisations",
            "type" => "organisation",
            "id" => $id,
            "body" => [
                "id" => $id,
                "name" => $name,
                "thumbnail" => $thumbnail
            ]
        ];
        $this->client->index($params);
    }

    public function updateIndex($id, $name, $thumbnail){
        $params = [
            "index" => "organisations",
            "type" => "organisation",
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
            "index" => "organisations",
            "type" => "organisation",
            "id" => $id
        ];
        $this->client->delete($params);
    }

}