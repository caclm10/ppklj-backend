<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('asset_rma_tracks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_rma_id')->constrained('asset_rmas')->cascadeOnDelete();
            $table->enum('status', [
                'rusak_di_kantor',
                'pengiriman_ke_pusat',
                'diterima_di_pusat',
                'pengiriman_ke_vendor',
                'diproses_vendor',
                'diterima_dari_vendor',
                'pengiriman_ke_kantor',
                'selesai_dipasang',
            ]);
            $table->text('notes')->nullable();
            $table->timestamp('tracked_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_rma_tracks');
    }
};
