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
        Schema::create('news_items', function (Blueprint $table) {
            $table->id();
            $table->string('news_title');
            $table->string('author_name');
            $table->longText('news_description')->nullable();
            $table->string('category')->nullable();
            $table->string('status')->default('Draft');
            $table->timestamp('date')->nullable();
            $table->string('image')->nullable();
            $table->integer('target_channel')->nullable();
            $table->text('admin_feedback')->nullable();
            $table->timestamps();
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->integer('article_id');
            $table->string('user_name');
            $table->text('comment_text');
            $table->string('status')->default('Approved');
            $table->timestamp('date')->nullable();
            $table->timestamps();
        });

        Schema::create('inbox_messages', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        Schema::create('channel_tasks', function (Blueprint $table) {
            $table->id();
            $table->integer('channel_id');
            $table->integer('author_id');
            $table->string('topic');
            $table->text('resources')->nullable();
            $table->timestamp('deadline')->nullable();
            $table->string('status')->default('Pending');
            $table->timestamps();
        });

        Schema::create('channel_authors', function (Blueprint $table) {
            $table->id();
            $table->integer('channel_id');
            $table->integer('author_id');
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action');
            $table->text('details');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news_items');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('inbox_messages');
        Schema::dropIfExists('channel_tasks');
        Schema::dropIfExists('channel_authors');
        Schema::dropIfExists('audit_logs');
    }
};
