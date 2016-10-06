<?php

use App\Classes;
use App\Group;
use App\Lesson;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () {
    $client = Elasticsearch\ClientBuilder::create()->build();


//  echo   LessonSearch::addToIndex();

//
//    $classes = Classes::all();
//
//    foreach ($classes as $class) {
//        $classId = $class->id;
//        $className = $class->name;
//        $classThumbnail = $class->thumbnail;
//
//        $classUsers = [];
//        foreach ($class->users as $user) {
//            $classUserName = $user->name;
//            array_push($classUsers, $classUserName);
//
//        }
//        $params = [
//            'index' => 'classes',
//            'type' => 'class',
//            'id' => $classId,
//            'body' => [
//                "className" => $className,
//                "classThumbnail" => $classThumbnail,
//                "classUsers" => $classUsers
//            ]
//        ];
//
//      $response = $client->index($params);
//        dd($response);
//
//
//    }

//======================================================================
//          Create object for work with  mega lib

// url:https://www.elastic.co/guide/en/elasticsearch/client/php-api/current/_quickstart.html#_index_a_document
//======================================================================



//======================================================================
//                       Index a document  (EXAMPLE)
//======================================================================

//      $params = [
//          'index' => 'my_index',
//          'type' => 'my_type',
//          'id' => 'my_id',
//          'body' => ['testField' => 'abc']
//      ];
//
//      $response = $client->index($params);
//      dd($response);
//======================================================================
//                      MY TEST
//======================================================================


//    $Godfather = [
//        'index' => 'movies',
//        'type' => 'movie',
//        'id' => '1',
//        'body' => [
//            "title" => "The Godfather",
//            "director" => "Francis Ford Coppola",
//            "year" => 1972,
//            "genres" => ["Crime", "Drama"]
//        ]
//    ];

//
//    $Lawrence = [
//        'index' => 'movies',
//        'type' => 'movie',
//        'id' => '2',
//        'body' => [
//            "title" => "Lawrence of Arabia",
//            "director" => "David Lean",
//            "year" => 1962,
//            "genres" => ["Adventure", "Biography", "Drama"]
//        ]
//    ];
//    $ToKillaMockingbird = [
//        'index' => 'movies',
//        'type' => 'movie',
//        'id' => '3',
//        'body' => [
//            "title" => "To Kill a Mockingbird",
//            "director" => "Robert Mulligan",
//            "year" => 1962,
//            "genres" => ["Crime", "Drama", "Mystery"]
//        ]
//    ];
//    $Apocalypse = [
//        'index' => 'movies',
//        'type' => 'movie',
//        'id' => '4',
//        'body' => [
//            "title" => "Apocalypse Now",
//            "director" => "Francis Ford Coppola",
//            "year" => 1979,
//            "genres" => ["Drama", "War"]
//        ]
//    ];


//        $ololo = [
//        'index' => 'tests',
//        'type' => 'test',
//        'id' => '1',
//        'body' => [
//            "title" => "blabla",
//            "director" => "zazazaza",
//            "year" => 1979,
//            "genres" => ["Drama", "War"]
//        ]
//    ];
//        $response = $client->index($ololo);
//        dd($response);


//
//    $response = $client->index($Godfather);
//    dd($response);
//    $response = $client->index($Lawrence);
//    dd($response);
//    $response = $client->index($ToKillaMockingbird);
//    dd($response);
//    $response = $client->index($Apocalypse);
//    dd($response);

//======================================================================
//                     Get a document
//======================================================================

//      $params = [
//          'index' => 'my_index',
//          'type' => 'my_type',
//          'id' => 'my_id'
//      ];
//
//      $response = $client->get($params);
//      dd($response);

//======================================================================
//              MY TEST   params(index,type,id) requried!
//======================================================================

//    $params = [
//        'index' => 'movies',
//        'type' => 'movie',
//        'id' => '4'
//    ];
//
//    $response = $client->get($params);
//    dd($response);


//======================================================================
//                Search for a document
//======================================================================

      $params = [
          'index' => 'groups',
          'type' => 'group',
          'body' => [
              'query' => [
                  'match' => [
                      'groupName' => 'WORKERS'
                  ]
              ]
          ]
      ];



      $response = $client->search($params);
      dd($response);




    //======================================================================
    //  WOW WOW really??
    //======================================================================
//
//      $params = [
//          'index' => 'movies',
//          'type' => 'movie',
//          'body' => [
//              'query' => [
//                  'match' => [
//                      'year' => '1979',
//
//                  ]
//              ]
//          ]
//      ];
//
//      $response = $client->search($params);
//      dd($response);


//======================================================================
//                Delete a document
//======================================================================

//      $params = [
//          'index' => 'classes',
//          'type' => 'class',
//          'id' => '1'
//      ];
//
//     $response = $client->delete($params);
//      dd($response);


//======================================================================
//                Delete an index
//======================================================================

//      $deleteParams = [
//          'index' => 'classes'
//      ];
//      $response = $client->indices()->delete($deleteParams);
//      dd($response);


//======================================================================
//                Create an index
//======================================================================

//      $params = [
//          'index' => 'my_index',
//          'body' => [
//              'settings' => [
//                  'number_of_shards' => 2,
//                  'number_of_replicas' => 0
//              ]
//          ]
//      ];
//
//      $response = $client->indices()->create($params);
//      dd($response);


//      return view('welcome');
});

Auth::routes();
Route::get('/home', 'HomeController@index');


