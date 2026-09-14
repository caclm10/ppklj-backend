<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetPurchase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;

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

        if (! empty($validated['asset_ids'])) {
            $this->validateLicenseQuantity($validated['asset_ids'], $validated['quantity'] ?? 1);
        }

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

        if (array_key_exists('asset_ids', $validated)) {
            $this->validateLicenseQuantity($validated['asset_ids'] ?? [], $validated['quantity'] ?? $assetPurchase->quantity);
        }

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

    /**
     * Ensure a license maintenance quantity does not exceed its source package.
     *
     * Hardware quantities are based on selected physical assets and are not capped here.
     *
     * @param  array<int, int>  $assetIds
     */
    private function validateLicenseQuantity(array $assetIds, int $quantity): void
    {
        $licenseAssets = Asset::query()
            ->whereIn('id', $assetIds)
            ->where('category', 'license')
            ->with('assetPurchases')
            ->get();

        if ($licenseAssets->isEmpty()) {
            return;
        }

        $sourceQuantities = $licenseAssets
            ->map(fn (Asset $asset): ?int => $asset->assetPurchases->max('quantity'))
            ->filter();

        if ($sourceQuantities->isNotEmpty() && $quantity > $sourceQuantities->min()) {
            throw ValidationException::withMessages([
                'quantity' => 'Quantity lisensi tidak boleh melebihi quantity paket pengadaan asal.',
            ]);
        }
    }

    public function destroy(AssetPurchase $assetPurchase): JsonResponse
    {
        $assetPurchase->delete();

        return Response::api(success: true, status: 200, message: 'Asset purchase deleted successfully');
    }
}
