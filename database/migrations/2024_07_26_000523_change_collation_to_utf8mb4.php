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
        DB::statement('ALTER DATABASE ' . env('DB_DATABASE') . ' CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci');

        // Đổi collation cho từng bảng
        $tables = DB::select('SHOW TABLES');
        $database = 'Tables_in_' . env('DB_DATABASE', 'gak');

        foreach ($tables as $table) {
            $tableName = $table->$database;
            DB::statement('ALTER TABLE ' . $tableName . ' CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
