@extends('layouts/main')
@section('content')
    <div class="container {{ Auth::check() ? '' : 'py-4' }}">
        @if (Session::has('success_message'))
            <div class="alert alert-success" style="font-weight: bold;">
                {{ Session::get('success_message') }}
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="invoice p-3 mb-3">
                    <div class="row">
                        <div class="col-12 border-bottom">
                            <h4>
                                <span style="font-weight: bold;">{{ Config::get('constants.APP_NAME') }} PEDAS PAGADEN</span>
                                <small class="float-right" style="font-size: 18px;">Tanggal:
                                    {{ date('d F Y', strtotime($data['order']->tanggal_pesanan)) }}</small>
                            </h4>
                        </div>
                    </div>
                    <div class="row invoice-info">
                        <div class="table-responsive">
                            <table class="table" style="width: 100%;">
                                <tr>
                                    <td class="px-2 pb-1 border-0" style="width: 50%;">Kepada</td>
                                    <td class="px-2 pb-1 border-0" style="width: 50%;"></td>
                                </tr>
                                <tr>
                                    <td class="px-2 py-1 border-0">
                                        <strong>{{ $data['order']->nama_pelanggan ?? ($data['order']->customer->nama ?? '-') }}</strong>
                                    </td>
                                    <td class="px-2 py-1 border-0"><b>ID Pesanan:</b> <a href="javascript:void(0)"
                                            class="copy-link"
                                            style="text-decoration: none; color: #333;"><i
                                            class="fas fa-copy"></i> {{ $data['order']->no_transaksi }}</a><br>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-2 py-1 border-0">Telepon:
                                        {{ $data['order']->telepon_pelanggan ?? ($data['order']->customer->telepon ?? '-') }}
                                    </td>
                                    <td class="px-2 py-1 border-0">Tanggal Bayar:
                                        @php
                                            $tanggal_bayar = Auth::check()
                                                ? $data['payment']->tanggal_pembayaran ??
                                                    $data['order']->tanggal_pesanan
                                                : $data['payment']->tanggal_pembayaran ?? null;
                                        @endphp
                                        {{ $tanggal_bayar != null ? date('d F Y', strtotime($tanggal_bayar)) : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-2 py-1 border-0">
                                        {{ $data['order']->keterangan ? 'Keterangan / Alamat: ' . $data['order']->keterangan : 'Keterangan: -' }}
                                    </td>
                                    @php
                                        $metode_pembayaran = ['Tunai', 'Transfer Bank'];
                                        $status_pesanan = ['Belum Bayar', 'Dikirim', 'Selesai'];
                                    @endphp
                                    <td class="px-2 py-1 border-0">
                                        @if (Auth::check())
                                            Metode Pembayaran:
                                            {{ $metode_pembayaran[$data['payment']->metode_pembayaran - 1] ?? '-' }} -
                                            {{ $status_pesanan[$data['order']->status_pesanan - 1] ?? '-' }}
                                        @else
                                            Status Pembayaran:
                                            {{ $status_pesanan[$data['order']->status_pesanan - 1] ?? '-' }}
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 10%;">Qty</th>
                                        <th style="width: 25%;">Produk</th>
                                        <th>Level</th>
                                        <th style="width: 25%;">Sub<span>_</span>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data['order_detail'] as $item)
                                        <tr>
                                            <td>{{ $item->jumlah_produk ?? 0 }}</td>
                                            <td>{{ $item->product->nama ?? '-' }}</td>
                                            <td>{{ $item->level ?? '-' }}</td>
                                            <td>{{ 'Rp' . number_format($item->jumlah_produk * $item->harga_satuan, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="table-responsive">
                                <table class="table" style="width: 100%;">
                                    <tr>
                                        <td class="px-2 py-2 border-0" style="width:50%">
                                            <ul class="list-group list-group-unbordered"
                                                style="{{ Auth::check() ? 'display: none;' : '' }}">
                                                <li class="list-group-item">
                                                    <b>Metode Pembayaran</b>
                                                    <p class="float-right m-0">
                                                        {{ $metode_pembayaran[$data['payment']->metode_pembayaran - 1] ?? '-' }}
                                                    </p>
                                                </li>
                                                @foreach ($data['bank'] as $key => $item)
                                                    <li class="list-group-item">
                                                        <span>{{ $item->nama }}</span>
                                                        <a href="javascript:void(0)" class="copy-link float-right"
                                                            style="text-decoration: none; color: #333;"><i
                                                                class="fas fa-copy"></i> {{ $item->nomor_rekening }}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td class="px-2 py-2 border-0" style="width:50%">
                                            <ul class="list-group list-group-unbordered">
                                                @if (intval($data['order']->biaya_pengiriman) >= 1)
                                                    <li class="list-group-item">
                                                        <b>Total Harga</b>
                                                        <p class="float-right m-0">
                                                            {{ 'Rp' . number_format($data['order']->total_harga, 0, ',', '.') }}
                                                        </p>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <b>Biaya Pengiriman</b>
                                                        <p class="float-right m-0">
                                                            {{ $data['order']->biaya_pengiriman ? 'Rp' . number_format($data['order']->biaya_pengiriman, 0, ',', '.') : '-' }}
                                                        </p>
                                                    </li>
                                                @endif
                                                <li class="list-group-item">
                                                    <b>Total Pembayaran</b>
                                                    <p class="float-right m-0">
                                                        {{ 'Rp' . number_format($data['payment']->total_pembayaran, 0, ',', '.') }}
                                                    </p>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row no-print mt-3">
                        <div class="col-12">
                            <button type="button" class="btn btn-default btn-sm" id="generate-pdf-btn"
                                style="font-weight: bold;">
                                <i class="fas fa-download"></i> Generate PDF
                            </button>
                            @if (Auth::check())
                                @if ($data['payment']->metode_pembayaran == 2 && $data['order']->status_pesanan != 3)
                                    <form action="{{ route('order.update') }}" method="POST" enctype="multipart/form-data"
                                        style="display: inline;">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="id"
                                            value="{{ base64_encode($data['order']->id) }}">
                                        <button type="submit" class="btn btn-default btn-sm" style="font-weight: bold;">
                                            <i class="fas fa-exchange-alt"></i> Konfirmasi Bayar
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('order.invoice', ['id' => base64_encode($data['order']->id)]) }}"
                                    rel="noopener" class="btn btn-danger btn-sm float-right" style="font-weight: bold;"><i
                                        class="fas fa-print"></i> Cetak
                                    Nota</a>
                            @else
                                <a href="javascript:void(0)" onclick="confirmPayment()" rel="noopener"
                                    class="btn btn-danger btn-sm float-right" style="font-weight: bold;"><i
                                        class="fas fa-exchange-alt"></i> Konfirmasi Bayar</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <style>
            @media print {
                @page {
                    margin-top: 24px;
                    margin-bottom: 0;
                }

                body::after {
                    content: none !important;
                }
            }
        </style>
        <script>
            $(function() {
                $('.alert').delay(2750).slideUp('slow', function() {
                    $(this).remove();
                });

                $('#generate-pdf-btn').click(function() {
                    var originalUrl = window.location.href;
                    window.history.replaceState({}, document.title, "/");
                    window.print();
                    window.history.replaceState({}, document.title, originalUrl);
                });

                $('.copy-link').click(function() {
                    var $temp = $('<input>');
                    $('body').append($temp);
                    $temp.val($(this).text()).select();
                    document.execCommand('copy');
                    $temp.remove();
                });

            });
        </script>
    @endsection
