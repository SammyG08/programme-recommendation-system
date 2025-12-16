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
        Schema::create('faculties', function (Blueprint $table) {
            $table->id();
            $table->string('faculty_name')->unique();
            $table->timestamps();
        });

        Schema::create('core_subjects', function (Blueprint $table) {
            $table->id();
            $table->enum("mathematics", ['required', 'not required'])->default('required');
            $table->enum("english", ['required', 'not required'])->default("required");
            $table->enum("science", ['required', 'not required'])->default('required');
            $table->enum("social", ['required', 'not required'])->default('not required');
            $table->timestamps();
        });

        Schema::create("elective_subjects", function (Blueprint $table) {
            $table->id();
            $table->string('elective_one');
            $table->string('elective_two');
            $table->string('elective_three');
            $table->timestamps();
        });

        Schema::create('programme_types', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->timestamps();
        });

        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->string('grade');
            $table->integer('value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faculties');
        Schema::dropIfExists('core_subjects');
        Schema::dropIfExists('elective_subjects');
        Schema::dropIfExists('programme_types');
        Schema::dropIfExists('grades');
    }
};
