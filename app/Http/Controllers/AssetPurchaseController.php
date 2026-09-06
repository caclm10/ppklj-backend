<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetPurchase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class AssetPurchaseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = AssetPurchase::with(['purchase', 'assets.networkAsset'])->withCount('assets')->latest();

        if ($request->filled('purchase_id')) {
            $query->where('purchase_id', $request->input('purchase_id'));
        }

        if ($request->filled('asset_id')) {
            $query->whereHas('assets', fn ($q) => $q->where('assets.id', $request->input('asset_id')));
        }

        return Response::api(success: true, status: 200, data: $query->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'purchase_id' => ['required', 'exists:purchases,id'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['nullable', 'integer', 'min:0'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'asset_ids' => ['nullable', 'array'],
            'asset_ids.*' => ['exists:assets,id'],
        ]);

        $assetPurchase = AssetPurchase::create($validated);

        if (! empty($validated['asset_ids'])) {
            $assetPurchase->assets()->syncWithoutDetaching($validated['asset_ids']);

            if (! empty($validated['end_date'])) {
                Asset::whereIn('id', $validated['asset_ids'])->update([
                    'end_date' => $validated['end_date'],
                ]);
            }
        }

        return Response::api(
            success: true,
            status: 201,
            message: 'Asset purchase recorded successfully',
            data: $assetPurchase->load(['purchase', 'assets']),
        );
    }

    public function show(AssetPurchase $assetPurchase): JsonResponse
    {
        return Response::api(
            success: true,
            status: 200,
            data: $assetPurchase->load(['purchase', 'assets.networkAsset']),
        );
    }

    public function update(Request $request, AssetPurchase $assetPurchase): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'price' => ['nullable', 'integer', 'min:0'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'asset_ids' => ['nullable', 'array'],
            'asset_ids.*' => ['exists:assets,id'],
        ]);

        $assetPurchase->update($validated);

        if (array_key_exists('asset_ids', $validated)) {
            $assetPurchase->assets()->sync($validated['asset_ids'] ?? []);
        }

        if (! empty($validated['end_date'])) {
            $assetPurchase->assets()->update([
                'end_date' => $validated['end_date'],
            ]);
        }

        return Response::api(
            success: true,
            status: 200,
            message: 'Asset purchase updated successfully',
            data: $assetPurchase->load(['purchase', 'assets']),
        );
    }

    public function destroy(AssetPurchase $assetPurchase): JsonResponse
    {
        $assetPurchase->delete();

        return Response::api(success: true, status: 200, message: 'Asset purchase deleted successfully');
    }
}
