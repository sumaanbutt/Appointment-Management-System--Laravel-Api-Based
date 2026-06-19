<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiDataHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\CreateOrganizationRequest;
use App\Http\Requests\Organization\UpdateOrganizationRequest;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrganizationController extends Controller
{
    public function index(Request $request)
    {
        $query = Organization::query()
            ->with('businesses');

        if ($request->businesses_code) {
            $query->where(
                'businesses_code',
                $request->buisnesses_code
            );
        }

        $organizations = ApiDataHelper::process( $query, $request );

        return response()->json([
            'success' => true,
            'message' => 'Organizations fetched successfully',
            'data' => $organizations
        ], 200);
    }

    public function store(CreateOrganizationRequest $request)
    {
        $data = $request->validated();

        $organization = Organization::create([
            'name' => $request->name,
            'description' => $request->description,
            'status'=> $request->status ?? 'active',
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

    public function updateStatus(Request $request, Organization $organization)
    {
        $request->validate(['status'=> 'required|in:active,inactive']);

        $organization->update(['status'=> $request->status]);

        return response()->json([
            'success'=>true,
            'message'=> 'Status updated successfully',
            'data'=> $organization
        ]);
    }

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
