<?php

use App\Models\CoreSubject;
use App\Models\ElectiveSubject;
use App\Models\Faculty;
use App\Models\ProgrammeType;
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
        Schema::create('programmes', function (Blueprint $table) {
            $table->id();
            $table->string('programme_name');
            $table->foreignIdFor(ElectiveSubject::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(CoreSubject::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Faculty::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(ProgrammeType::class)->constrained()->cascadeOnDelete();
            $table->foreignId('lowest_grade_for_cores')->references('id')->on('grades')->cascadeOnDelete();
            $table->foreignId('lowest_grade_for_electives')->references('id')->on('grades')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programmes');
    }
};
