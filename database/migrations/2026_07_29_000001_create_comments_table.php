<?php

use App\Models\Post;
use App\Models\User;
use Phaseolies\Support\Facades\Schema;
use Phaseolies\Database\Migration\Blueprint;
use Phaseolies\Database\Migration\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Post::class, true, true);
            $table->foreignIdFor(User::class, true, true);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->text('body');
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->foreign('parent_id')
                ->references('id')
                ->on('comments')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
