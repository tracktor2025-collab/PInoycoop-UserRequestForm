<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pages')) {
            Schema::create('pages', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->string('subcontext', 500)->nullable();
                $table->string('seo_title')->nullable();
                $table->text('seo_description')->nullable();
                $table->string('seo_keywords')->nullable();
                $table->json('builder_settings')->nullable();
                $table->longText('content')->nullable();
                if (DB::getDriverName() === 'sqlite') {
                    $table->binary('image_blob')->nullable();
                }
                $table->string('image_mime')->nullable();
                $table->string('template')->nullable();
                $table->boolean('is_published')->default(false);
                $table->timestamp('published_at')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });

            if (DB::getDriverName() !== 'sqlite') {
                DB::statement('ALTER TABLE pages ADD image_blob LONGBLOB NULL AFTER content');
            }
        } else {
            Schema::table('pages', function (Blueprint $table) {
                if (! Schema::hasColumn('pages', 'subcontext')) {
                    $table->string('subcontext', 500)->nullable()->after('slug');
                }
                if (! Schema::hasColumn('pages', 'seo_title')) {
                    $table->string('seo_title')->nullable()->after('subcontext');
                }
                if (! Schema::hasColumn('pages', 'seo_description')) {
                    $table->text('seo_description')->nullable()->after('seo_title');
                }
                if (! Schema::hasColumn('pages', 'seo_keywords')) {
                    $table->string('seo_keywords')->nullable()->after('seo_description');
                }
                if (! Schema::hasColumn('pages', 'builder_settings')) {
                    $table->json('builder_settings')->nullable()->after('seo_keywords');
                }
                if (! Schema::hasColumn('pages', 'image_mime')) {
                    $table->string('image_mime')->nullable()->after('content');
                }
            });

            if (! Schema::hasColumn('pages', 'image_blob')) {
                if (DB::getDriverName() === 'sqlite') {
                    Schema::table('pages', function (Blueprint $table) {
                        $table->binary('image_blob')->nullable();
                    });
                } else {
                    DB::statement('ALTER TABLE pages ADD image_blob LONGBLOB NULL AFTER content');
                }
            }
        }

        if (! Schema::hasTable('menus')) {
            Schema::create('menus', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('location')->default('primary');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('menu_items')) {
            Schema::create('menu_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('menu_id')->constrained()->cascadeOnDelete();
                $table->foreignId('parent_id')->nullable()->constrained('menu_items')->cascadeOnDelete();
                $table->string('label');
                $table->string('url')->nullable();
                $table->foreignId('page_id')->nullable()->constrained('pages')->nullOnDelete();
                $table->unsignedInteger('sort_order')->default(0);
                $table->string('target')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('contact_messages')) {
            Schema::create('contact_messages', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email');
                $table->text('message');
                $table->string('ip_address', 45)->nullable();
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('page_sub_images')) {
            Schema::create('page_sub_images', function (Blueprint $table) {
                $table->id();
                $table->foreignId('page_id')->constrained()->cascadeOnDelete();
                $table->unsignedInteger('sort_order')->default(0);
                if (DB::getDriverName() === 'sqlite') {
                    $table->binary('image_blob');
                }
                $table->string('image_mime');
                $table->timestamps();
            });

            if (DB::getDriverName() !== 'sqlite') {
                DB::statement('ALTER TABLE page_sub_images ADD image_blob LONGBLOB NOT NULL AFTER sort_order');
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('page_sub_images');
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menus');
    }
};
