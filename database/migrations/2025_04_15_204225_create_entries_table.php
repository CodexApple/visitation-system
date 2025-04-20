<?php

use App\Models\User;
use App\Models\Purpose;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('entries', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->date('date_in');
            $table->date('date_out')->nullable();
            $table->time('time_in');
            $table->time('time_out')->nullable();
            $table->integer('edit_state')->unsigned()->nullable();
            $table->foreignIdFor(Purpose::class);
            $table->longText('description');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entries');
    }
};
