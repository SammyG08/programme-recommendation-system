<?php

use App\Models\CoreSubject;
use App\Models\Programme;
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
        Schema::create('core_programme', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Programme::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(CoreSubject::class)->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('core_programme');
    }
};
