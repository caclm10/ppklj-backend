<?php

namespace App\Http\Controllers;

use App\Enums\PurchaseType;
use App\Models\Purchase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;

class PurchaseController extends Controller
{
    public function index(): JsonResponse
    {
        return Response::api(success: true, status: 200, data: Purchase::latest('year')->latest('id')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', Rule::enum(PurchaseType::class)],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'description' => ['nullable', 'string'],
        ]);

        return Response::api(
            success: true,
            status: 201,
            message: 'Purchase created successfully',
            data: Purchase::create($validated),
        );
    }

    public function show(Purchase $purchase): JsonResponse
    {
        return Response::api(success: true, status: 200, data: $purchase);
    }

    public function update(Request $request, Purchase $purchase): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['sometimes', 'required', Rule::enum(PurchaseType::class)],
            'year' => ['sometimes', 'required', 'integer', 'min:2000', 'max:2100'],
            'description' => ['nullable', 'string'],
        ]);

        $purchase->update($validated);

        return Response::api(
            success: true,
            status: 200,
            message: 'Purchase updated successfully',
            data: $purchase,
        );
    }

    public function destroy(Purchase $purchase): JsonResponse
    {
        $purchase->delete();

        return Response::api(success: true, status: 200, message: 'Purchase deleted successfully');
    }
}
