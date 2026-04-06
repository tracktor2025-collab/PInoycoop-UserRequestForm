<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('access_requests', function (Blueprint $table): void {
            $table->string('coop_name')->nullable()->after('mobile_no');
            $table->string('branch')->nullable()->after('coop_name');
            $table->string('approval_signed_path')->nullable()->after('pdf_path');
        });

        if (Schema::hasColumn('access_requests', 'coop_name_branch')) {
            DB::table('access_requests')
                ->whereNotNull('coop_name_branch')
                ->orderBy('id')
                ->chunkById(200, function ($rows): void {
                    foreach ($rows as $row) {
                        DB::table('access_requests')
                            ->where('id', $row->id)
                            ->update(['coop_name' => (string) $row->coop_name_branch]);
                    }
                });

            Schema::table('access_requests', function (Blueprint $table): void {
                $table->dropColumn('coop_name_branch');
            });
        }
    }

    public function down(): void
    {
        Schema::table('access_requests', function (Blueprint $table): void {
            $table->string('coop_name_branch')->nullable()->after('mobile_no');
        });

        DB::table('access_requests')
            ->orderBy('id')
            ->chunkById(200, function ($rows): void {
                foreach ($rows as $row) {
                    $coop = trim((string) ($row->coop_name ?? ''));
                    $branch = trim((string) ($row->branch ?? ''));
                    $merged = $coop;
                    if ($branch !== '') {
                        $merged = $merged === '' ? $branch : $coop.' — '.$branch;
                    }
                    DB::table('access_requests')
                        ->where('id', $row->id)
                        ->update(['coop_name_branch' => $merged !== '' ? $merged : null]);
                }
            });

        Schema::table('access_requests', function (Blueprint $table): void {
            $table->dropColumn(['coop_name', 'branch', 'approval_signed_path']);
        });
    }
};
