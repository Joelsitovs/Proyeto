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
        Schema::create('file_upload_users', function (Blueprint $table) {
            $table->id();

            $table->string('folder_name');
            $table->string('new_file_name');
            $table->string('file_name');
            $table->string('url_file_stl');
            $table->string('url_file_gltf');
            $table->string('url_firmada',1000)->nullable();
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->timestamps();  // Añadir los campos de created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_upload_users');
    }
};
