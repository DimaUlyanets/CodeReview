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


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $organization = Organization::find($id);
        isset($request['name'])?$organization->name = $request['name']:"";
        isset($request['logo'])?$organization->icon = $request['logo']:"";
        isset($request['cover'])?$organization->cover = $request['cover']:"";
        isset($request['description'])?$organization->description = $request['description']:"";

        if(isset($request['topics'])){
          $idTag = DB::table('tags')->insertGetId(
                ['name' => $request['topics']]
            );
            DB::table('organization_tag')->insert(
                ['organization_id' => $id, 'tag_id' => $idTag]
            );
        }

        $organization->save();
        if(isset($request['addAdmins'])){
           $addAdmins = $request['addAdmins'];
            foreach ($addAdmins as $id) {
                DB::table('organization_user')->where('user_id',$id)->update(
                    ['role' => "admin"]
                );
            }
       }
        if(isset($request['removeAdmins'])){
            $removeAdmins  = $request['removeAdmins'];
            foreach ($removeAdmins as $id) {
                DB::table('organization_user')->where('user_id',$id)->update(
                    ['role' => null]
                );
            }
        }
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
