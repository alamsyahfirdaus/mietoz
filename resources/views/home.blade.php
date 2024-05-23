@extends('layouts/main')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ App\Models\Product::count() }}</h3>
                        <a href="{{ route('product') }}">Jumlah Produk</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ App\Models\Customer::count() }}</h3>
                        <a href="{{ route('customer') }}">Jumlah Pelanggan</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ App\Models\Order::count() }}</h3>
                        <a href="{{ route('order') }}">Jumlah Transaksi</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        @php
                            $pesanan_online = App\Models\Order::countOrdersOnline();
                        @endphp
                        <h3>{{ $pesanan_online }}</h3>
                        <a href="javascript:void(0)">Pesanan Online</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h5 class="card-title">Pesanan Online</h5>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body collapse">
                        <div class="table-responsive">
                            <table id="datatable" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 5%; text-align: center;">No</th>
                                        <th>ID<span>_</span>Pesanan</th>
                                        <th>Nama<span>_</span>Pembeli</th>
                                        <th>Tanggal<span>_</span>Pesanan</th>
                                        <th>Total<span>_</span>Harga</th>
                                        <th>Bukti<span>_</span>Pembayaran</th>
                                        <th style="width: 5%; text-align: center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order as $key => $item)
                                        <tr>
                                            <td style="text-align: center;">{{ $key + 1 }}</td>
                                            <td>{{ $item->no_transaksi }}</td>
                                            <td>{{ $item->nama_pelanggan ?? ($item->customer->nama ?? '-') }}</td>
                                            <td>{{ date('d F Y', strtotime($item->tanggal_pesanan)) }}</td>
                                            <td>{{ 'Rp' . number_format($item->total_harga, 0, ',', '.') }}</td>
                                            <td style="width: 150px;">
                                                @if (isset($item->payment->bukti_pembayaran))
                                                    <img src="{{ asset('upload_images/' . $item->payment->bukti_pembayaran) }}"
                                                        alt="" style="width: 150px; height: 100px;">
                                                @else
                                                    <span>-</span>
                                                @endif
                                            </td>
                                            <td style="text-align: center;">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-success btn-sm dropdown-toggle"
                                                        data-toggle="dropdown">Aksi </button>
                                                    <div class="dropdown-menu" role="menu">
                                                        <a class="dropdown-item"
                                                            href="{{ route('order.show', ['id' => base64_encode($item->id)]) }}">Konfirmasi
                                                            Bayar</a>
                                                        <div class="dropdown-divider"></div>
                                                        {!! Form::open([
                                                            'route' => ['order.delete', base64_encode($item->id)],
                                                            'method' => 'DELETE',
                                                            'id' => 'remove-' . md5($item->id),
                                                        ]) !!}
                                                        <a class="dropdown-item" href="javascript:void(0)"
                                                            onclick="deleteData('{{ md5($item->id) }}')">Hapus Pesanan</a>
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
            </div>
            <div class="col-lg-12">
                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h5 class="card-title">Grafik Transaksi</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart">
                            <canvas id="grafik-transaksi"
                                style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6"></div>
        </div>
    </div>
    <style>
        .inner a {
            color: #fff;
        }
    </style>
    <script>
        $(function() {
            var salesData = <?php echo json_encode($transaksi); ?>;
            var labels = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];

            var jumlah_transaksi = [];
            var total_pendapatan = [];

            for (var i = 1; i <= 12; i++) {
                jumlah_transaksi.push(salesData[i] ? salesData[i].jumlah_transaksi : 0);
                total_pendapatan.push(salesData[i] ? salesData[i].total_pendapatan : 0);
            }

            var stackedBarChartCanvas = $('#grafik-transaksi').get(0).getContext('2d');

            var stackedBarChartData = {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Transaksi',
                    backgroundColor: '#00a65a',
                    data: jumlah_transaksi
                }, {
                    label: 'Total Pendapatan',
                    backgroundColor: '#00c0ef',
                    data: total_pendapatan
                }]
            };

            var stackedBarChartOptions = {
                responsive: true,
                maintainAspectRatio: false
            };

            new Chart(stackedBarChartCanvas, {
                type: 'bar',
                data: stackedBarChartData,
                options: stackedBarChartOptions
            });
        });
    </script>
@endsection
