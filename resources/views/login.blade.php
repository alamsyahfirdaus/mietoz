<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>{{ Config::get('constants.APP_NAME') }} | Login</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">
    <link href="{{ asset('assets/img/favicon.ico') }}" rel="icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('assets/dashmin/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/dashmin/css/style.css') }}" rel="stylesheet">
    <style>
        body {
            font-family: Arial;
        }
        .form-control:focus {
            color: #495057;
            background-color: #fff;
            border-color: #28a745;
            outline: 0;
            box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.25);
        }
    </style>
</head>

<body>
    <div class="container-xxl position-relative bg-danger d-flex p-0">
        <div class="container-fluid">
            <div class="row h-100 align-items-center justify-content-center" style="min-height: 100vh;">
                <div class="col-12 col-sm-8 col-md-6 col-lg-5 col-xl-4">
                    <div class="bg-white rounded p-4 p-sm-5 my-4 mx-3">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <a href="javascript:void(0)" style="text-align: center; width: 100%;">
                                <h3 class="text-danger">{{ Config::get('constants.APP_NAME') }}</h3>
                            </a>
                        </div>
                        <form class="mb-3" action="{{ route('login') }}" method="post" enctype="multipart/form-data"
                            id="form-login">
                            @csrf
                            <div id="alert-message"></div>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="username" name="username"
                                    placeholder="Username" autocomplete="off">
                                <label for="username" class="form-label">Username</label>
                                <span id="error-username" class="error invalid-feedback"></span>
                            </div>
                            <div class="form-floating mb-4">
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Password">
                                <label for="password">Password</label>
                                <span id="error-password" class="error invalid-feedback"></span>
                            </div>
                            <button type="submit" class="btn btn-danger py-3 w-100 mb-4 fw-bold">Login</button>
                            {{-- <p class="text-center mb-0">Belum punya Akun? <a class="text-success"
                                    href="javascript:void(0)">Daftar</a></p> --}}
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        $('#form-login').submit(function(e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: $(this).attr('action'),
                data: new FormData($(this)[0]),
                dataType: 'json',
                contentType: false,
                processData: false,
                success: function(response) {
                    window.location.href = '{{ route('home') }}';
                },
                error: function(xhr, status, error) {
                    if (xhr.responseJSON.error) {
                        $("#alert-message")
                            .html('<div class="alert alert-danger" role="alert">' + xhr.responseJSON
                                .error + '</div>')
                            .slideDown()
                            .delay(3000)
                            .slideUp(function() {
                                $(this).empty().show();
                            });
                    } else {
                        $.each(xhr.responseJSON.errors, function(field, messages) {
                            $('[name="' + field + '"]').addClass('is-invalid');
                            $('#error-' + field).text(messages[0]).show();
                            $('[name="' + field + '"]').keyup(function() {
                                $('[name="' + field + '"]').removeClass('is-invalid');
                                $('#error-' + field).text('').hide();
                            });
                        });
                    }
                }
            });
        });
    </script>
</body>

</html>
