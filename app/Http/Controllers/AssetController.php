<?php

namespace App\Http\Controllers;

use App\Enums\AssetCategory;
use App\Models\Asset;
use App\Models\AssetPurchase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;

class AssetController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Asset::with(['assetPurchases.purchase', 'networkAsset'])->latest();

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('asset_purchase_id')) {
            $query->whereHas('assetPurchases', fn ($q) => $q->where('asset_purchases.id', $request->input('asset_purchase_id')));
        }

        return Response::api(success: true, status: 200, data: $query->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'asset_purchase_id' => ['nullable', 'exists:asset_purchases,id'],
            'category' => ['required', Rule::enum(AssetCategory::class)],
            'name' => ['required', 'string', 'max:255'],
            'number' => ['required', 'string', 'max:255'],
            'unit_price' => ['nullable', 'integer', 'min:0'],
            'end_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        if (empty($validated['end_date']) && ! empty($validated['asset_purchase_id'])) {
            $assetPurchase = AssetPurchase::find($validated['asset_purchase_id']);
            $validated['end_date'] = $assetPurchase?->end_date?->format('Y-m-d');
        }

        $assetPurchaseId = $validated['asset_purchase_id'] ?? null;
        unset($validated['asset_purchase_id']);

        $asset = Asset::create($validated);

        if ($assetPurchaseId) {
            $asset->assetPurchases()->syncWithoutDetaching([$assetPurchaseId]);
        }

        return Response::api(
            success: true,
            status: 201,
            message: 'Asset created successfully',
            data: $asset->load('assetPurchases.purchase'),
        );
    }

    public function show(Asset $asset): JsonResponse
    {
        return Response::api(
            success: true,
            status: 200,
            data: $asset->load(['assetPurchases.purchase', 'networkAsset']),
        );
    }

    public function update(Request $request, Asset $asset): JsonResponse
    {
        $validated = $request->validate([
            'asset_purchase_id' => ['nullable', 'exists:asset_purchases,id'],
            'category' => ['sometimes', 'required', Rule::enum(AssetCategory::class)],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'number' => ['sometimes', 'required', 'string', 'max:255'],
            'unit_price' => ['nullable', 'integer', 'min:0'],
            'end_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        if (array_key_exists('asset_purchase_id', $validated)) {
            if (! empty($validated['asset_purchase_id'])) {
                $asset->assetPurchases()->syncWithoutDetaching([$validated['asset_purchase_id']]);
            }
            unset($validated['asset_purchase_id']);
        }

        $asset->update($validated);

        return Response::api(
            success: true,
            status: 200,
            message: 'Asset updated successfully',
            data: $asset->load('assetPurchases.purchase'),
        );
    }

    public function destroy(Asset $asset): JsonResponse
    {
        $asset->delete();

        return Response::api(success: true, status: 200, message: 'Asset deleted successfully');
    }
}
