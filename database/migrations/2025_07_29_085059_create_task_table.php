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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->text('discreaption')->nullable();
            $table->string('list_title'); // 🔧 اضافه شد
            $table->integer('progress')->nullable(); // 🔧 اضافه شد
            $table->boolean('complete')->default(false);
            $table->foreignId('parent_id')->nullable()->constrained('tasks')->onDelete('cascade'); // 🔧 اضافه شد
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks'); // 🔧 اصلاح نام از Todo به tasks
    }
};
