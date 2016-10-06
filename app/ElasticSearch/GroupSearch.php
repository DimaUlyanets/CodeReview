<?php

namespace App\ElasticSearch;
use Elasticsearch;

class GroupSearch implements IElasticSearch{

    private $client;

    function __construct() {

        $this->client = Elasticsearch\ClientBuilder::create()->build();

    }

    public function addToIndex($id, $name, $thumbnail)
    {
        $params = [
            'index' => 'groups',
            'type' => 'group',
            'id' => $id,
            'body' => [
                "Name" => $name,
                "Thumbnail" => $thumbnail
            ]
        ];

        $this->client->index($params);
    }

    public function updateIndex($id, $name, $thumbnail)
    {
        $params = [
            'index' => 'groups',
            'type' => 'group',
            'id' => $id,
            'body' => [
                "Name" => $name,
                "Thumbnail" => $thumbnail
            ]
        ];

        $this->client->update($params);
    }

    public function deleteIndex($id)
    {
        $params = [
            'index' => 'groups',
            'type' => 'group',
            'id' => $id
        ];

        $this->client->delete($params);
    }
}