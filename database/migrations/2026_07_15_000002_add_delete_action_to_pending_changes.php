<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ALTER ... MODIFY ... ENUM is MySQL-specific syntax and breaks on SQLite
        // (used by the test suite). Non-MySQL engines get the same effective change
        // via the schema builder, which recreates the column's constraint instead.
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE pending_changes MODIFY action ENUM('create', 'update', 'delete') NOT NULL");

            return;
        }

        Schema::table('pending_changes', function (Blueprint $table) {
            $table->enum('action', ['create', 'update', 'delete'])->change();
        });
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE pending_changes MODIFY action ENUM('create', 'update') NOT NULL");

            return;
        }

        Schema::table('pending_changes', function (Blueprint $table) {
            $table->enum('action', ['create', 'update'])->change();
        });
    }
};
