<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_servicio', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50)->unique();
            $table->string('descripcion', 255)->nullable();
            $table->boolean('activo')->default(true);
        });

        Schema::create('servicios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_servicio_id')->nullable()->constrained('tipos_servicio')->nullOnDelete();
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 10, 2);
            $table->unsignedSmallInteger('tiempo_estimado')->nullable();
            $table->boolean('requiere_repuestos')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicios');
        Schema::dropIfExists('tipos_servicio');
    }
};
