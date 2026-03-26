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
        Schema::create('access_requests', function (Blueprint $table): void {
            $table->id();
            $table->string('request_number')->nullable()->index();
            $table->string('full_name');
            $table->string('email')->nullable()->index();
            $table->string('mobile_no', 20)->nullable();
            $table->string('coop_name_branch')->nullable();
            $table->date('request_date')->nullable();
            $table->string('status', 30)->default('pending')->index();
            $table->json('systems')->nullable();
            $table->json('summary')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->string('approved_by')->nullable();
            $table->text('approval_remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_requests');
    }
};
