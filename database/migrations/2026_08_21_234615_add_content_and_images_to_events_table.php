<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->text('description')->nullable()->after('type');
            $table->string('short_description', 500)->nullable()->after('description');
            $table->string('host')->nullable()->after('short_description');
            $table->string('image_path')->nullable()->after('host');
            $table->string('mobile_image_path')->nullable()->after('image_path');
        });

        Schema::create('event_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_images');

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'short_description',
                'host',
                'image_path',
                'mobile_image_path',
            ]);
        });
    }
};
