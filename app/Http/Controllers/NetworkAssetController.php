<?php

namespace App\Http\Controllers;

use App\Enums\NetworkAssetStatus;
use App\Models\Feature;
use App\Models\NetworkAsset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;

class NetworkAssetController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = NetworkAsset::with(['asset', 'office', 'features'])->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('brand')) {
            $query->where('brand', $request->input('brand'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('office_id')) {
            $query->where('office_id', $request->input('office_id'));
        }

        return Response::api(success: true, status: 200, data: $query->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'asset_id' => ['required', 'exists:assets,id', 'unique:network_assets,asset_id'],
            'office_id' => ['nullable', 'exists:offices,id'],
            'status' => ['sometimes', 'required', Rule::enum(NetworkAssetStatus::class)],
            'brand' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'ip' => ['nullable', 'string', 'max:45'],
            'hostname' => ['nullable', 'string', 'max:255'],
            'features' => ['nullable', 'array'],
            'features.*' => ['string', 'max:255'],
        ]);

        $networkAsset = NetworkAsset::create($validated);

        if ($request->has('features')) {
            $featureIds = collect($request->input('features'))
                ->filter()
                ->map(fn ($name) => Feature::firstOrCreate(['name' => trim($name)])->id);

            $networkAsset->features()->sync($featureIds);
        }

        return Response::api(
            success: true,
            status: 201,
            message: 'Network asset created successfully',
            data: $networkAsset->load(['asset', 'office', 'features']),
        );
    }

    public function show(NetworkAsset $networkAsset): JsonResponse
    {
        return Response::api(
            success: true,
            status: 200,
            data: $networkAsset->load(['asset', 'office', 'features']),
        );
    }

    public function update(Request $request, NetworkAsset $networkAsset): JsonResponse
    {
        $validated = $request->validate([
            'office_id' => ['nullable', 'exists:offices,id'],
            'status' => ['sometimes', 'required', Rule::enum(NetworkAssetStatus::class)],
            'brand' => ['sometimes', 'required', 'string', 'max:255'],
            'model' => ['sometimes', 'required', 'string', 'max:255'],
            'type' => ['sometimes', 'required', 'string', 'max:255'],
            'ip' => ['nullable', 'string', 'max:45'],
            'hostname' => ['nullable', 'string', 'max:255'],
            'features' => ['nullable', 'array'],
            'features.*' => ['string', 'max:255'],
        ]);

        $networkAsset->update($validated);

        if ($request->has('features')) {
            $featureIds = collect($request->input('features'))
                ->filter()
                ->map(fn ($name) => Feature::firstOrCreate(['name' => trim($name)])->id);

            $networkAsset->features()->sync($featureIds);
        }

        return Response::api(
            success: true,
            status: 200,
            message: 'Network asset updated successfully',
            data: $networkAsset->load(['asset', 'office', 'features']),
        );
    }

    public function destroy(NetworkAsset $networkAsset): JsonResponse
    {
        $networkAsset->delete();

        return Response::api(
            success: true,
            status: 200,
            message: 'Network asset deleted successfully',
        );
    }
}
