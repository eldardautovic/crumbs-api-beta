<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGroupRequest;
use App\Http\Resources\GroupResource;
use App\Models\Group;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{

    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $id = Auth::user()->id;

        return Group::with('user')->where('creatorId', $id)->get();
        
    }

    public function join(string $groupId) {

        $id = Auth::user()->id;

        Group::where('id', $groupId)->get()[0]->user()->attach($id);

        return $this->success([
            'group' => Group::with('user')->where('id', $id)->get()[0]
        ], "Successfully joined group.", 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGroupRequest $request)
    {
        $request->validated($request->all());

        $id = Auth::user()->id;

        $group = Group::create([
            'name' => $request->name,
            'creatorId' => $id
        ]);

        $group->user()->attach($id);

        return $this->success([
            'group' => $group
        ], "Successfully created a group.", 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group)
    {
        if(Auth::user()->id !== $group->creatorId) {
            return $this->error("", "Forbidden.", 403);
        } 

        $group->update($request->all());

        return new GroupResource($group);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        return $this->isNotAuthorized($group) ? $this->isNotAuthorized($group) : $group->delete();
    }

    private function isNotAuthorized($group) {
        if(Auth::user()->id !== $group->creatorId) {
            return $this->error("", "Forbidden.", 403);
        } 
    }
}
