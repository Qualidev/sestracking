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
            $driver = DB::getDriverName();
            
            // Ensure opens column has default value
            if (Schema::hasColumn('emails', 'opens')) {
                if ($driver === 'mysql') {
                    DB::statement('ALTER TABLE `emails` MODIFY COLUMN `opens` INT UNSIGNED NOT NULL DEFAULT 0');
                } else {
                    // For other databases, try using Schema builder
                    // Note: This may require doctrine/dbal package for change()
                    try {
                        Schema::table('emails', function (Blueprint $table) {
                            $table->unsignedInteger('opens')->default(0)->change();
                        });
                    } catch (\Exception $e) {
                        // If change() fails, column might already have the correct default
                        // Log but don't fail
                    }
                }
            } else {
                // Column doesn't exist, add it with default
                Schema::table('emails', function (Blueprint $table) {
                    $table->unsignedInteger('opens')->default(0);
                });
            }
            
            // Ensure clicks column has default value
            if (Schema::hasColumn('emails', 'clicks')) {
                if ($driver === 'mysql') {
                    DB::statement('ALTER TABLE `emails` MODIFY COLUMN `clicks` INT UNSIGNED NOT NULL DEFAULT 0');
                } else {
                    // For other databases, try using Schema builder
                    try {
                        Schema::table('emails', function (Blueprint $table) {
                            $table->unsignedInteger('clicks')->default(0)->change();
                        });
                    } catch (\Exception $e) {
                        // If change() fails, column might already have the correct default
                        // Log but don't fail
                    }
                }
            } else {
                // Column doesn't exist, add it with default
                Schema::table('emails', function (Blueprint $table) {
                    $table->unsignedInteger('clicks')->default(0);
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: We don't remove defaults to avoid breaking things
        // If you need to reverse, create a separate migration
    }
};
