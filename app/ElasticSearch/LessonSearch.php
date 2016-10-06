<?php

namespace App\ElasticSearch;
use Elasticsearch;

class LessonSearch implements IElasticSearch{

    private $client;

    function __construct() {

        $this->client = Elasticsearch\ClientBuilder::create()->build();

    }

    public function addToIndex($id, $name, $thumbnail)
    {
        $params = [
            'index' => 'lessons',
            'type' => 'lesson',
            'id' => $id,
            'body' => [
                "Name" => $name,
                "Thumbnail" => $thumbnail,
                "views" => 0
            ]
        ];

        $this->client->index($params);
    }

    public function updateIndex($id, $name, $thumbnail)
    {
        $params = [
            'index' => 'lessons',
            'type' => 'lesson',
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
            'index' => 'lessons',
            'type' => 'lesson',
            'id' => $id
        ];

        $this->client->delete($params);
    }
}