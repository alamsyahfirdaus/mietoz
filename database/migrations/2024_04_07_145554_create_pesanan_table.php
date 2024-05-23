<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePesananTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();
            $table->string('no_transaksi')->unique();
            $table->foreignId('id_pelanggan')->nullable()->constrained('pelanggan')->onDelete('set null')->onUpdate('cascade');
            $table->string('nama_pelanggan')->nullable();
            $table->string('telepon_pelanggan')->nullable();
            $table->datetime('tanggal_pesanan');
            $table->decimal('total_harga', 10, 2);
            $table->decimal('biaya_pengiriman', 10, 2)->nullable();
            // $table->enum('status_pesanan', ['Belum Bayar', 'Dikemas', 'Dikirim', 'Selesai'])->nullable();
            $table->string('status_pesanan');
            $table->text('keterangan')->nullable();
            $table->foreignId('id_kasir')->nullable()->constrained('users')->onDelete('set null')->onUpdate('cascade');
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
        Schema::dropIfExists('pesanan');
    }
}
