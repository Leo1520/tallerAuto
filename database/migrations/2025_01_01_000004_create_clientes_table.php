<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persona_id')->unique()->constrained('persona')->cascadeOnDelete();
            $table->string('direccion', 255)->nullable();
            $table->string('ciudad', 50)->nullable();
            $table->string('tipo_documento', 20)->nullable();
            $table->string('numero_documento', 50)->nullable()->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
