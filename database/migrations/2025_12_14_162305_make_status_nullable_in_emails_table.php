<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('emails')) {
            if (Schema::hasColumn('emails', 'status')) {
                // Alter existing column to be nullable
                // Use raw SQL as Laravel Schema builder change() requires doctrine/dbal
                $driver = DB::getDriverName();
                
                if ($driver === 'mysql') {
                    DB::statement('ALTER TABLE `emails` MODIFY COLUMN `status` VARCHAR(255) NULL');
                } elseif ($driver === 'sqlite') {
                    // SQLite doesn't support MODIFY, need to recreate table
                    // For SQLite, this is usually handled differently
                    // Skip for SQLite as it's more complex
                } else {
                    // For other databases, try the standard approach
                    Schema::table('emails', function (Blueprint $table) {
                        $table->string('status')->nullable()->change();
                    });
                }
            } else {
                // Column doesn't exist, add it as nullable for backward compatibility
                Schema::table('emails', function (Blueprint $table) {
                    $table->string('status')->nullable();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: We don't change it back to NOT NULL to avoid breaking things
        // If you need to reverse, create a separate migration
    }
};
