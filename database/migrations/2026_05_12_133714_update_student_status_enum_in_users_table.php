<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }
        DB::statement("ALTER TABLE users MODIFY COLUMN student_status ENUM('active','inactive','cuti','alumni','student aktif','student non aktif','student cuti','alumni aktif','alumni non aktif','alumni cuti') DEFAULT 'active'");
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }
        DB::statement("ALTER TABLE users MODIFY COLUMN student_status ENUM('active','inactive','cuti','alumni') DEFAULT 'active'");
    }
};
