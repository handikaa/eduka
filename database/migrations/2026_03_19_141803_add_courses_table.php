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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('instructor_id');
            $table->string('title', 100);
            $table->string('slug', 100);
            $table->text('description');
            $table->enum('level', ['beginner', 'intermediate', 'advanced']);
            $table->bigInteger('price');
            $table->string('thumbnail_url')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->integer('quota');
            $table->integer('enrolled_count')->default(0);
            $table->integer('rating_count')->default(0);
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->foreign('instructor_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
