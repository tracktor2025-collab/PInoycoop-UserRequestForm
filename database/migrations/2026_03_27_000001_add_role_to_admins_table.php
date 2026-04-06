<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table): void {
            $table->string('role', 50)->default('admin')->after('password');
        });

        DB::table('admins')
            ->whereNull('role')
            ->orWhere('role', '')
            ->update(['role' => 'admin']);
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table): void {
            $table->dropColumn('role');
        });
    }
};
