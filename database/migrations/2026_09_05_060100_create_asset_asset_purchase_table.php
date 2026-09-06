<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('asset_asset_purchase', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_purchase_id')->constrained('asset_purchases')->cascadeOnDelete();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['asset_purchase_id', 'asset_id']);
        });

        if (Schema::hasColumn('assets', 'asset_purchase_id')) {
            $existing = DB::table('assets')
                ->whereNotNull('asset_purchase_id')
                ->select('id as asset_id', 'asset_purchase_id', 'created_at', 'updated_at')
                ->get();

            foreach ($existing as $row) {
                DB::table('asset_asset_purchase')->insertOrIgnore([
                    'asset_purchase_id' => $row->asset_purchase_id,
                    'asset_id' => $row->asset_id,
                    'created_at' => $row->created_at ?? now(),
                    'updated_at' => $row->updated_at ?? now(),
                ]);
            }

            Schema::table('assets', function (Blueprint $table) {
                $table->dropConstrainedForeignId('asset_purchase_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('assets', 'asset_purchase_id')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->foreignId('asset_purchase_id')->nullable()->constrained('asset_purchases')->nullOnDelete();
            });

            $pivots = DB::table('asset_asset_purchase')->get();
            foreach ($pivots as $pivot) {
                DB::table('assets')->where('id', $pivot->asset_id)->update([
                    'asset_purchase_id' => $pivot->asset_purchase_id,
                ]);
            }
        }

        Schema::dropIfExists('asset_asset_purchase');
    }
};
