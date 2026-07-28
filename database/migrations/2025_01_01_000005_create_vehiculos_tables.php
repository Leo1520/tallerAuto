<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marcas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50)->unique();
            $table->string('pais', 50)->nullable();
            $table->boolean('activo')->default(true);
        });

        Schema::create('modelos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marca_id')->constrained('marcas')->cascadeOnDelete();
            $table->string('nombre', 50);
            $table->boolean('activo')->default(true);

            $table->unique(['marca_id', 'nombre']);
        });

        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('modelo_id')->constrained('modelos');
            $table->string('placa', 10)->unique();
            $table->string('vin', 17)->unique();
            $table->unsignedSmallInteger('ano');
            $table->string('color', 30)->nullable();
            $table->unsignedInteger('kilometraje')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index('cliente_id');
        });

        Schema::create('mantenimientos_preventivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehiculo_id')->constrained('vehiculos')->cascadeOnDelete();
            $table->string('tipo', 100)->nullable();
            $table->date('fecha_ultimo')->nullable();
            $table->date('proxima_fecha')->nullable();
            $table->unsignedInteger('kilometraje_ultimo')->nullable();
            $table->unsignedInteger('kilometraje_proximo')->nullable();
            $table->boolean('notificar')->default(true);
            $table->string('estado', 20)->default('Proximo');
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index('proxima_fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mantenimientos_preventivos');
        Schema::dropIfExists('vehiculos');
        Schema::dropIfExists('modelos');
        Schema::dropIfExists('marcas');
    }
};
