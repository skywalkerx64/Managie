<?php

use App\Models\Mail;
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
        Schema::create('mails', function (Blueprint $table) {
            $table->id();
            $table->string('fullname')->nullable();
            $table->string('subject')->nullable();
            $table->string('object')->nullable();
            $table->string('content')->nullable();
            $table->string('contact')->nullable();
            $table->string('email')->nullable();
            $table->json('cc')->nullable();
            $table->json('bcc')->nullable();
            $table->string('type')->default(Mail::TYPE_COMMON);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mails');
    }
};
