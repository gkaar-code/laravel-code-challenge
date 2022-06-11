<?php

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
        Schema::create('posts', function (Blueprint $table) {
            $userModel = User::factory()->newModel();

            $table->id();
            $table->foreignIdFor(User::class, 'author_id')
                ->references($userModel->getKeyName())
                ->on($userModel->getTable())
                ->cascadeOnDelete()
                ->cascadeOnUpdate()
            ;
            $table->string('title', 50)->index();
            $table->string('slug', 50)->index();
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
        Schema::dropIfExists('posts');
    }
};
