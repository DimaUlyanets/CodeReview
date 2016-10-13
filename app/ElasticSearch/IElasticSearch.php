<?php

namespace App\ElasticSearch;

interface IElasticSearch
{
    public function addToIndex($id,$name,$thumbnail);
    public function updateIndex($id,$name,$thumbnail);
    public function deleteIndex($id);

}