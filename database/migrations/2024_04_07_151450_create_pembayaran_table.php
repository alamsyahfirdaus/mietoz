<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePembayaranTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pesanan')->constrained('pesanan')->onDelete('cascade')->onUpdate('cascade');
            $table->string('metode_pembayaran'); // Metode Pembayaran (Tunai / Transfer Bank)
            $table->decimal('total_pembayaran', 10, 2);
            $table->datetime('tanggal_pembayaran')->nullable();
            $table->decimal('jumlah_pembayaran', 10, 2)->nullable();
            $table->decimal('jumlah_kembalian', 10, 2)->nullable();
            $table->text('bukti_pembayaran')->nullable();
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pembayaran');
    }
}
