<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $postModel = Post::factory()->newModel();
            $userModel = User::factory()->newModel();

            $table->id();
            $table->foreignIdFor(Post::class)
                  ->references($postModel->getKeyName())
                  ->on($postModel->getTable())
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate()
            ;
            $table->foreignIdFor(User::class, 'author_id')
                  ->references($userModel->getKeyName())
                  ->on($userModel->getTable())
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate()
            ;
            $table->text('content');
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
};
