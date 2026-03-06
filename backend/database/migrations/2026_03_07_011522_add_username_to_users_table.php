<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Add username column (nullable first so we can populate it)
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->after('id');
        });

        // Step 2: Populate username for existing users
        // Mahasiswa: username = NIM
        DB::statement("
            UPDATE users u
            INNER JOIN mahasiswa m ON m.user_id = u.id
            SET u.username = m.nim
            WHERE u.role = 'mahasiswa' AND u.username IS NULL
        ");

        // Dosen: username = NIP
        DB::statement("
            UPDATE users u
            INNER JOIN dosen d ON d.user_id = u.id
            SET u.username = d.nip
            WHERE u.role = 'dosen' AND u.username IS NULL
        ");

        // Admin/Staff/SuperAdmin: username = email (or id as fallback)
        DB::statement("
            UPDATE users
            SET username = COALESCE(email, CONCAT('user_', id))
            WHERE username IS NULL
        ");

        // Step 3: Make username unique and not nullable
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable(false)->change();
        });

        // Step 4: Make email nullable and drop unique
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
        });

        // Drop unique index on email (if exists)
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique(['email']);
            });
        } catch (\Exception $e) {
            // Index might not exist or have a different name
            try {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropIndex('users_email_unique');
                });
            } catch (\Exception $e) {
                // Already dropped or doesn't exist
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
            $table->string('email')->nullable(false)->change();
            $table->unique('email');
        });
    }
};
