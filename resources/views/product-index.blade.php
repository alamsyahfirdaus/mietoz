@extends('layouts/main')
@section('content')
    <div class="container">
        <div class="card {{ isset($data) || session('level') != 1 ? 'card-danger card-outline' : '' }}">
            @if (session('level') == 1)
                @if (empty($data))
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item"><a class="nav-link active" href="#tab1" data-toggle="tab">Daftar
                                    {{ $title }}</a></li>
                            <li class="nav-item"><a class="nav-link" href="#tab2" data-toggle="tab">Tambah
                                    {{ $title }}</a></li>
                        </ul>
                    </div>
                @else
                    <div class="card-header">
                        <h3 class="card-title">Edit {{ $title }}</h3>
                    </div>
                @endif
            @else
                <div class="card-header">
                    <h3 class="card-title">Daftar {{ $title }}</h3>
                </div>
            @endif
            <div class="card-body">
                <div class="tab-content">
                    <div class="{{ empty($data) ? 'active' : '' }} tab-pane" id="tab1">
                        <div class="table-responsive">
                            <table id="datatable" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 5%; text-align: center;">No</th>
                                        <th style="width: 5%;">Kode</th>
                                        <th>Nama<span>_</span>Produk</th>
                                        <th>Kategori</th>
                                        <th>Harga</th>
                                        <th>Diskon</th>
                                        <th>Stok</th>
                                        <th>Terjual</th>
                                        <th>Deskripsi</th>
                                        <th>Gambar</th>
                                        @if (session('level') == 1)
                                            <th style="width: 5%; text-align: center;">Aksi</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($product as $key => $item)
                                        <tr>
                                            <td style="text-align: center;">{{ $key + 1 }}</td>
                                            <td>{{ $item->kode ?? '-' }}</td>
                                            <td>{{ $item->nama }}</td>
                                            <td>{{ $item->category->nama ?? '-' }}</td>
                                            <td>
                                                @php
                                                    $harga_diskon =
                                                        $item->diskon >= 1
                                                            ? intval($item->harga) -
                                                                intval(($item->harga * $item->diskon) / 100)
                                                            : $item->harga;
                                                @endphp
                                                @if ($item->diskon >= 1)
                                                    <del
                                                        class="text-muted">{{ 'Rp' . number_format($item->harga, 0, ',', '.') }}</del>
                                                @endif
                                                <span>{{ 'Rp' . number_format($harga_diskon, 0, ',', '.') }}</span>
                                            </td>
                                            <td>{{ $item->diskon > 0 ? $item->diskon . '%' : '-' }}</td>
                                            <td>
                                                {{ $item->stok - \App\Models\OrderDetail::countProductsSold($item->id) }}
                                            </td>
                                            <td>
                                                <a href="{{ route('product.sold', ['id' => base64_encode($item->id)]) }}"
                                                    type="button"
                                                    class="btn btn-block btn-default btn-sm">{{ \App\Models\OrderDetail::countProductsSold($item->id) }}</a>
                                            </td>
                                            <td>
                                                <span title="{{ isset($item->deskripsi) ? $item->deskripsi : '' }}">
                                                    {{ isset($item->deskripsi) ? Str::limit($item->deskripsi, 50) : '-' }}
                                                </span>
                                            </td>
                                            <td style="width: 150px;">
                                                @if ($item->gambar)
                                                    <img class="" src="{{ asset('upload_images/' . $item->gambar) }}"
                                                        alt="{{ $item->nama }}" style="width: 150px; height: 100px;">
                                                @else
                                                    <span>-</span>
                                                @endif
                                            </td>
                                            @if (session('level') == 1)
                                                <td style="text-align: center;">
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-danger btn-sm dropdown-toggle"
                                                            data-toggle="dropdown">Aksi </button>
                                                        <div class="dropdown-menu" role="menu">
                                                            <a class="dropdown-item"
                                                                href="{{ route('product.edit', ['id' => base64_encode($item->id)]) }}">Edit</a>
                                                            <div class="dropdown-divider"></div>
                                                            {!! Form::open([
                                                                'route' => ['product.delete', base64_encode($item->id)],
                                                                'method' => 'DELETE',
                                                                'id' => 'remove-' . md5($item->id),
                                                            ]) !!}
                                                            <a class="dropdown-item" href="javascript:void(0)"
                                                                onclick="deleteData('{{ md5($item->id) }}')">Hapus</a>
                                                            {!! Form::close() !!}
                                                        </div>
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="{{ isset($data) ? 'active' : '' }} tab-pane" id="tab2">
                        <form action="{{ route('product.save', isset($data) ? base64_encode($data->id) : '') }}"
                            method="POST" enctype="multipart/form-data" id="form-data">
                            @csrf
                            @if (isset($data))
                                @method('PUT')
                            @endif
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="kode">Kode<small style="color: #dc3545;">*</small></label>
                                        <input type="text" class="form-control" name="kode" id="kode"
                                            placeholder="Kode Produk" autocomplete="off" value="{{ $kode }}">
                                        <span id="error-kode" class="error invalid-feedback"></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="nama">Nama Produk<small style="color: #dc3545;">*</small></label>
                                        <input type="text" class="form-control" name="nama" id="nama"
                                            placeholder="Masukan Nama" autocomplete="off"
                                            value="{{ isset($data) ? $data->nama : '' }}">
                                        <span id="error-nama" class="error invalid-feedback"></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="id_kategori">Kategori<small style="color: #dc3545;">*</small></label>
                                        <div class="input-group">
                                            <select name="tanggal_selesai" id="tanggal_selesai"
                                                class="form-control select2 select2-danger"
                                                data-dropdown-css-class="select2-danger" style="width: 450px;">
                                                <option value="">Pilih Kategori</option>
                                                @foreach ($category as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ isset($data) && $data->id_kategori == $item->id ? 'selected' : '' }}>
                                                        {{ $item->nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span class="input-group-append">
                                                <a href="{{ route('category') }}" type="button"
                                                    class="btn btn-default btn-block"
                                                    style="border-top-right-radius: 3px; border-bottom-right-radius: 3px; width: 85px; font-weight: normal;">
                                                    Tambah
                                                </a>
                                            </span>
                                            <span id="error-id_kategori" class="error invalid-feedback"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="harga">Harga<small style="color: #dc3545;">*</small></label>
                                        <input type="text" class="form-control" name="harga" id="harga"
                                            placeholder="Masukan Harga" autocomplete="off"
                                            value="{{ isset($data) ? intval($data->harga) : '' }}">
                                        <span id="error-harga" class="error invalid-feedback"></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="diskon">Diskon</label>
                                        <select name="diskon" id="diskon" class="form-control select2 select2-danger"
                                            data-dropdown-css-class="select2-danger" style="width: 100%;">
                                            <option value="">Persentase Diskon</option>
                                            @for ($i = 1; $i <= 100; $i++)
                                                <option value="{{ $i }}"
                                                    {{ isset($data) && $data->diskon == $i ? 'selected' : '' }}>
                                                    {{ $i }}%</option>
                                            @endfor
                                        </select>
                                        <span id="error-diskon" class="error invalid-feedback"></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="harga_diskon">Harga Diskon</label>
                                        <input type="text" class="form-control" name="harga_diskon" id="harga_diskon"
                                            placeholder="Harga Diskon / Harga Jual" autocomplete="off"
                                            value="{{ isset($data) && $data->diskon > 0 ? number_format(intval($data->harga) - intval(($data->harga * $data->diskon) / 100), 0, ',', '.') : '' }}"
                                            disabled>
                                        <span id="error-harga_diskon" class="error invalid-feedback"></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="deskripsi">Deskripsi</label>
                                        <textarea class="form-control" name="deskripsi" id="deskripsi" placeholder="Masukan Deskripsi">{{ isset($data) ? $data->deskripsi : '' }}</textarea>
                                        <span id="error-deskripsi" class="error invalid-feedback"></span>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    @if (isset($data))
                                        <div class="form-group">
                                            <label for="stok_tersedia">Stok Tersedia</label>
                                            <input type="text" class="form-control" name="stok_tersedia"
                                                id="stok_tersedia" placeholder="Stok Tersedia" autocomplete="off"
                                                value="{{ isset($data) ? $data->stok - $terjual : '' }}" disabled>
                                            <span id="error-stok_tersedia" class="error invalid-feedback"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="stok">Tambah Stok<small
                                                    style="color: #dc3545;">*</small></label>
                                            <input type="text" class="form-control" name="stok" id="stok"
                                                placeholder="Jika tidak akan menambah stok, isi dengan angka nol"
                                                autocomplete="off" value="0">
                                            <span id="error-stok" class="error invalid-feedback"></span>
                                        </div>
                                    @else
                                        <div class="form-group">
                                            <label for="stok">Stok<small style="color: #dc3545;">*</small></label>
                                            <input type="text" class="form-control" name="stok" id="stok"
                                                placeholder="Masukan Stok" autocomplete="off"
                                                value="{{ isset($data) ? $data->stok : '' }}">
                                            <span id="error-stok" class="error invalid-feedback"></span>
                                        </div>
                                    @endif
                                    <div class="form-group">
                                        <label for="gambar">Gambar</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" name="gambar"
                                                    id="gambar" onchange="previewImage(this)">
                                                <label class="custom-file-label" for="gambar">Pilih File</label>
                                            </div>
                                        </div>
                                        <span id="error-gambar" class="error invalid-feedback"></span>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group image-upload-container">
                                                <input type="hidden" name="delete_image" value="0">
                                                <img id="preview-image"
                                                    src="{{ isset($data->gambar) ? asset('upload_images/' . $data->gambar) : '' }}"
                                                    style="max-width: 100%; max-height: 200px; display: {{ empty($data->gambar) ? 'none' : 'block' }};">
                                                <button type="button"
                                                    class="btn btn-default btn-flat btn-sm remove-image-btn"
                                                    id="remove-image"
                                                    style="display: {{ empty($data->gambar) ? 'none' : 'block' }};"><i
                                                        class="fas fa-times"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group pt-2">
                                <a href="javascript:void(0)" onclick="cancelAdd('{{ isset($data) ? 1 : 0 }}')"
                                    class="btn btn-secondary btn-sm">
                                    <i class="fas fa-times-circle"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-danger btn-sm float-right"><i
                                        class="fas fa-save"></i>
                                    Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .image-upload-container {
            position: relative;
            display: inline-block;
        }

        .remove-image-btn {
            position: absolute;
            top: 0;
            right: 0;
            z-index: 10;
        }
    </style>
    <script>
        $('[name="diskon"], [name="harga"]').on('change keyup', function() {
            var harga = $('[name="harga"]').val().replace(/\./g, '');
            var diskon = parseInt($('[name="diskon"]').val()) || 0;
            var hargaDiskon = harga - (harga * (diskon / 100));

            if (isNaN(parseInt(harga)) || harga.trim() === '') {
                $('[name="harga"]').addClass('is-invalid');
                $('#error-harga').text('Harga harus diisi dan berupa angka.').show();
                $('[name="harga_diskon"]').val('');
                return;
            }

            if (harga <= 0) {
                $('[name="harga"]').addClass('is-invalid');
                $('#error-harga').text('Harga harus lebih dari 0.').show();
                $('[name="harga_diskon"]').val('');
                return;
            }

            $('[name="harga"]').removeClass('is-invalid');
            $('#error-harga').text('').hide();

            if (diskon > 0) {
                $('[name="harga_diskon"]').val(hargaDiskon.toLocaleString('id-ID'));
            } else {
                $('[name="harga_diskon"]').val('');
            }
        });
    </script>
@endsection
