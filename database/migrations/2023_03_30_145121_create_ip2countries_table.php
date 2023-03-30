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
        Schema::create('ip2countries', function (Blueprint $table) {
            $table->id();
            $table->string('as');
            $table->string('city');
            $table->string('country');
            $table->string('countryCode');
            $table->string('isp');
            $table->string('lat');
            $table->string('lon');
            $table->string('org');
            $table->string('query');
            $table->string('region');
            $table->string('regionName');
            $table->string('status');
            $table->string('timezone');
            $table->string('zip');
            $table->string('ip');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ip2countries');
    }
};
