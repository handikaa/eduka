<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE lessons MODIFY type ENUM('video', 'content', 'file') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE lessons MODIFY type ENUM('video', 'text', 'file') NOT NULL");
    }
};