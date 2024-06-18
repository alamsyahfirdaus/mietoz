@extends('layouts/main')
@section('content')
    <div class="container">

        @if (session('level') == 1)
            <div class="card {{ isset($data) ? 'card-danger card-outline' : '' }}">
                @if (empty($data))
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item"><a class="nav-link active" href="#tab1" data-toggle="tab">Daftar
                                    {{ $title }}</a>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="#tab2" data-toggle="tab">Tambah
                                    {{ $title }}</a></li>
                        </ul>
                    </div>
                @else
                    <div class="card-header">
                        <h3 class="card-title">Daftar Produk</h3>
                    </div>
                @endif

                <div class="card-body">
                    <div class="tab-content">
                        <div class="{{ empty($data) ? 'active' : '' }} tab-pane" id="tab1">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table datatable table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th style="width: 5%; text-align: center;">No</th>
                                                    <th>ID<span>_</span>Pesanan</th>
                                                    <th>Nama<span>_</span>Pelanggan</th>
                                                    <th>Tanggal<span>_</span>Pesanan</th>
                                                    <th>Total<span>_</span>Harga</th>
                                                    <th>Status<span>_</span>Pesanan</th>
                                                    <th style="width: 5%; text-align: center;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($order as $key => $item)
                                                    <tr>
                                                        <td style="text-align: center;">{{ $key + 1 }}</td>
                                                        <td>{{ $item->no_transaksi }}</td>
                                                        <td><a href="{{ route('home.chat', ['id' => base64_encode($item->id)]) }}" title="Chat Pelanggan">{{ $item->nama_pelanggan ?? ($item->customer->nama ?? '-') }}</a>
                                                        </td>
                                                        <td>{{ date('d F Y', strtotime($item->tanggal_pesanan)) }}</td>
                                                        <td>{{ 'Rp' . number_format($item->total_harga, 0, ',', '.') }}</td>
                                                        <td>
                                                            @php
                                                                $metode_pembayaran = ['Tunai', 'Transfer Bank'];
                                                                $status_pesanan = ['Belum Bayar', 'Dikirim', 'Selesai'];
                                                            @endphp
                                                            @if (isset($item->payment->id))
                                                                <div style="display: none;">
                                                                    <input type="text"
                                                                        id="id_pesanan_{{ md5($item->id) }}"
                                                                        value="{{ base64_encode($item->id) }}">
                                                                    <input type="text"
                                                                        id="bukti_pembayaran_{{ md5($item->id) }}"
                                                                        value="{{ $item->payment->bukti_pembayaran ? asset('upload_images/' . $item->payment->bukti_pembayaran) : asset('assets/img/product-450x300.jpg') }}">
                                                                </div>
                                                                @if ($item->payment->metode_pembayaran == 1)
                                                                    {{ $metode_pembayaran[$item->payment->metode_pembayaran - 1] }}
                                                                    -
                                                                    {{ $status_pesanan[$item->status_pesanan - 1] ?? '-' }}
                                                                @else
                                                                    @if ($item->status_pesanan != 3)
                                                                        <a href="javascript:void(0)"
                                                                            onclick="updatePayment('{{ md5($item->id) }}')">
                                                                            {{ $metode_pembayaran[$item->payment->metode_pembayaran - 1] }}
                                                                            -
                                                                            {{ $status_pesanan[$item->status_pesanan - 1] ?? '-' }}
                                                                        </a>
                                                                    @else
                                                                        {{ $metode_pembayaran[$item->payment->metode_pembayaran - 1] }}
                                                                        -
                                                                        {{ $status_pesanan[$item->status_pesanan - 1] ?? '-' }}
                                                                    @endif
                                                                @endif
                                                            @else
                                                                {{ $status_pesanan[$item->status_pesanan - 1] }}
                                                            @endif
                                                        </td>

                                                        <td style="text-align: center;">
                                                            <div class="btn-group">
                                                                <button type="button"
                                                                    class="btn btn-danger btn-sm dropdown-toggle"
                                                                    data-toggle="dropdown">Aksi </button>
                                                                <div class="dropdown-menu" role="menu">
                                                                    @if ($item->status_pesanan == 1)
                                                                        <a class="dropdown-item"
                                                                            href="{{ route('order.edit', ['id' => base64_encode($item->id)]) }}">Edit</a>
                                                                    @else
                                                                        <a class="dropdown-item"
                                                                            href="{{ route('order.show', ['id' => base64_encode($item->id)]) }}">Detail</a>
                                                                        <div class="dropdown-divider"></div>
                                                                        <a class="dropdown-item"
                                                                            href="{{ route('order.invoice', ['id' => base64_encode($item->id)]) }}">Nota</a>
                                                                    @endif
                                                                    <div class="dropdown-divider"></div>
                                                                    {!! Form::open([
                                                                        'route' => ['order.delete', base64_encode($item->id)],
                                                                        'method' => 'DELETE',
                                                                        'id' => 'remove-' . md5($item->id),
                                                                    ]) !!}
                                                                    <a class="dropdown-item" href="javascript:void(0)"
                                                                        onclick="deleteData('{{ md5($item->id) }}')">Hapus</a>
                                                                    {!! Form::close() !!}
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @empty($data)
                            <div class="card card-danger card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">Transaksi dan Pendapatan</h3>
                                </div>
                                <div class="card-body">
                                    <form action="" method="POST" enctype="multipart/form-data" id="filter-transaksi">
                                        @csrf
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label for="tanggal_mulai">Dari Tanggal</label>
                                                    <select name="tanggal_mulai" id="tanggal_mulai"
                                                        class="form-control select2 select2-danger"
                                                        data-dropdown-css-class="select2-danger" style="width: 100%;">
                                                        <option value="">Dari Tanggal</option>
                                                        @foreach (App\Models\Order::getOrderDates('asc') as $key => $item)
                                                            <option value="{{ $item->tanggal }}"
                                                                {{ $item->tanggal == @$tanggal_mulai || $key == 0 ? 'selected' : '' }}>
                                                                {{ date('d F Y', strtotime($item->tanggal)) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <span id="error-tanggal_mulai" class="error invalid-feedback"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label for="tanggal_selesai">Sampai Tanggal</label>
                                                    <select name="tanggal_selesai" id="tanggal_selesai"
                                                        class="form-control select2 select2-danger"
                                                        data-dropdown-css-class="select2-danger" style="width: 100%;">
                                                        <option value="">Sampai Tanggal</option>
                                                        @foreach (App\Models\Order::getOrderDates('desc') as $key => $item)
                                                            <option value="{{ $item->tanggal }}"
                                                                {{ $item->tanggal == @$tanggal_selesai || $key == 0 ? 'selected' : '' }}>
                                                                {{ date('d F Y', strtotime($item->tanggal)) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <span id="error-tanggal_selesai" class="error invalid-feedback"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label for="jumlah_transaksi">Jumlah Transaksi</label>
                                                    <input type="text" class="form-control" id="jumlah_transaksi"
                                                        name="jumlah_transaksi" placeholder="Jumlah Transaksi"
                                                        value="{{ $jumlah_transaksi }} Transaksi (Selesai)" disabled>
                                                    <span id="error-jumlah_transaksi" class="error invalid-feedback"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label for="total_pendapatan">Total Pendapatan</label>
                                                    <input type="text" class="form-control" id="total_pendapatan"
                                                        name="total_pendapatan" placeholder="Total Pendapatan"
                                                        value="{{ 'Rp' . number_format($total_pendapatan, 0, ',', '.') }}"
                                                        disabled>
                                                    <span id="error-total_pendapatan"
                                                        class="error invalid-feedback"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="table-responsive">
                                        <table class="table datatable table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th style="width: 5%; text-align: center;">No</th>
                                                    <th>Tanggal</th>
                                                    <th>Jumlah<span>_</span>Transaksi</th>
                                                    <th>Total<span>_</span>Pendapatan</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($transaction as $key => $item)
                                                    <tr>
                                                        <td style="text-align: center;">{{ $key + 1 }}</td>
                                                        <td>{{ date('d F Y', strtotime($item->tanggal_pesanan)) }}</td>
                                                        <td>{{ $item->jumlah_transaksi }} Transaksi (Selesai)</td>
                                                        <td>{{ 'Rp' . number_format($item->total_pendapatan, 0, ',', '.') }}
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endempty
                    </div>
                    <div class="{{ isset($data) ? 'active' : '' }} tab-pane" id="tab2">
                        <div class="row">
                            <div class="col-md-6 mb-4 m-lg-0 pl-0">
                                <div class="input-group px-2">
                                    <input type="text" class="form-control" id="search" name="search"
                                        placeholder="Cari Kategori / Produk" autocomplete="off">
                                </div>
                                <div class="table-responsive">
                                    <table id="dt-product" class="table" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($product->chunk(9) as $chunk)
                                                <tr>
                                                    <td>
                                                        <div class="row row-cols-lg-3 justify-content-center">
                                                            @foreach ($chunk as $item)
                                                                @php
                                                                    $stok =
                                                                        $item->stok -
                                                                        \App\Models\OrderDetail::countProductsSold(
                                                                            $item->id,
                                                                        );
                                                                @endphp
                                                                <div class="col mb-3">
                                                                    <div class="card h-100">
                                                                        <div class="custom-control custom-checkbox position-absolute"
                                                                            style="top: 0.5rem; left: 0.5rem; {{ $stok <= 0 ? 'display: none;' : '' }}">
                                                                            <input
                                                                                class="custom-control-input custom-control-input-danger"
                                                                                type="checkbox"
                                                                                id="{{ 'check_' . md5($item->id) }}"
                                                                                value="{{ base64_encode($item->id) }}">
                                                                            <label
                                                                                for="{{ 'check_' . md5($item->id) }}"
                                                                                class="custom-control-label"></label>
                                                                        </div>
                                                                        @if ($item->diskon)
                                                                            <div class="badge bg-white position-absolute"
                                                                                style="top: 0.5rem; right: 0.5rem">
                                                                                {{ $item->diskon }}%
                                                                            </div>
                                                                        @endif
                                                                        <img class="card-img-top"
                                                                            src="{{ $item->gambar ? asset('upload_images/' . $item->gambar) : asset('assets/img/product-450x300.jpg') }}"
                                                                            alt=""
                                                                            style="width: 100%; height: 150px;">
                                                                        <div class="card-body">
                                                                            <div class="text-center">
                                                                                <span
                                                                                    style="display: none;">{{ $item->category->nama ?? '' }}</span>
                                                                                <p
                                                                                    style="font-weight: bold; margin-bottom: 4px; font-size: 18px;">
                                                                                    {{ Str::limit($item->nama, 50) }}
                                                                                </p>
                                                                                <p style="margin-bottom: 4px;">
                                                                                    @php
                                                                                        $harga_diskon =
                                                                                            $item->diskon >= 1
                                                                                                ? intval($item->harga) -
                                                                                                    intval(
                                                                                                        ($item->harga *
                                                                                                            $item->diskon) /
                                                                                                            100,
                                                                                                    )
                                                                                                : $item->harga;
                                                                                    @endphp
                                                                                    @if ($item->diskon >= 1 || $stok <= 0)
                                                                                        <del
                                                                                            class="text-muted">{{ 'Rp' . number_format($item->harga, 0, ',', '.') }}</del>
                                                                                    @endif
                                                                                    @if ($stok <= 0)
                                                                                        <span>Habis</span>
                                                                                    @else
                                                                                        <span>{{ 'Rp' . number_format($harga_diskon, 0, ',', '.') }}</span>
                                                                                    @endif
                                                                                </p>
                                                                                <p style="font-size: 12px;">Terjual
                                                                                    {{ \App\Models\OrderDetail::countMonthlyProductsSold($item->id) }}
                                                                                    / Bulan
                                                                                </p>
                                                                            </div>
                                                                            <div style="display: none;">
                                                                                <input type="text"
                                                                                    class="nama_produk"
                                                                                    name="{{ 'nama_produk_' . base64_encode($item->id) }}"
                                                                                    value="{{ $item->nama }}">
                                                                                <input type="text" class="stok"
                                                                                    name="{{ 'stok_' . base64_encode($item->id) }}"
                                                                                    value="{{ $stok }}">
                                                                                <input type="text"
                                                                                    class="harga_produk"
                                                                                    name="{{ 'harga_produk_' . base64_encode($item->id) }}"
                                                                                    value="{{ $item->diskon >= 1 ? $harga_diskon : intval($item->harga) }}">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <form action="{{ route('order.save') }}" method="POST"
                                    enctype="multipart/form-data" id="form-data">
                                    @csrf
                                    @if (isset($data))
                                        @method('PUT')
                                        <input type="hidden" name="id_pesanan"
                                            value="{{ base64_encode($data['order']->id) }}">
                                    @endif
                                    <button type="button" class="btn btn-outline-danger mb-3"
                                        style="font-weight: bold;" onclick="addProductOrder()"><i
                                            class="fas fa-check-double"></i> Daftar
                                        Pesanan</button>
                                    <div class="table-responsive">
                                        <table id="dt-order" class="table table-striped" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>Produk</th>
                                                    <th style="width: 20%;">Qty</th>
                                                    <th>Level</th>
                                                    <th>Harga</th>
                                                    <th style="width: 5%; color: #fff;">#</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (isset($data))
                                                    @php
                                                        $total_harga = 0;
                                                        $biaya_pengiriman = intval($data['order']->biaya_pengiriman);
                                                        $nama_pelanggan = $data['order']->nama_pelanggan;
                                                        $keterangan = $data['order']->keterangan;
                                                    @endphp
                                                    @foreach ($data['order_detail'] as $item)
                                                        @php
                                                            $stok =
                                                                $item->product->stok -
                                                                \App\Models\OrderDetail::countProductsSold();
                                                            $jumlah_produk = $stok >= 1 ? $item->jumlah_produk : 0;
                                                            $total_harga += $jumlah_produk * $item->product->harga;
                                                        @endphp
                                                        <tr>
                                                            <td>
                                                                <input type="hidden" class="id_produk"
                                                                    name="id_produk[]"
                                                                    value="{{ base64_encode($item->product->id) }}">
                                                                {{ $item->product->nama }}
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control"
                                                                    name="qty_{{ base64_encode($item->product->id) }}"
                                                                    placeholder="1" value="{{ $jumlah_produk }}"
                                                                    autocomplete="off">
                                                            </td>
                                                            <td>
                                                                <select class="form-control select2 select2-danger"
                                                                    data-dropdown-css-class="select2-danger"
                                                                    name="level_{{ base64_encode($item->product->id) }}"
                                                                    style="width: 100%;">
                                                                    @foreach (['Tidak Pedas', 'Pedas Sedang', 'Sangat Pedas'] as $level)
                                                                        <option value="{{ $level }}"
                                                                            {{ $item->level == $level ? 'selected' : '' }}>
                                                                            {{ $level }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td>
                                                                {{ 'Rp' . number_format($jumlah_produk * $item->product->harga, 0, ',', '.') }}
                                                            </td>
                                                            <td><button type="button" class="btn btn-danger btn-sm"
                                                                    onclick="removeProduct(this)"><i
                                                                        class="fas fa-trash"></i></button></td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="form-group-order" class="mt-2"
                                        style="{{ empty($data) ? 'display: none;' : '' }}">
                                        <div class="form-group mb-1">
                                            <label for="total_harga">Total Harga</label>
                                            <input type="text" class="form-control" id="total_harga"
                                                name="total_harga"
                                                value="{{ isset($data) ? 'Rp' . number_format($total_harga, 0, ',', '.') : 0 }}"
                                                placeholder="Total Harga" autocomplete="off" disabled>
                                            <span id="error-total_harga" class="error invalid-feedback"></span>
                                        </div>
                                        <div class="form-group mb-1">
                                            <label for="member">Member</label>
                                            <select name="member" id="member"
                                                class="form-control select2 select2-danger"
                                                data-dropdown-css-class="select2-danger" style="width: 100%;">
                                                <option value="">Member</option>
                                                @foreach (['1' => 'Ya', '2' => 'Tidak'] as $key => $value)
                                                    <option value="{{ $key }}"
                                                        {{ (isset($data['order']) && $data['order']->id_pelanggan && $key == '1') ||
                                                        (isset($data['order']) && ($data['order']->nama_pelanggan && $key == '2'))
                                                            ? 'selected'
                                                            : '' }}>
                                                        {{ $value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span id="error-member" class="error invalid-feedback"></span>
                                        </div>
                                        <div id="member_1" class="form-group mb-1"
                                            style="{{ isset($data) && $data['order']->id_pelanggan ? '' : 'display: none;' }}">
                                            <label for="id_pelanggan">Nama Pelanggan</label>
                                            <select name="id_pelanggan" id="id_pelanggan"
                                                class="form-control select2 select2-danger"
                                                data-dropdown-css-class="select2-danger" style="width: 100%;">
                                                <option value="">Nama Pelanggan</option>
                                                @foreach ($customer as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ isset($data) && $data['order']->id_pelanggan == $item->id ? 'selected' : '' }}>
                                                        {{ $item->nama . ' (' . $item->kode . ')' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span id="error-id_pelanggan" class="error invalid-feedback"></span>
                                        </div>
                                        <div id="member_2" class="form-group mb-1"
                                            style="{{ isset($data['order']) && $data['order']->nama_pelanggan ? '' : 'display: none;' }}">
                                            <label for="nama_pelanggan">Nama Pembeli</label>
                                            <input type="text" name="nama_pelanggan" id="nama_pelanggan"
                                                class="form-control"
                                                value="{{ isset($data['order']) ? $data['order']->nama_pelanggan : '' }}"
                                                placeholder="Nama Pembeli" autocomplete="off">
                                            <span id="error-nama_pelanggan" class="error invalid-feedback"></span>
                                        </div>
                                        <div class="form-group mb-1">
                                            <label for="metode_pembayaran">Metode Pembayaran</label>
                                            <select name="metode_pembayaran" id="metode_pembayaran"
                                                class="form-control select2 select2-danger"
                                                data-dropdown-css-class="select2-danger" style="width: 100%;">
                                                <option value="">Metode Pembayaran</option>
                                                @foreach (['Tunai', 'Transfer Bank'] as $key => $value)
                                                    <option value="{{ $key + 1 }}"
                                                        {{ $key + 1 == 1 ? 'selected' : '' }}>{{ $value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span id="error-metode_pembayaran" class="error invalid-feedback"></span>
                                        </div>
                                        <div class="form-group mb-1">
                                            <label for="opsi_pengiriman">Opsi Pengiriman</label>
                                            <select name="opsi_pengiriman" id="opsi_pengiriman"
                                                class="form-control select2 select2-danger"
                                                data-dropdown-css-class="select2-danger" style="width: 100%;">
                                                <option value="">Opsi Pengiriman</option>
                                                @foreach (['1' => 'Diambil di Toko', '2' => 'Dikirim ke Alamat Tujuan'] as $key => $value)
                                                    <option value="{{ $key }}"
                                                        {{ (empty($data['data']) && $key == '1') || (isset($data['data']) && $data['order']->biaya_pengiriman > 0 && $key == '2') ? 'selected' : '' }}>
                                                        {{ $value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span id="error-opsi_pengiriman" class="error invalid-feedback"></span>
                                        </div>
                                        <div id="opsi_kirim_2" class="form-group mb-1"
                                            style="{{ isset($data['order']) && $data['order']->biaya_pengiriman ? '' : 'display: none;' }}">
                                            <label for="biaya_pengiriman">Biaya Pengiriman</label>
                                            <input type="text" class="form-control" id="biaya_pengiriman"
                                                name="biaya_pengiriman" placeholder="Biaya Pengiriman"
                                                autocomplete="off"
                                                value="{{ isset($data) && $biaya_pengiriman >= 1 ? $biaya_pengiriman : '' }}">
                                            <span id="error-biaya_pengiriman" class="error invalid-feedback"></span>
                                        </div>
                                        <div class="form-group mb-1">
                                            <label for="total_bayar">Total Pembayaran</label>
                                            <input type="text" class="form-control" id="total_bayar"
                                                name="total_bayar" placeholder="Total Pembayaran"
                                                value="{{ isset($data) ? 'Rp' . number_format($total_harga + $biaya_pengiriman, 0, ',', '.') : 0 }}"
                                                autocomplete="off" disabled>
                                            <span id="error-total_bayar" class="error invalid-feedback"></span>
                                        </div>
                                        <div class="form-group mb-1">
                                            <label for="keterangan">Keterangan</label>
                                            <textarea class="form-control" name="keterangan" id="keterangan" placeholder="Keterangan (Opsional)"
                                                autocomplete="off">{{ isset($data) ? $keterangan : '' }}</textarea>
                                            <span id="error-keterangan" class="error invalid-feedback"></span>
                                        </div>
                                        <div id="form-group-payment">
                                            <div class="form-group mb-1">
                                                <label for="jumlah_pembayaran">Jumlah Pembayaran</label>
                                                <input type="text" class="form-control" id="jumlah_pembayaran"
                                                    name="jumlah_pembayaran" placeholder="Jumlah Pembayaran"
                                                    value="" autocomplete="off" title="Jumlah Pembayaran">
                                                <span id="error-jumlah_pembayaran"
                                                    class="error invalid-feedback"></span>
                                            </div>
                                            <div class="form-group mb-1">
                                                <label for="jumlah_kembalian">Jumlah Kembalian</label>
                                                <input type="text" class="form-control" id="jumlah_kembalian"
                                                    name="jumlah_kembalian" placeholder="Jumlah Kembalian"
                                                    value="" autocomplete="off" disabled
                                                    title="Jumlah Kembalian">
                                                <span id="error-jumlah_kembalian"
                                                    class="error invalid-feedback"></span>
                                            </div>
                                        </div>
                                        <div class="form-group mt-4">
                                            <input type="hidden" name="status_pesanan" value="">
                                            <input type="hidden" name="total_pembayaran" value="">
                                            <button type="button" class="btn btn-outline-danger"
                                                onclick="addCart()"><i class="fas fa-cart-plus"></i>
                                                Keranjang</button>
                                            <button type="button" class="btn btn-outline-danger float-right"
                                                onclick="addPayment()"><i class="fas fa-shipping-fast"></i>
                                                Bayar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h3 class="card-title">Transaksi dan Pendapatan</h3>
            </div>
            <div class="card-body">
                <form action="" method="POST" enctype="multipart/form-data" id="filter-transaksi">
                    @csrf
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="tanggal_mulai">Dari Tanggal</label>
                                <select name="tanggal_mulai" id="tanggal_mulai"
                                    class="form-control select2 select2-danger"
                                    data-dropdown-css-class="select2-danger" style="width: 100%;">
                                    <option value="">Dari Tanggal</option>
                                    @foreach (App\Models\Order::getOrderDates('asc') as $key => $item)
                                        <option value="{{ $item->tanggal }}"
                                            {{ $item->tanggal == @$tanggal_mulai || $key == 0 ? 'selected' : '' }}>
                                            {{ date('d F Y', strtotime($item->tanggal)) }}
                                        </option>
                                    @endforeach
                                </select>
                                <span id="error-tanggal_mulai" class="error invalid-feedback"></span>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="tanggal_selesai">Sampai Tanggal</label>
                                <select name="tanggal_selesai" id="tanggal_selesai"
                                    class="form-control select2 select2-danger"
                                    data-dropdown-css-class="select2-danger" style="width: 100%;">
                                    <option value="">Sampai Tanggal</option>
                                    @foreach (App\Models\Order::getOrderDates('desc') as $key => $item)
                                        <option value="{{ $item->tanggal }}"
                                            {{ $item->tanggal == @$tanggal_selesai || $key == 0 ? 'selected' : '' }}>
                                            {{ date('d F Y', strtotime($item->tanggal)) }}
                                        </option>
                                    @endforeach
                                </select>
                                <span id="error-tanggal_selesai" class="error invalid-feedback"></span>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="jumlah_transaksi">Jumlah Transaksi</label>
                                <input type="text" class="form-control" id="jumlah_transaksi"
                                    name="jumlah_transaksi" placeholder="Jumlah Transaksi"
                                    value="{{ $jumlah_transaksi }} Transaksi (Selesai)" disabled>
                                <span id="error-jumlah_transaksi" class="error invalid-feedback"></span>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="total_pendapatan">Total Pendapatan</label>
                                <input type="text" class="form-control" id="total_pendapatan"
                                    name="total_pendapatan" placeholder="Total Pendapatan"
                                    value="{{ 'Rp' . number_format($total_pendapatan, 0, ',', '.') }}" disabled>
                                <span id="error-total_pendapatan" class="error invalid-feedback"></span>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table datatable table-bordered table-hover">
                        <thead>
                            <tr>
                                <th style="width: 5%; text-align: center;">No</th>
                                <th>Tanggal</th>
                                <th>Jumlah<span>_</span>Transaksi</th>
                                <th>Total<span>_</span>Pendapatan</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transaction as $key => $item)
                                <tr>
                                    <td style="text-align: center;">{{ $key + 1 }}</td>
                                    <td>{{ date('d F Y', strtotime($item->tanggal_pesanan)) }}</td>
                                    <td>{{ $item->jumlah_transaksi }} Transaksi (Selesai)</td>
                                    <td>{{ 'Rp' . number_format($item->total_pendapatan, 0, ',', '.') }}
                                    </td>
                                    <td></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>

<div class="modal fade" id="modal-form">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                {{-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button> --}}
            </div>
            <div class="modal-body py-2">
                <div class="form-group mb-2">
                    <label>Bukti Pembayaran</label>
                    <img src="" id="img-bukti_pembayaran" alt=""
                        style="width: 100%; height: 175px;">
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"><i
                        class="fas fa-times-circle"></i> Batal</button>
                <form action="{{ route('order.update') }}" method="POST" enctype="multipart/form-data"
                    id="form-update-payment">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">
                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-angle-double-right"></i>
                        Konfirmasi</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    #dt-product thead th {
        vertical-align: bottom;
        border: none;
        padding-top: 0;
        display: none;
    }

    #dt-product tbody td {
        border-top: none;
    }

    #dt-product_filter {
        display: none;
    }

    #dt-order thead th {
        border-bottom: none;
    }

    #form-group-order .form-group label {
        font-size: 12px;
    }
</style>

<script>
    $(function() {
        var table = $('#dt-product').DataTable({
            "paging": true,
            "pageLength": 1,
            "lengthChange": false,
            "searching": true,
            "ordering": false,
            "info": false,
            "autoWidth": false,
            "responsive": false,
            "language": {
                "emptyTable": "",
                "zeroRecords": ""
            }
        });

        $('#search').on('keyup', function() {
            table.search($(this).val()).draw();
        });

        var orderTable = $('#dt-order').DataTable({
            "paging": false,
            "searching": false,
            "info": false,
            "autoWidth": false,
            "responsive": false,
            "ordering": false,
            "language": {
                "emptyTable": "<small class='text-muted'>Pilih minimal satu produk untuk ditambahkan ke pesanan.</small>",
            }
        });

        $('#dt-order tbody').on('keyup', 'input[type="text"]', function() {
            var id = $(this).attr('name').split('_')[1];
            var stock = parseInt($('[name="stok_' + id + '"]').val());
            var quantity = parseInt($('[name="qty_' + id + '"]').val());
            var price = parseFloat($('[name="harga_produk_' + id + '"]').val());

            if (isNaN(quantity) || quantity <= 0) {
                $('[name="qty_' + id + '"]').val('');
                var total = price;
            } else if (quantity > stock) {
                $('[name="qty_' + id + '"]').val(stock);
                var total = stock * price;
            } else {
                var total = quantity * price;
            }

            $('[name="qty_' + id + '"]').closest('tr').find('td:nth-last-child(2)').text('Rp' + total
                .toLocaleString('id-ID'));

            updateTotalPrice();
        });

        $('[name="metode_pembayaran"]').change(function() {
            if ($(this).val() == 1) {
                $('#form-group-payment').show();
            } else {
                $('#form-group-payment').hide();
            }
            $(this).next().find('.select2-selection').removeClass('border border-danger');
            $('#error-metode_pembayaran').text('').hide();
        });

        $('[name="biaya_pengiriman"]').keyup(function() {
            var totalPrice = parseFloat($('#total_harga').val().replace(/[^\d]/g, '')) || 0;
            var additionalCost = parseInt($(this).val());

            if (isNaN(additionalCost) || additionalCost <= 0) {
                var totalPayment = totalPrice;
            } else {
                var totalPayment = totalPrice + additionalCost;
            }

            $('#total_bayar').val('Rp' + totalPayment.toLocaleString('id-ID'));

            if ($('[name="jumlah_pembayaran"]').val()) {
                $('[name="jumlah_pembayaran"]').trigger('keyup');
            }
        });

        $('[name="jumlah_pembayaran"]').keyup(function() {
            var totalPayment = parseFloat($('#total_bayar').val().replace(/[^\d]/g, '')) || 0;
            var payment = parseInt($(this).val().replace(/[^\d]/g, '')) || 0;

            var change;
            if (isNaN(payment) || payment <= totalPayment) {
                change = payment == totalPayment ? 0 : '';
                $('#jumlah_kembalian').val(change);
            } else {
                change = payment - totalPayment;
                $('#jumlah_kembalian').val('Rp' + change.toLocaleString('id-ID'));
            }

            $('[name="jumlah_pembayaran"]').removeClass('is-invalid');
            $('#error-jumlah_pembayaran').text('').hide();
        });

        $('[name="member"]').change(function() {
            var member = $(this).val();
            $('#member_1, #member_2').hide();

            if (member == 1) {
                $('#member_1').show();
            } else if (member == 2) {
                $('#member_2').show();
            }
        });

        $('[name="opsi_pengiriman"]').change(function() {
            if ($(this).val() == 2) {
                $('#opsi_kirim_2').show();
                $('[name="metode_pembayaran"]').val('2').trigger('change');
            } else {
                $('#opsi_kirim_2').hide();
                if ($('[name="metode_pembayaran"]').val() == 2) {
                    $('[name="metode_pembayaran"]').val('').trigger('change');
                }
            }
        });

        $('[name="tanggal_selesai"]').change(function() {
            if ($(this).val()) {
                $('#filter-transaksi').submit();
            }
        });
    });

    function addProductOrder() {
        var checkboxes = $('input[type="checkbox"]:checked');

        if (checkboxes.length > 0) {
            checkboxes.each(function() {
                var productId = $(this).val();
                var productName = $('[name="nama_produk_' + productId + '"]').val();
                var productPrice = parseFloat($('[name="harga_produk_' + productId + '"]').val());
                var productQuantity = 1;

                var isProductExists = false;
                $('#dt-order').DataTable().rows().every(function() {
                    var rowData = this.data();
                    if (rowData[0].includes(productId)) {
                        isProductExists = true;
                        return false;
                    }
                });

                if (!isProductExists) {
                    var newRow = [
                        '<input type="hidden" class="id_produk" name="id_produk[]" value="' + productId +
                        '">' + productName,
                        '<input type="text" class="form-control" name="qty_' + productId +
                        '" placeholder="1" value="' +
                        productQuantity + '" autocomplete="off">',
                        '<select class="form-control select2 select2-danger" name="level_' + productId +
                        '" data-dropdown-css-class="select2-danger" style="width: 100%;">' +
                        '<option value="Tidak Pedas">Tidak Pedas</option>' +
                        '<option value="Pedas Sedang">Pedas Sedang</option>' +
                        '<option value="Sangat Pedas">Sangat Pedas</option>' +
                        '</select>',
                        'Rp' + (productPrice * productQuantity).toLocaleString('id-ID'),
                        '<button type="button" class="btn btn-danger btn-sm" onclick="removeProduct(this)"><i class="fas fa-trash"></i></button>'
                    ];

                    var table = $('#dt-order').DataTable();
                    table.row.add(newRow).draw(false);

                    $('select[name="level_' + productId + '"]').select2({
                        theme: 'bootstrap4'
                    });

                    updateTotalPrice();

                    var total = parseFloat($('#total').text().replace(/[^\d]/g, ''));
                    total += productPrice * productQuantity;
                    $('#total').text('Rp' + total.toLocaleString('id-ID')).trigger('change');

                    $('#form-group-order').show();
                }
            });
        } else {
            showSwalAlert("Pilih Produk", "Tambahkan setidaknya satu produk ke pesanan.", "warning");
        }
    }

    function removeProduct(button) {
        var row = $(button).closest('tr');
        var price = parseFloat(row.find('td:eq(2)').text().replace(/[^\d]/g, ''));
        var quantity = parseInt(row.find('input[type="text"]').val());
        var total = parseFloat($('#total').text().replace(/[^\d]/g, ''));
        total -= price * quantity;
        $('#total').text('Rp' + total.toLocaleString('id-ID'));
        $('#dt-order').DataTable().row(row).remove().draw();
        updateTotalPrice();
        if ($('#dt-order').DataTable().rows().count() === 0) {
            $('#form-group-order').hide();
        }
    }

    function updateTotalPrice() {
        var totalPrice = 0;
        $('#dt-order tbody tr').each(function() {
            var priceText = $(this).find('td:nth-last-child(2)').text().trim().replace(/[^\d]/g, '');
            var price = parseFloat(priceText);
            if (!isNaN(price)) {
                totalPrice += price;
            }
        });
        $('#total_harga').val('Rp' + totalPrice.toLocaleString('id-ID'));
        $('#total_bayar').val('Rp' + totalPrice.toLocaleString('id-ID'));
    }

    function addCart() {
        var productIds = [];
        $('.id_produk').each(function() {
            productIds.push($(this).val());
        });

        if (productIds.length === 0) {
            showSwalAlert("Pilih Produk", "Tambahkan setidaknya satu produk ke pesanan.", "warning");
            return;
        }

        $('[name="status_pesanan"]').val('1');
        $('#form-data').submit();
    }

    function addPayment() {
        var totalPayment = parseInt($('#total_bayar').val().replace(/[^\d]/g, ''));
        var isValid = true;

        $('.id_produk').each(function() {
            var productId = $(this).val();
            var stock = parseInt($('[name="stok_' + productId + '"]').val());
            var productName = $(this).closest('tr').find('td:first').text().trim();
            if (stock <= 0) {
                $('[name="qty_' + productId + '"]').addClass('is-invalid');
                showSwalAlert("Produk Habis", productName + " stok tidak tersedia.", "warning");
                isValid = false;
                return;
            }
        });

        if (isValid) {
            $('[name="total_pembayaran"]').val(totalPayment);
            $('[name="status_pesanan"]').val('3');
            $('#form-data').submit();
        }
    }

    function updatePayment(id) {
        var id_pesanan = $('#id_pesanan_' + id).val();
        var bukti_pembayaran = $('#bukti_pembayaran_' + id).val();

        $('#img-bukti_pembayaran').attr('src', bukti_pembayaran);
        $('#form-update-payment').append('<input type="hidden" name="id" value="' + id_pesanan + '">');

        $('.modal-title').text('Konfirmasi Pembayaran');
        $('#modal-form').modal('show');
    }
</script>
@endsection
