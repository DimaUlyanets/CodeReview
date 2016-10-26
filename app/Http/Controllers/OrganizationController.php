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
use Illuminate\Support\Facades\Auth;

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
        if(!empty($request->icon)){
            $path = Files::qualityCompress($request->icon, "organizations/{$result->id}/icon");
            $result->icon = $path;
            $result->save();
        } else {
            $result->icon = 'https://unsplash.it/200/200'; //TODO: temporary
        }
        if(!empty($request->cover)){
            $path = Files::qualityCompress($request->cover, "organizations/{$result->id}/cover");
            $result->cover = $path;
            $result->save();
        } else {
            $result->cover = 'https://unsplash.it/200/200'; //TODO: temporary
        }
        $result->save();
        if($result){
            if($request->tags){
                $request->tags = explode(',', $request->tags);
                Tag::assignTag($result, $request);
            }
        }
        $user = Auth::guard('api')->user();
        DB::table('organization_user')->insert(
            ['user_id' => $user->id, 'organization_id' => $result->id ,'role'=>'owner']
        );
        if(isset($request['members'])){
            $addMembers = $request['members'];
            foreach ($addMembers as $id) {
                DB::table('organization_user')->insert(
                    ['user_id'=>$id,'organization_id'=>$result->id,'role'=>'member']
                );
            }
        }
        if(isset($request['admins'])){
            $addAdmins = $request['admins'];
            foreach ($addAdmins as $id) {
                DB::table('organization_user')->insert(
                    ['user_id'=>$id,'organization_id'=>$result->id,'role'=>'admin']
                );
            }
        }

        return Response::json($result->toArray(), 200);

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

        if(isset($request['tags'])){
            $request['tags'] = explode(',', $request['tags']);
            Tag::assignTag($organization, $request);
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
        return Response::json($organization->toArray(), 200);
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
