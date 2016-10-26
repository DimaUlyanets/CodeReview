<?php

namespace App\Http\Controllers;

use App\Files;
use App\Http\Requests\OrganizationCreateRequest;
use App\Organization;
use App\Tag;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all()
    {

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(OrganizationCreateRequest $request)
    {


        $data = $request->all();

        $result = Organization::create($data);
        $result->icon = Files::qualityCompress($request->icon, "organizations/{$result->id}/icon");
        $result->save();

        if($result){

            if($request->tags){

                Tag::assignTag($result, $request);

            }

            Organization::createDefaultGroup($result);
            return Response::json($result->toArray(), 200);

        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }
    public function addMembers(Request $request, $id)
    {
           if(isset($request['userIds'])) {
               $addMembers = $request['userIds'];
               foreach ($addMembers as $idMember) {
                       DB::table('organization_user')->insert(
                           ['user_id' => $idMember, 'organization_id' => $id, 'role' => 'member']
                       );
                   }
           }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        //
    }
}
