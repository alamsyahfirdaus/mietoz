@extends('layouts/main')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-12 {{ Auth::check() ? '' : 'my-4' }}">
                <div class="card direct-chat direct-chat-danger">
                    <div class="card-header">
                        <h3 class="card-title" style="{{ Auth::check() ? '' : 'font-weight: bold;' }}">
                            {{ $title }}</h3>
                        <div class="card-tools">
                            <a href="" type="button" class="btn btn-tool"><i class="fas fa-sync"></i></a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="direct-chat-messages">
                            @foreach ($chat as $item)
                                @php
                                    $isKasir = Auth::check();
                                    $isRight = $isKasir ? $item['id_kasir'] : !$item['id_kasir'];
                                @endphp
                                <div class="direct-chat-msg{{ $isRight ? ' right' : '' }}">
                                    <div class="direct-chat-infos clearfix">
                                        <span class="direct-chat-name float-{{ $isRight ? 'right' : 'left' }}">
                                            {{ $item['id_kasir'] ? $item['nama_kasir'] : $item['nama_pelanggan'] }}
                                        </span>
                                        <span class="direct-chat-timestamp float-{{ $isRight ? 'left' : 'right' }}">
                                            {{ date('d M Y H:i', strtotime($item['tanggal'])) }}
                                        </span>
                                    </div>
                                    <img class="direct-chat-img" src="{{ asset('assets/img/profile.png') }}"
                                        alt="message user image">
                                    <div class="direct-chat-text">{{ $item['message'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="card-footer">
                        <form action="" method="POST" enctype="multipart/form-data" id="chatForm">
                            @csrf
                            <div class="form-group px-0">
                                <div class="input-group">
                                    <input type="text" name="message" id="message" placeholder="Ketik Pesan..."
                                        class="form-control" autocomplete="off">
                                    <span class="input-group-append">
                                        <button type="submit" class="btn btn-danger"><i class="fas fa-paper-plane"></i>
                                            Kirim</button>
                                    </span>
                                    <span id="error-message" class="error invalid-feedback"></span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function() {
            $('#chatForm').submit(function(e) {
                e.preventDefault();
                var message = $('[name="message"]').val().trim();
                if (message === '') {
                    $('[name="message"]').addClass('is-invalid');
                    $('#error-message').text('Pesan tidak boleh kosong.');
                    return;
                }
                this.submit();
            });
            $('[name="message"]').keyup(function() {
                $('[name="message"]').removeClass('is-invalid');
                $('#error-message').text('');
            });
        });
    </script>
@endsection
