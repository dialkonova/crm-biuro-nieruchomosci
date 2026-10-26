<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('phone', 30);
            $table->string('phone_normalized', 20);
            $table->string('phone_reversed', 20);
            $table->string('email', 255)->nullable();
            $table->text('comment')->nullable();
            $table->foreignId('owner_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['phone_normalized', 'deleted_at']);
            $table->index(['phone_reversed', 'deleted_at']);
            $table->index(['last_name', 'first_name']);
            $table->index('email');
            $table->index('owner_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};