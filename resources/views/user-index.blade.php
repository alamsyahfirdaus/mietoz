@extends('layouts/main')
@section('content')
    <div class="container">
        <div class="row">
            @if (session('level') == 3)
                <div class="col-lg-12">
                    <div class="card {{ isset($data) ? 'card-success card-outline' : '' }}">
                        @if (empty($data))
                            <div class="card-header p-2">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"><a class="nav-link active" href="#tab1" data-toggle="tab">Daftar
                                            Admin/Kasir</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#tab2" data-toggle="tab">Tambah
                                            Admin/Kasir</a>
                                    </li>
                                </ul>
                            </div>
                        @else
                            <div class="card-header">
                                <h3 class="card-title">Edit Admin/Kasir</h3>
                            </div>
                        @endif
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="{{ empty($data) ? 'active' : '' }} tab-pane" id="tab1">
                                    <div class="table-responsive">
                                        <table id="datatable" class="table table-bordered table-hover datatable">
                                            <thead>
                                                <tr>
                                                    <th style="width: 5%; text-align: center;">No</th>
                                                    <th>Nama Lengkap</th>
                                                    <th>Email</th>
                                                    <th>Username</th>
                                                    <th>Telepon</th>
                                                    <th style="width: 5%; text-align: center;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($users as $key => $user)
                                                    <tr>
                                                        <td style="text-align: center;">{{ $key + 1 }}</td>
                                                        <td>{{ $user->name }}</td>
                                                        <td>{{ $user->email }}</td>
                                                        <td>{{ $user->username }}</td>
                                                        <td>{{ $user->telephone ?? '-' }}</td>
                                                        <td style="text-align: center;">
                                                            <div class="btn-group">
                                                                <button type="button"
                                                                    class="btn btn-success btn-sm dropdown-toggle"
                                                                    data-toggle="dropdown">Aksi </button>
                                                                <div class="dropdown-menu" role="menu">
                                                                    <a class="dropdown-item"
                                                                        href="{{ route('user.edit', ['id' => base64_encode($user->id)]) }}">Edit</a>
                                                                    @if ($user->id != Auth::id())
                                                                        <div class="dropdown-divider"></div>
                                                                        {!! Form::open([
                                                                            'route' => ['user.delete', base64_encode($user->id)],
                                                                            'method' => 'DELETE',
                                                                            'id' => 'remove-' . md5($user->id),
                                                                        ]) !!}
                                                                        <a class="dropdown-item" href="javascript:void(0)"
                                                                            onclick="deleteData('{{ md5($user->id) }}')">Hapus</a>
                                                                        {!! Form::close() !!}
                                                                    @endif
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
                                    <form action="{{ route('user.save', isset($data) ? base64_encode($data->id) : '') }}"
                                        method="POST" enctype="multipart/form-data" id="form-data">
                                        @csrf
                                        @if (isset($data))
                                            @method('PUT')
                                        @endif
                                        <div class="form-group row">
                                            <label for="name" class="col-sm-2 col-form-label">Nama Lengkap<small
                                                    style="color: #dc3545;">*</small></label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="name" id="name"
                                                    placeholder="Masukan Nama" autocomplete="off"
                                                    value="{{ isset($data) ? $data->name : '' }}">
                                                <span id="error-name" class="error invalid-feedback"></span>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="email" class="col-sm-2 col-form-label">Email<small
                                                    style="color: #dc3545;">*</small></label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="email" id="email"
                                                    placeholder="Masukan Email" autocomplete="off"
                                                    value="{{ isset($data) ? $data->email : '' }}">
                                                <span id="error-email" class="error invalid-feedback"></span>
                                            </div>
                                        </div>
                                        @isset($data)
                                            <div class="form-group row">
                                                <label for="username" class="col-sm-2 col-form-label">Username<small
                                                        style="color: #dc3545;">*</small></label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" name="username" id="username"
                                                        placeholder="Masukan Username" autocomplete="off"
                                                        value="{{ isset($data) ? $data->username : '' }}">
                                                    <span id="error-username" class="error invalid-feedback"></span>
                                                </div>
                                            </div>
                                        @endisset
                                        <div class="form-group row">
                                            <label for="telephone" class="col-sm-2 col-form-label">Telepon<small
                                                    style="color: #dc3545;">*</small></label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="telephone" id="telephone"
                                                    placeholder="Masukan Telepon" autocomplete="off"
                                                    value="{{ isset($data) ? $data->telephone : '' }}">
                                                <span id="error-telephone" class="error invalid-feedback"></span>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="password" class="col-sm-2 col-form-label">Password<small
                                                    style="color: #dc3545;">*</small></label>
                                            <div class="col-sm-10">
                                                <div class="input-group">
                                                    <input type="password" class="form-control" name="password"
                                                        id="password"
                                                        placeholder="{{ isset($data) ? 'Biarkan Password Kosong Jika Tidak Akan Diubah' : 'Password Default (Sama dengan Email)' }}">
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-default"
                                                            id="btn-password"><i class="fas fa-eye-slash"></i></button>
                                                    </div>
                                                </div>
                                                <span id="error-password" class="error invalid-feedback"></span>
                                            </div>
                                        </div>
                                        <div class="form-group row pt-2">
                                            <div class="offset-sm-2 col-sm-10">
                                                <a href="javascript:void(0)"
                                                    onclick="cancelAdd('{{ isset($data) ? 1 : 0 }}')"
                                                    class="btn btn-secondary btn-sm">
                                                    <i class="fas fa-times-circle"></i> Batal
                                                </a>
                                                <button type="submit" class="btn btn-success btn-sm float-right"><i
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
            @empty($data)
                <div class="col-lg-6">
                    <div class="card card-success card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Daftar Rekening</h3>
                            <div class="card-tools">
                                <a href="javascript:void(0)" onclick="addRekening();" class="btn btn-tool"
                                    title="Tambah Rekening">
                                    <i class="fas fa-plus"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="dt-bank" class="table table-bordered table-hover datatable">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%; text-align: center;">No</th>
                                            <th>Nama<span>_</span>Bank</th>
                                            <th>Nomor<span>_</span>Rekening</th>
                                            <th style="width: 5%; text-align: center;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($bank as $key => $item)
                                            <tr>
                                                @php
                                                    $no = $key + 1;
                                                @endphp
                                                <td style="text-align: center;">{{ $key + 1 }}</td>
                                                <td>{{ $item->nama }}</td>
                                                <td>{{ $item->nomor_rekening }}</td>
                                                <td style="text-align: center;">
                                                    <div style="display: none;">
                                                        <input type="text" name="{{ 'id_' . md5($item->id) }}"
                                                            value="{{ base64_encode($item->id) }}">
                                                        <input type="text" name="{{ 'nama_' . md5($item->id) }}"
                                                            value="{{ $item->nama }}">
                                                        <input type="text"
                                                            name="{{ 'nomor_rekening_' . md5($item->id) }}"
                                                            value="{{ $item->nomor_rekening }}">
                                                    </div>
                                                    <div class="btn-group">
                                                        <button type="button"
                                                            class="btn btn-success btn-sm dropdown-toggle"
                                                            data-toggle="dropdown">Aksi </button>
                                                        <div class="dropdown-menu" role="menu">
                                                            <a class="dropdown-item" href="javascript:void(0)"
                                                                onclick="editRekening('{{ md5($item->id) }}')">Edit</a>
                                                            <div class="dropdown-divider"></div>
                                                            {!! Form::open([
                                                                'route' => ['bank.delete', base64_encode($item->id)],
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
                </div>
                <div class="col-lg-6">
                    <div class="card card-success card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Daftar Banner</h3>
                            <div class="card-tools">
                                <a href="javascript:void(0)" onclick="addBanner();" class="btn btn-tool"
                                    title="Tambah Banner">
                                    <i class="fas fa-plus"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover datatable">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%; text-align: center;">No</th>
                                            <th>Judul</th>
                                            <th>Gambar</th>
                                            <th style="width: 5%; text-align: center;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($carousel as $key => $item)
                                            <tr>
                                                <td style="text-align: center;">{{ $key + 1 }}</td>
                                                <td>{{ $item->judul ?? '-' }}</td>
                                                <td style="width: 150px;">
                                                    <img class=""
                                                        src="{{ asset('upload_images/' . $item->gambar) }}"
                                                        alt="{{ $item->judul }}" style="width: 150px; height: 100px;">
                                                </td>
                                                <td style="text-align: center;">
                                                    <div class="btn-group">
                                                        <button type="button"
                                                            class="btn btn-success btn-sm dropdown-toggle"
                                                            data-toggle="dropdown">Aksi </button>
                                                        <div class="dropdown-menu" role="menu">
                                                            {!! Form::open([
                                                                'route' => ['carousel.delete', base64_encode($item->id)],
                                                                'method' => 'DELETE',
                                                                'id' => 'remove-' . md5('carousel.delete'),
                                                            ]) !!}
                                                            <a class="dropdown-item" href="javascript:void(0)"
                                                                onclick="deleteData('{{ md5('carousel.delete') }}')">Hapus</a>
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
            @endempty
        @else
            <div class="col-lg-12">
                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Daftar Banner</h3>
                        <div class="card-tools">
                            <a href="javascript:void(0)" onclick="addBanner();" class="btn btn-tool"
                                title="Tambah Banner">
                                <i class="fas fa-plus"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover datatable">
                                <thead>
                                    <tr>
                                        <th style="width: 5%; text-align: center;">No</th>
                                        <th>Judul</th>
                                        <th>Gambar</th>
                                        <th style="width: 5%; text-align: center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($carousel as $key => $item)
                                        <tr>
                                            <td style="text-align: center;">{{ $key + 1 }}</td>
                                            <td>{{ $item->judul ?? '-' }}</td>
                                            <td style="width: 150px;">
                                                <img class=""
                                                    src="{{ asset('upload_images/' . $item->gambar) }}"
                                                    alt="{{ $item->judul }}" style="width: 150px; height: 100px;">
                                            </td>
                                            <td style="text-align: center;">
                                                <div class="btn-group">
                                                    <button type="button"
                                                        class="btn btn-success btn-sm dropdown-toggle"
                                                        data-toggle="dropdown">Aksi </button>
                                                    <div class="dropdown-menu" role="menu">
                                                        {!! Form::open([
                                                            'route' => ['carousel.delete', base64_encode($item->id)],
                                                            'method' => 'DELETE',
                                                            'id' => 'remove-' . md5('carousel.delete'),
                                                        ]) !!}
                                                        <a class="dropdown-item" href="javascript:void(0)"
                                                            onclick="deleteData('{{ md5('carousel.delete') }}')">Hapus</a>
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
        @endif
    </div>
</div>

<div class="modal fade" id="modal-form">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data" id="form-show" class="modal-form">
                @csrf
                <div class="modal-body"></div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"><i
                            class="fas fa-times-circle"></i> Batal</button>
                    <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-save"></i>
                        Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(function() {
        @if (empty($data))
            $('#email').keyup(function(e) {
                $('#password').val($(this).val());
            });
        @endif
    });

    function createInput(attributes) {
        return $('<input>').attr(attributes);
    }

    function createFormGroup(labelText, inputName, inputPlaceholder, inputValue = '', showAsterisk = true) {
        var input = createInput({
            type: 'text',
            name: inputName,
            class: 'form-control',
            id: inputName,
            placeholder: inputPlaceholder,
            autocomplete: 'off',
            value: inputValue
        });

        var label = $('<label>').attr('for', inputName).text(labelText);

        if (showAsterisk) {
            label.append($('<small>').css('color', '#dc3545').text('*'));
        }

        var span = $('<span>').attr('id', 'error-' + inputName).addClass('error invalid-feedback');

        return $('<div>').addClass('form-group').append(label, input, span);
    }

    function createFormGroupFile(labelText, inputName, inputPlaceholder) {
        var input = $('<input>').attr({
            type: 'file',
            class: 'custom-file-input',
            name: inputName,
            id: inputName,
        }).on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass('selected').html(fileName);
        });

        var label = $('<label>').addClass('custom-file-label').attr('for', inputName).text(inputPlaceholder);

        var divInput = $('<div>').addClass('custom-file').append(input, label);

        var divInputGroup = $('<div>').addClass('input-group').append(divInput);

        var spanError = $('<span>').attr('id', 'error-' + inputName).addClass('error invalid-feedback');

        return $('<div>').addClass('form-group').append(
            $('<label>').attr('for', inputName).text(labelText).append(
                $('<small>').css('color', '#dc3545').text('*')
            ),
            divInputGroup,
            spanError
        );
    }

    function addRekening() {
        $('.modal-body').empty();

        var divFormGroupNama = createFormGroup('Nama Bank', 'nama', 'Masukkan Nama');

        var divFormGroupNomorRekening = createFormGroup('Nomor Rekening', 'nomor_rekening', 'Masukkan Nomor');

        $('.modal-body').append(divFormGroupNama, divFormGroupNomorRekening);
        $('#form-show').attr('action', '{{ route('bank.save') }}');
        $('#form-show').attr('method', 'POST').append('@method('PUT')');

        $('.modal-title').text('Tambah Rekening');
        $('#modal-form').modal('show');
    }

    function editRekening(id) {
        $('.modal-body').empty();

        var inputId = createInput({
            type: 'hidden',
            name: 'id',
            value: $('[name="id_' + id + '"]').val()
        });

        var divFormGroupNama = createFormGroup('Nama Bank', 'nama', 'Masukkan Nama', $('[name="nama_' + id +
                '"]')
            .val());

        var divFormGroupNomorRekening = createFormGroup('Nomor Rekening', 'nomor_rekening', 'Masukkan Nomor', $(
            '[name="nomor_rekening_' + id + '"]').val());

        $('.modal-body').append(inputId, divFormGroupNama, divFormGroupNomorRekening);

        $('.modal-title').text('Edit Rekening');
        $('#form-show').attr('action', '{{ route('bank.save') }}');
        $('#form-show').attr('method', 'POST').append('@method('PUT')');
        $('#modal-form').modal('show');
    }

    function addBanner() {
        $('.modal-body').empty();

        var divFormGroupJudul = createFormGroup('Judul', 'judul', 'Masukkan Judul', '', false);

        var divFormGroupGambar = createFormGroupFile('Gambar', 'gambar', 'Pilih File');

        $('.modal-body').append(divFormGroupJudul, divFormGroupGambar);
        $('#form-show').attr('action', '{{ route('carousel.save') }}');

        $('.modal-title').text('Tambah Banner');
        $('#modal-form').modal('show');
    }
</script>
@endsection
