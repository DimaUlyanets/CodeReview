<?php

namespace App\Http\Controllers;

use App\ElasticSearch\ElasticSearch;
use Illuminate\Http\Request;

use App\Http\Requests;

class SearchController extends Controller
{
    public function fullSearch(Request $request){

        $this->validate($request, ['itemToSearch' => 'required']);
        $itemToSearch = $request->itemToSearch;
        $search = new ElasticSearch();

        return  $search->fullSearch($itemToSearch);
    }
    public function quickSearch(Request $request){

        $this->validate($request, ['itemToSearch' => 'required']);
        $itemToSearch = $request->itemToSearch;
        $search = new ElasticSearch();

        return  $search->quickSearch($itemToSearch);
    }
}
