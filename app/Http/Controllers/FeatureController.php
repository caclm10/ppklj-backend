<?php

namespace App\Http\Controllers;

use App\Models\Feature;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class FeatureController extends Controller
{
    public function index(): JsonResponse
    {
        return Response::api(
            success: true,
            status: 200,
            data: Feature::orderBy('name')->get(),
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:features,name'],
        ]);

        $feature = Feature::create(['name' => trim($validated['name'])]);

        return Response::api(
            success: true,
            status: 201,
            message: 'Feature created successfully',
            data: $feature,
        );
    }

    public function show(Feature $feature): JsonResponse
    {
        return Response::api(
            success: true,
            status: 200,
            data: $feature->load('networkAssets'),
        );
    }

    public function update(Request $request, Feature $feature): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:features,name,'.$feature->id],
        ]);

        $feature->update(['name' => trim($validated['name'])]);

        return Response::api(
            success: true,
            status: 200,
            message: 'Feature updated successfully',
            data: $feature,
        );
    }

    public function destroy(Feature $feature): JsonResponse
    {
        $feature->delete();

        return Response::api(
            success: true,
            status: 200,
            message: 'Feature deleted successfully',
        );
    }
}
