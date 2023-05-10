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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->integer("monto");
            $table->boolean("anulada")->default(false);
            $table->string("codigo_seg");
            $table->unsignedBigInteger('usuario_id')->default(0);
            $table->timestamps();
        });

        
        Schema::table('ventas', function (Blueprint $table) {
            $table->unsignedBigInteger('dispositivo_id');
            $table->foreign('dispositivo_id')->references('id')->on('dispositivos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
