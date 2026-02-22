<?php

use App\Models\Theater;
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
        Schema::create('screens', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Theater::class)->constrained()->cascadeOnDelete();
            $table->integer('label');
            $table->integer('regular_seats');
            $table->integer('couples_seats');
            $table->integer('disability_seats');
            $table->integer('vip_seats');
            $table->integer('royal_seats');
            $table->integer('total_seats');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('screens');
    }
};
