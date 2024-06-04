@extends('layouts/main')
@section('content')
    <div class="container">
        <div class="card {{ isset($data) ? 'card-danger card-outline' : '' }}">
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
            <div class="card-body">
                <div class="tab-content">
                    <div class="{{ empty($data) ? 'active' : '' }} tab-pane" id="tab1">
                        <div class="table-responsive">
                            <table id="datatable" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 5%; text-align: center;">No</th>
                                        <th style="width: 5%;">Kode</th>
                                        <th>Nama<span>_</span>Pelanggan</th>
                                        <th>Telepon</th>
                                        <th>Alamat</th>
                                        <th style="width: 5%; text-align: center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($customer as $key => $item)
                                        <tr>
                                            <td style="text-align: center;">{{ $key + 1 }}</td>
                                            <td>{{ $item->kode ?? '-' }}</td>
                                            <td>{{ $item->nama }}</td>
                                            <td>{{ $item->telepon ?? '-' }}</td>
                                            <td>{{ $item->alamat ?? '-' }}</td>
                                            <td style="text-align: center;">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-danger btn-sm dropdown-toggle"
                                                        data-toggle="dropdown">Aksi </button>
                                                    <div class="dropdown-menu" role="menu">
                                                        <a class="dropdown-item"
                                                            href="{{ route('customer.edit', ['id' => base64_encode($item->id)]) }}">Edit</a>
                                                        <div class="dropdown-divider"></div>
                                                        {!! Form::open([
                                                            'route' => ['customer.delete', base64_encode($item->id)],
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
                    <div class="{{ isset($data) ? 'active' : '' }} tab-pane" id="tab2">
                        <form action="{{ route('customer.save', isset($data) ? base64_encode($data->id) : '') }}"
                            method="POST" enctype="multipart/form-data" id="form-data">
                            @csrf
                            @if (isset($data))
                                @method('PUT')
                            @endif
                            <div class="form-group row">
                                <label for="nama" class="col-sm-2 col-form-label">Nama Pelanggan<small
                                        style="color: #dc3545;">*</small></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="nama" id="nama"
                                        placeholder="Masukan Nama" autocomplete="off" value="{{ isset($data) ? $data->nama : '' }}">
                                    <span id="error-nama" class="error invalid-feedback"></span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="telepon" class="col-sm-2 col-form-label">Telepon<small
                                        style="color: #dc3545;">*</small></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="telepon" id="telepon"
                                        placeholder="Masukan Telepon" autocomplete="off" value="{{ isset($data) ? $data->telepon : '' }}">
                                    <span id="error-telepon" class="error invalid-feedback"></span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="alamat" class="col-sm-2 col-form-label">Alamat</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="alamat" id="alamat" placeholder="Masukan Alamat"></textarea>
                                    <span id="error-alamat" class="error invalid-feedback">{{ isset($data) ? $data->alamat : '' }}</span>
                                </div>
                            </div>
                            <div class="form-group row pt-2">
                                <div class="offset-sm-2 col-sm-10">
                                    <a href="javascript:void(0)" onclick="cancelAdd('{{ isset($data) ? 1 : 0 }}')"
                                        class="btn btn-secondary btn-sm">
                                        <i class="fas fa-times-circle"></i> Batal
                                    </a>
                                    <button type="submit" class="btn btn-danger btn-sm float-right"><i
                                            class="fas fa-save"></i>
                                        Simpan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
