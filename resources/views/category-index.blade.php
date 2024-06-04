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
                                        <th>Nama<span>_</span>Kategori</th>
                                        <th>Deskripsi</th>
                                        <th style="width: 5%; text-align: center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($category as $key => $item)
                                        <tr>
                                            <td style="text-align: center;">{{ $key + 1 }}</td>
                                            <td>{{ $item->nama }}</td>
                                            <td>
                                                <span title="{{ isset($item->deskripsi) ? $item->deskripsi : '' }}">
                                                    {{ isset($item->deskripsi) ? Str::limit($item->deskripsi, 50) : '-' }}
                                                </span>
                                            </td>
                                            <td style="text-align: center;">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-danger btn-sm dropdown-toggle"
                                                        data-toggle="dropdown">Aksi </button>
                                                    <div class="dropdown-menu" role="menu">
                                                        <a class="dropdown-item"
                                                            href="{{ route('category.edit', ['id' => base64_encode($item->id)]) }}">Edit</a>
                                                        <div class="dropdown-divider"></div>
                                                        {!! Form::open([
                                                            'route' => ['category.delete', base64_encode($item->id)],
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
                        <form action="{{ route('category.save', isset($data) ? base64_encode($data->id) : '') }}"
                            method="POST" enctype="multipart/form-data" id="form-data">
                            @csrf
                            @if (isset($data))
                                @method('PUT')
                            @endif
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="nama">Nama Kategori<small style="color: #dc3545;">*</small></label>
                                        <input type="text" class="form-control" name="nama" id="nama"
                                            placeholder="Masukan Nama" autocomplete="off"
                                            value="{{ isset($data) ? $data->nama : '' }}">
                                        <span id="error-nama" class="error invalid-feedback"></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="deskripsi">Deskripsi</label>
                                        <textarea class="form-control" name="deskripsi" id="deskripsi" placeholder="Masukan Deskripsi">{{ isset($data) ? $data->deskripsi : '' }}</textarea>
                                        <span id="error-deskripsi" class="error invalid-feedback"></span>
                                    </div>
                                    <div class="form-group pt-2">
                                        {{-- <a href="javascript:void(0)" onclick="cancelAdd('{{ isset($data) ? 1 : 0 }}')"
                                            class="btn btn-secondary btn-sm">
                                            <i class="fas fa-times-circle"></i> Batal
                                        </a> --}}
                                        <a href="{{ route('product') }}"
                                            class="btn btn-secondary btn-sm">
                                            <i class="fas fa-times-circle"></i> Batal
                                        </a>
                                        <button type="submit" class="btn btn-danger btn-sm float-right"><i
                                                class="fas fa-save"></i>
                                            Simpan</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
