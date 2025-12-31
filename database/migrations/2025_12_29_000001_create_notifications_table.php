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
        // Laravel's standard notifications table
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        // User notification preferences
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('email_enabled')->default(true);
            $table->boolean('sms_enabled')->default(false);
            $table->boolean('in_app_enabled')->default(true);

            // Category preferences
            $table->boolean('certification_alerts')->default(true);
            $table->boolean('document_alerts')->default(true);
            $table->boolean('training_reminders')->default(true);
            $table->boolean('service_request_updates')->default(true);
            $table->boolean('system_announcements')->default(true);

            // Reminder timing (days before)
            $table->integer('certification_reminder_days')->default(30);
            $table->integer('document_reminder_days')->default(30);
            $table->integer('training_reminder_days')->default(7);

            $table->timestamps();

            $table->unique('user_id');
        });

        // Notification log for tracking sent notifications
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('type'); // certification_expiry, document_expiry, training_reminder, etc.
            $table->string('channel'); // email, sms, database
            $table->string('status'); // sent, failed, pending
            $table->string('recipient_email')->nullable();
            $table->string('recipient_phone')->nullable();
            $table->string('subject')->nullable();
            $table->text('message')->nullable();
            $table->json('metadata')->nullable(); // Additional data like farmer_id, certification_id, etc.
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'type']);
            $table->index(['type', 'status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('notification_preferences');
        Schema::dropIfExists('notifications');
    }
};
