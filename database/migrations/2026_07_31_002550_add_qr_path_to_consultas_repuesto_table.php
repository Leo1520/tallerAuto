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
        Schema::table('consultas_repuesto', function (Blueprint $table) {
            $table->string('qr_path', 500)->nullable()->after('token');
        });
    }

    public function down(): void
    {
        Schema::table('consultas_repuesto', function (Blueprint $table) {
            $table->dropColumn('qr_path');
        });
    }
};
