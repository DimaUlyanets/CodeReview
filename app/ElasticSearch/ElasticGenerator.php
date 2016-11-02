<?php

namespace App\ElasticSearch;

use App\Classes;
use App\Group;
use App\Lesson;
use Elasticsearch;

class ElasticGenerator{
    
    private $client;

    function __construct(){
        $this->client = Elasticsearch\ClientBuilder::create()->setHosts([env("ELASTIC_SEARCH_HOST")])->build();
    }

    public function addClassesToSearch(){
        $classes = Classes::all();
        foreach ($classes as $class) {
            $classId = $class->id;
            $className = $class->name;
            $classThumbnail = $class->thumbnail;
            $classUsers = [];
            foreach ($class->users as $user) {
                $classUserName = $user->name;
                array_push($classUsers, $classUserName);
            }
            $group= Group::find($class->group_id);
            $classOrganizationId = $group->organization_id;
            $params = [
                "index" => "classes",
                "type" => "class",
                "id" => $classId,
                "body" => [
                    "Name" => $className,
                    "Thumbnail" => $classThumbnail,
                    "Users" => $classUsers,
                    'OrganizationId'=>$classOrganizationId
                ]
            ];
          $this->client->index($params);
        }
    }
    public function addGroupsToSearch(){
        $groups = Group::all();
        foreach ($groups as $group) {
            if (!$group->default) {
                $groupId = $group->id;
                $groupName = $group->name;
                $groupThumbnail = $group->thumbnail;
                $groupOrganizationId =$group->organization_id;
                $groupUsers = [];
                foreach ($group->users as $user) {
                    $groupUserName = $user->name;
                    array_push($groupUsers, $groupUserName);
                }
                $params = [
                    "index" => "groups",
                    "type" => "group",
                    "id" => $groupId,
                    "body" => [
                        "Name" => $groupName,
                        "Thumbnail" => $groupThumbnail,
                        "Users" => $groupUsers,
                        'OrganizationId'=>$groupOrganizationId
                    ]
                ];
              $this->client->index($params);
        }
        }
    }
    public function addLessonsToSearch(){
        $lessons = Lesson::all();
        foreach ($lessons as $lesson) {
            $lessonId = $lesson->id;
            $lessonName = $lesson->name;
            $lessonThumbnail = $lesson->thumbnail;
            $group= Group::find($lesson->group_id);
            $lessonOrganizationId = $group->organization_id;
            $params = [
                "index" => "lessons",
                "type" => "lesson",
                "id" => $lessonId,
                "body" => [
                    "Name" => $lessonName,
                    "Thumbnail" => $lessonThumbnail,
                    "views" => $lesson->views,
                    'OrganizationId'=>$lessonOrganizationId
                ]
            ];
          $this->client->index($params);
        }
    }

    public function clearIndices() {
        $deleteParams = [
            'index' => '_all'
        ];
        $response = $this->client->indices()->delete($deleteParams);
    }
    
}