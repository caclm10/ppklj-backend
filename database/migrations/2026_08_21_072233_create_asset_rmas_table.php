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
        Schema::create('asset_rmas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('pic_name');
            $table->string('pic_phone')->nullable();
            $table->string('rma_number')->nullable();
            $table->string('vendor_name')->nullable();
            $table->enum('current_status', [
                'rusak_di_kantor',
                'pengiriman_ke_pusat',
                'diterima_di_pusat',
                'pengiriman_ke_vendor',
                'diproses_vendor',
                'diterima_dari_vendor',
                'pengiriman_ke_kantor',
                'selesai_dipasang',
            ])->default('rusak_di_kantor');
            $table->enum('resolution', ['diperbaiki', 'diganti_unit'])->nullable();
            $table->string('old_serial_number')->nullable();
            $table->string('new_serial_number')->nullable();
            $table->text('problem_description')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_rmas');
    }
};
