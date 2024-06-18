<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->text('message');
            $table->foreignId('id_pesanan')->nullable()->constrained('pesanan')->onDelete('set null')->onUpdate('cascade');
            $table->foreignId('id_kasir')->nullable()->constrained('users')->onDelete('set null')->onUpdate('cascade');
            $table->string('sender')->nullable();
            $table->boolean('is_read')->default(false);
            $table->datetime('tanggal');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chats');
    }
}
