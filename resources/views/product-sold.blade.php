@extends('layouts/main')
@section('content')
    <div class="container">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h3 class="card-title">{{ $title }} Terjual</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('product.sold', base64_encode($data->id)) }}" method="POST"
                    enctype="multipart/form-data" id="filter-terjual">
                    @csrf
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="id_produk">Nama Produk</label>
                                <select name="id_produk" id="id_produk" class="form-control select2 select2-danger"
                                    data-dropdown-css-class="select2-danger" style="width: 100%;">
                                    <option value="" disabled>Nama Produk</option>
                                    @foreach ($product as $key => $item)
                                        <option value="{{ base64_encode($item->id) }}"
                                            {{ $item->id == $data->id ? 'selected' : '' }}>
                                            {{ $item->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                <span id="error-id_produk" class="error invalid-feedback"></span>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="tanggal_mulai">Dari Tanggal</label>
                                <select name="tanggal_mulai" id="tanggal_mulai" class="form-control select2 select2-danger"
                                    data-dropdown-css-class="select2-danger" style="width: 100%;">
                                    <option value="">Dari Tanggal</option>
                                    @foreach (App\Models\Order::getOrderDates('asc') as $key => $item)
                                        <option value="{{ $item->tanggal }}"
                                            {{ $item->tanggal == $tanggal_mulai || $key == 0 ? 'selected' : '' }}>
                                            {{ date('d F Y', strtotime($item->tanggal)) }}
                                        </option>
                                    @endforeach
                                </select>
                                <span id="error-tanggal_mulai" class="error invalid-feedback"></span>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="tanggal_selesai">Sampai Tanggal</label>
                                <select name="tanggal_selesai" id="tanggal_selesai"
                                    class="form-control select2 select2-danger" data-dropdown-css-class="select2-danger">
                                    <option value="">Sampai Tanggal</option>
                                    @foreach (App\Models\Order::getOrderDates('desc') as $key => $item)
                                        <option value="{{ $item->tanggal }}"
                                            {{ $item->tanggal == $tanggal_selesai || $key == 0 ? 'selected' : '' }}>
                                            {{ date('d F Y', strtotime($item->tanggal)) }}
                                        </option>
                                    @endforeach
                                </select>
                                <span id="error-tanggal_selesai" class="error invalid-feedback"></span>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <table id="datatable" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 5%; text-align: center;">No</th>
                                        <th>Tanggal</th>
                                        <th>Terjual</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($terjual as $key => $item)
                                        <tr>
                                            <td style="text-align: center;">{{ $key + 1 }}</td>
                                            <td>{{ date('d F Y', strtotime($item->tanggal)) }}</td>
                                            <td>{{ $item->terjual }}</td>
                                            <td></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function() {
            $('[name="id_produk"]').change(function() {
                if ($(this).val()) {
                    var url = '{{ route('product.sold', ':id') }}';
                    url = url.replace(':id', $(this).val());
                    window.location.href = url;
                }
            });

            $('[name="tanggal_selesai"]').change(function() {
                if ($(this).val()) {
                    $('#filter-terjual').submit();
                }
            });
        });
    </script>
@endsection
