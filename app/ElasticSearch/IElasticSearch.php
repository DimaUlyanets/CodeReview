<?php

namespace App\ElasticSearch;

interface IElasticSearch
{
    public function addToIndex($id,$name,$thumbnail, $orgId, $type);
    public function updateIndex($id,$name,$thumbnail, $orgId, $type);
    public function deleteIndex($id);

}