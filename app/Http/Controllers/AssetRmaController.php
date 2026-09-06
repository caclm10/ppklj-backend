<?php

namespace App\Http\Controllers;

use App\Enums\NetworkAssetStatus;
use App\Enums\RmaResolution;
use App\Enums\RmaStatus;
use App\Models\Asset;
use App\Models\AssetRma;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;

class AssetRmaController extends Controller
{
    /**
     * Display a listing of RMA tickets.
     */
    public function index(Request $request): JsonResponse
    {
        $query = AssetRma::with(['asset', 'user', 'tracks'])->latest();

        if ($request->filled('asset_id')) {
            $query->where('asset_id', $request->input('asset_id'));
        }

        if ($request->filled('current_status')) {
            $query->where('current_status', $request->input('current_status'));
        }

        if ($request->filled('resolution')) {
            $query->where('resolution', $request->input('resolution'));
        }

        return Response::api(
            success: true,
            status: 200,
            data: $query->get(),
        );
    }

    /**
     * Store a newly created RMA ticket and its initial tracking record.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'asset_id' => ['required', 'exists:assets,id'],
            'pic_name' => ['required', 'string', 'max:255'],
            'pic_phone' => ['nullable', 'string', 'max:50'],
            'rma_number' => ['nullable', 'string', 'max:255'],
            'vendor_name' => ['nullable', 'string', 'max:255'],
            'current_status' => ['sometimes', 'required', Rule::enum(RmaStatus::class)],
            'problem_description' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $asset = Asset::with('networkAsset')->findOrFail($validated['asset_id']);
        $status = $validated['current_status'] ?? RmaStatus::RusakDiKantor->value;

        $rma = AssetRma::create([
            'asset_id' => $asset->id,
            'user_id' => $request->user()?->id,
            'pic_name' => $validated['pic_name'],
            'pic_phone' => $validated['pic_phone'] ?? null,
            'rma_number' => $validated['rma_number'] ?? null,
            'vendor_name' => $validated['vendor_name'] ?? null,
            'current_status' => $status,
            'old_serial_number' => $asset->number,
            'problem_description' => $validated['problem_description'] ?? null,
        ]);

        $rma->tracks()->create([
            'status' => $status,
            'notes' => $validated['notes'] ?? 'Laporan kerusakan RMA diajukan.',
            'tracked_at' => now(),
        ]);

        $asset->networkAsset?->update(['status' => NetworkAssetStatus::TidakAktif]);

        return Response::api(
            success: true,
            status: 201,
            message: 'RMA ticket created successfully',
            data: $rma->load(['asset', 'user', 'tracks']),
        );
    }

    /**
     * Display the specified RMA ticket with tracking history.
     */
    public function show(AssetRma $assetRma): JsonResponse
    {
        return Response::api(
            success: true,
            status: 200,
            data: $assetRma->load(['asset', 'user', 'tracks']),
        );
    }

    /**
     * Update metadata on the RMA ticket.
     */
    public function update(Request $request, AssetRma $assetRma): JsonResponse
    {
        $validated = $request->validate([
            'pic_name' => ['sometimes', 'required', 'string', 'max:255'],
            'pic_phone' => ['nullable', 'string', 'max:50'],
            'rma_number' => ['nullable', 'string', 'max:255'],
            'vendor_name' => ['nullable', 'string', 'max:255'],
            'problem_description' => ['nullable', 'string'],
        ]);

        $assetRma->update($validated);

        return Response::api(
            success: true,
            status: 200,
            message: 'RMA ticket updated successfully',
            data: $assetRma->load(['asset', 'user', 'tracks']),
        );
    }

    /**
     * Add a new tracking progression milestone to the RMA ticket.
     */
    public function addTrack(Request $request, AssetRma $assetRma): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::enum(RmaStatus::class)],
            'notes' => ['nullable', 'string'],
            'tracked_at' => ['nullable', 'date'],
            'resolution' => [
                'nullable',
                Rule::requiredIf(fn () => $request->input('status') === RmaStatus::SelesaiDipasang->value),
                Rule::enum(RmaResolution::class),
            ],
            'new_serial_number' => [
                'nullable',
                Rule::requiredIf(fn () => $request->input('resolution') === RmaResolution::DigantiUnit->value),
                'string',
                'max:255',
            ],
        ]);

        $assetRma->tracks()->create([
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
            'tracked_at' => $validated['tracked_at'] ?? now(),
        ]);

        $assetRma->current_status = $validated['status'];

        if ($validated['status'] === RmaStatus::SelesaiDipasang->value) {
            $assetRma->resolution = $validated['resolution'];
            $assetRma->completed_at = now();

            if ($validated['resolution'] === RmaResolution::DigantiUnit->value) {
                $assetRma->new_serial_number = $validated['new_serial_number'];
                $assetRma->asset->update(['number' => $validated['new_serial_number']]);
            }

            $assetRma->asset->networkAsset?->update(['status' => NetworkAssetStatus::Aktif]);
        }

        $assetRma->save();

        return Response::api(
            success: true,
            status: 201,
            message: 'RMA tracking status updated successfully',
            data: $assetRma->fresh()->load(['asset', 'user', 'tracks']),
        );
    }

    /**
     * Remove the specified RMA ticket.
     */
    public function destroy(AssetRma $assetRma): JsonResponse
    {
        $assetRma->delete();

        return Response::api(
            success: true,
            status: 200,
            message: 'RMA ticket deleted successfully',
        );
    }
}
