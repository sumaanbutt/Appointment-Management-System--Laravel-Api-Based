<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Location\UpdateBusinessLocationRequest;
use App\Http\Requests\Organization\CreateOrganizationRequest;
use App\Http\Requests\Organization\UpdateOrganizationRequest;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrganizationController extends Controller
{
    public function index()
    {
        $organizations = Organization::with('businesses')->latest()->paginate(10);
        return response()->json($organizations, 200);
    }

    public function store(CreateOrganizationRequest $request)
    {
        $data = $request->validated();

        $organization = Organization::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Organization created successfully',
            'data' => $organization
        ], 200);
    }
    public function show(Organization $organization)
    {
        $organization->load('businesses');

        if(!$organization){
            return response()->json([
                'success' => false,
                'message' => 'Organization not found'
            ],404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Organization retrieved successfully',
            'data' => $organization,
        ], 200);
    }

    public function update(UpdateOrganizationRequest $request, Organization $organization)
    {
        $data = $request->validated();

        $organization->load('businesses');

        if(!$organization){
            return response()->json([
                'success' => false,
                'message' => 'Organization not found'
            ],404);
        }

        /*if ($data->fails()) {

            return response()->json([
                'success' => false,
                'errors' => $data->errors()
            ], 422);
        }*/

        $organization->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Organization updated successfully',
            'data' => $organization
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organization $organization)
    {
        $organization->load('businesses');
        if(!$organization){
            return response()->json([
                'success' => false,
                'message' => 'Organization not found'
            ],404);
        }
        if($organization->businesses()->count() > 0){
            return response()->json([
                'success' => false,
                'message' => 'Organization can not be deleted because it has businesses associated with it'
            ],422);
        }
        $organization->delete();
        return response()->json([
            'success' => true,
            'message' => 'Organization deleted successfully'
        ],200);
    }
}
