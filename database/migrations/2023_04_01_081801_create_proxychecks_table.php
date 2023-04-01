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
        Schema::create('proxychecks', function (Blueprint $table) {
            $table->id();
            $table->integer('ip2country_id');
            $table->string('ip')->unique();
            $table->string('block');
            $table->string('block_reason');
            $table->text('raw');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proxychecks');
    }
};
