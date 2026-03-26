<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table): void {
            $table->string('position')->nullable()->after('email');
            $table->string('department')->nullable()->after('position');
            $table->string('contact_number', 50)->nullable()->after('department');
        });
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table): void {
            $table->dropColumn(['position', 'department', 'contact_number']);
        });
    }
};
