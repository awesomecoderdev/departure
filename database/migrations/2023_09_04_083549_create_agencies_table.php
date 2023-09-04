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
        Schema::create('agencies', function (Blueprint $table) {
            $table->id();
            $table->string("first_name");
            $table->string("last_name")->nullable();
            $table->string("email")->unique();
            $table->string("password");
            $table->string("phone", 15)->unique();
            $table->string("city")->nullable();
            $table->string("country")->nullable();

            $table->string("agency_name")->nullable();
            $table->string("agency_phone", 15)->unique();
            $table->string("agency_email")->unique();

            $table->text("thumbnail")->nullable();
            $table->string("image")->nullable();
            $table->text("metadata")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agencies');
    }
};
