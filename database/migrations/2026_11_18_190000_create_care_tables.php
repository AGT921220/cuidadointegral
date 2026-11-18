<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('birth_date')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('care_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('patient_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('link_type', 32);
            $table->timestamps();
            $table->unique(['patient_id', 'user_id', 'link_type']);
        });

        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->restrictOnDelete();
            $table->foreignId('caregiver_id')->constrained('users')->restrictOnDelete();
            $table->string('status', 32);
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->string('service_address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('instructions')->nullable();
            $table->dateTime('check_in_at')->nullable();
            $table->decimal('check_in_latitude', 10, 7)->nullable();
            $table->decimal('check_in_longitude', 10, 7)->nullable();
            $table->decimal('check_in_accuracy', 8, 2)->nullable();
            $table->dateTime('check_out_at')->nullable();
            $table->decimal('check_out_latitude', 10, 7)->nullable();
            $table->decimal('check_out_longitude', 10, 7)->nullable();
            $table->decimal('check_out_accuracy', 8, 2)->nullable();
            $table->timestamps();
            $table->index(['caregiver_id', 'starts_at']);
            $table->index(['patient_id', 'starts_at']);
        });

        Schema::create('shift_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('from_status', 32)->nullable();
            $table->string('to_status', 32);
            $table->string('note')->nullable();
            $table->timestamps();
        });

        Schema::create('shift_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('accuracy', 8, 2)->nullable();
            $table->dateTime('recorded_at');
            $table->timestamps();
            $table->index(['shift_id', 'recorded_at']);
        });

        Schema::create('care_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shift_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('instructions')->nullable();
            $table->timestamps();
        });

        Schema::create('care_task_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('care_task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shift_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('status', 32);
            $table->text('observation')->nullable();
            $table->dateTime('performed_at')->nullable();
            $table->timestamps();
            $table->index(['care_task_id', 'shift_id']);
        });

        Schema::create('care_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('kind', 32);
            $table->text('body');
            $table->timestamps();
            $table->index(['shift_id', 'created_at']);
        });

        Schema::create('health_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->restrictOnDelete();
            $table->foreignId('shift_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->dateTime('recorded_at');
            $table->decimal('temperature', 4, 1)->nullable();
            $table->unsignedSmallInteger('heart_rate')->nullable();
            $table->unsignedSmallInteger('systolic')->nullable();
            $table->unsignedSmallInteger('diastolic')->nullable();
            $table->unsignedTinyInteger('oxygen_saturation')->nullable();
            $table->unsignedTinyInteger('respiratory_rate')->nullable();
            $table->unsignedSmallInteger('glucose')->nullable();
            $table->text('symptoms')->nullable();
            $table->text('progress_notes')->nullable();
            $table->timestamps();
            $table->index(['patient_id', 'recorded_at']);
        });

        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->restrictOnDelete();
            $table->foreignId('shift_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('level', 32);
            $table->string('status', 32);
            $table->text('description');
            $table->dateTime('occurred_at');
            $table->timestamps();
            $table->index(['patient_id', 'occurred_at']);
        });

        Schema::create('incident_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('status', 32);
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_events');
        Schema::dropIfExists('incidents');
        Schema::dropIfExists('health_records');
        Schema::dropIfExists('care_logs');
        Schema::dropIfExists('care_task_records');
        Schema::dropIfExists('care_tasks');
        Schema::dropIfExists('shift_locations');
        Schema::dropIfExists('shift_events');
        Schema::dropIfExists('shifts');
        Schema::dropIfExists('patient_links');
        Schema::dropIfExists('patients');
    }
};
