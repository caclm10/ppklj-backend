<?php

namespace App\Http\Controllers;

use App\Enums\OfficeType;
use App\Models\Office;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;

class OfficeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return Response::api(
            success: true,
            status: 200,
            data: Office::latest()->get(),
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', Rule::enum(OfficeType::class)],
            'name' => ['required', 'string', 'max:255'],
        ]);

        $office = Office::create($validated);

        return Response::api(
            success: true,
            status: 201,
            message: 'Office created successfully',
            data: $office,
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Office $office): JsonResponse
    {
        return Response::api(
            success: true,
            status: 200,
            data: $office,
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Office $office): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['sometimes', 'required', Rule::enum(OfficeType::class)],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
        ]);

        $office->update($validated);

        return Response::api(
            success: true,
            status: 200,
            message: 'Office updated successfully',
            data: $office,
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Office $office): JsonResponse
    {
        $office->delete();

        return Response::api(
            success: true,
            status: 200,
            message: 'Office deleted successfully',
        );
    }
}
