<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ Config::get('constants.APP_NAME') }} {{ Auth::Check() ? '| ' . $title : 'PEDAS PAGADEN' }}</title>
    <link href="{{ asset('assets/img/favicon.ico') }}" rel="icon">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/dist/css/adminlte.css') }}">
    <style>
        body {
            font-family: Arial;
        }

        .navbar-success {
            border-bottom: 1px solid #28a745;
        }

        a,
        a:hover {
            color: #28a745;
            text-decoration: none;
            background-color: transparent;
        }

        .dropdown-item.active,
        .dropdown-item:active {
            color: #fff;
            text-decoration: none;
            background-color: #28a745;
        }

        .brand-text {
            font-weight: bold;
        }

        .main-footer div a {
            color: #869099;
        }

        .content-wrapper {
            background-color: #ffffff;
        }

        th span {
            color: #ffffff;
        }

        .card-footer .btn,
        .modal-footer .btn,
        td .btn-group .btn,
        .bs-stepper-content div .btn,
        .tab-pane .btn {
            font-weight: bold;
        }

        .page-item.active .page-link {
            background-color: #28a745;
            border-color: #28a745;
        }

        .page-link:hover {
            z-index: 2;
            color: #28a745;
            text-decoration: none;
            background-color: #e9ecef;
            border-color: #dee2e6;
        }

        .page-link:focus {
            z-index: 3;
            outline: 0;
            box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.25);
        }

        .page-link {
            position: relative;
            display: block;
            padding: 0.5rem 0.75rem;
            margin-left: -1px;
            line-height: 1.25;
            color: #28a745;
            background-color: #fff;
            border: 1px solid #dee2e6;
        }

        .form-control:focus {
            color: #495057;
            background-color: #fff;
            border-color: #28a745;
            outline: 0;
            box-shadow: inset 0 0 0 rgba(0, 0, 0, 0);
        }

        .form-control:disabled {
            background-color: #ffffff;
            opacity: 1;
        }

        .nav-pills .nav-link.active,
        .nav-pills .show>.nav-link {
            color: #fff;
            background-color: #28a745;
        }

        .nav-pills .nav-link:not(.active):hover {
            color: #28a745;
        }

        .nav-pills li a {
            font-weight: bold;
        }

        .nav-pills .nav-link {
            color: #212529;
        }
    </style>
</head>

<body class="hold-transition layout-top-nav">
    <div class="wrapper">
        <nav class="main-header navbar navbar-expand-md navbar-success navbar-dark">
            <div class="container {{ Auth::check() ? '' : 'px-2 py-0' }}">
                <a href="/" class="navbar-brand" style="{{ Auth::check() ? '' : 'font-size: 24px;' }}">
                    <span class="brand-text">{{ Config::get('constants.APP_NAME') }}</span>
                </a>
                @if (Auth::check())
                    <button class="navbar-toggler order-1" type="button" data-toggle="collapse"
                        data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false"
                        aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse order-3" id="navbarCollapse">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a href="{{ route('home') }}"
                                    class="nav-link {{ Request::segment(1) == 'home' ? 'active' : '' }}">Beranda</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('product') }}"
                                    class="nav-link {{ Request::segment(1) == 'product' ? 'active' : '' }}">Produk</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('customer') }}"
                                    class="nav-link {{ Request::segment(1) == 'customer' ? 'active' : '' }}">Pelanggan</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('order') }}"
                                    class="nav-link {{ Request::segment(1) == 'order' ? 'active' : '' }}">Penjualan</a>
                            </li>
                        </ul>
                    </div>
                @else
                    @if (Request::segment(1) == 'shop')
                        <div class="order-1 order-md-3 ml-md-5">
                            <div class="input-group">
                                <input type="text" class="form-control form-control-navbar" name="search"
                                    id="search" placeholder="Cari Produk" autocomplete="off">
                            </div>
                        </div>
                    @endif
                @endif

                <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
                    @if (Auth::check())
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="javascript:void(0)"
                                id="user-profile">
                                <i class="fas fa-user-alt fa-fw"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a href="javascript:void(0)" class="dropdown-item">
                                    <i class="fas fa-user mr-2"></i> {{ Str::limit(Auth::user()->name, 20) }}
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="{{ route('home.setting') }}" class="dropdown-item">
                                    <i class="fas fa-cog mr-2"></i> Pengaturan
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="{{ route('logout') }}" class="dropdown-item">
                                    <i class="fas fa-power-off mr-2"></i> Log Out
                                </a>
                            </div>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="javascript:void(0)"
                                id="user-profile">
                                <i class="fas fa-user-alt fa-fw"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a href="javascript:void(0)" onclick="confirmPayment()" class="dropdown-item">
                                    <i class="fas fa-exchange-alt mr-2"></i> Konfirmasi Bayar
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="{{ route('login') }}" class="dropdown-item">
                                    <i class="fas fa-sign-in-alt mr-2"></i> Login Admin/Kasir
                                </a>
                            </div>
                        </li>
                    @endif
                </ul>
            </div>
        </nav>

        <div class="content-wrapper">
            @if (Auth::check() && session('role') == 1)
                <div class="content-header">
                    <div class="container">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1 class="m-0"> {{ $title }}</h1>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <div class="content">
                <script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
                <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
                <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
                <script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
                <script src="{{ asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
                <script src="{{ asset('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
                <script src="{{ asset('assets/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
                <script src="{{ asset('assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
                <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
                <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
                <script src="{{ asset('assets/plugins/chart.js/Chart.min.js') }}"></script>
                <script src="{{ asset('assets/dist/js/adminlte.min.js') }}"></script>
                <script src="{{ asset('assets/dist/js/adminlte.js') }}"></script>
                @yield('content')
                @if (session()->has('success') || session()->has('error'))
                    <script>
                        $(function() {
                            @if (session()->has('success'))
                                showSwalAlert("Berhasil!", "{{ session('success') }}", "success");
                            @elseif (session()->has('error'))
                                showSwalAlert("Gagal!", "{{ session('error') }}", "error");
                            @endif
                        });
                    </script>
                @endif
                @if (!Auth::check())
                    <div class="modal fade" id="modal-form">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title"></h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="{{ route('home.payment') }}" method="POST"
                                    enctype="multipart/form-data" id="form-data">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="no_transaksi">No Transaksi<small
                                                    style="color: #dc3545;">*</small></label>
                                            <input type="text" class="form-control" name="no_transaksi"
                                                id="no_transaksi" placeholder="No Transaksi / ID Pesanan"
                                                autocomplete="off" value="">
                                            <span id="error-no_transaksi" class="error invalid-feedback"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="bukti_pembayaran">Bukti Pembayaran<small
                                                    style="color: #dc3545;">*</small></label>
                                            <div class="input-group">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input"
                                                        name="bukti_pembayaran" id="bukti_pembayaran"
                                                        onchange="previewImage(this)">
                                                    <label class="custom-file-label" for="bukti_pembayaran">Pilih
                                                        Foto</label>
                                                </div>
                                            </div>
                                            <span id="error-bukti_pembayaran" class="error invalid-feedback"></span>
                                        </div>
                                    </div>
                                    <div class="modal-footer justify-content-start">
                                        <button type="submit" class="btn btn-success btn-sm"><i
                                                class="fas fa-angle-double-up"></i>
                                            Kirim Bukti Bayar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <script>
                        function confirmPayment() {
                            $('#form-data .form-control').val('').change();
                            $('#form-data .form-control').removeClass('is-invalid');
                            $('.modal-title').text('Konfirmasi Bayar');
                            $('#modal-form').modal('show');
                        }
                    </script>
                @endif
            </div>
        </div>

        <aside class="control-sidebar control-sidebar-dark"></aside>
        <footer class="main-footer no-print">
            <div class="float-right d-none d-sm-inline"></div>
            <div>Copyright &copy; 2024-{{ date('Y') }} <a
                    href="javascript:void(0)">{{ Config::get('constants.FOOTER_NAME') }}</a>.</div>
        </footer>
    </div>
    <script>
        $(function() {
            $('#datatable, .datatable').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": false,
                "order": [],
                "columnDefs": [{
                    "targets": [-1],
                    "orderable": false,
                }],
                "language": {
                    "emptyTable": "Tidak ada data yang tersedia dalam tabel",
                    // "infoEmpty": "Menampilkan 0 hingga 0 dari 0 entri",
                    // "infoFiltered": "(disaring dari total _MAX_ entri)",
                    // "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
                    // "lengthMenu": "Tampilkan _MENU_ entri per halaman",
                    "search": "Cari:",
                    "zeroRecords": "Tidak ada data yang cocok dengan pencarian Anda",
                    // "paginate": {
                    //     "first": "Pertama",
                    //     "last": "Terakhir",
                    //     "next": "Selanjutnya",
                    //     "previous": "Sebelumnya"
                    // }
                }
            }).on('order.dt search.dt', function() {
                $('#datatable tbody tr').each(function(index) {
                    $(this).find('td:first').text(index + 1);
                });
            });

            $('.select2').select2();
            // $('.datetimepicker-input').datetimepicker({
            //     format: 'DD/MM/YYYY'
            // });
            $('#btn-password').click(function() {
                var passwordField = $('#password');
                var passwordFieldType = passwordField.attr('type');
                if (passwordFieldType === 'password') {
                    passwordField.attr('type', 'text');
                    $(this).find('i').removeClass('fas fa-eye-slash').addClass('fas fa-eye');
                } else {
                    passwordField.attr('type', 'password');
                    $(this).find('i').removeClass('fas fa-eye').addClass('fas fa-eye-slash');
                }
            });
        });
        $('#form-data, .modal-form').submit(function(e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: $(this).attr('action'),
                data: new FormData($(this)[0]),
                dataType: 'json',
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.success) {
                        @if (Auth::Check())
                            showSwalAlert("Berhasil!", response.message, "success");
                            if (response.previous) {
                                $('#modal-form').modal('hide');
                                setTimeout(function() {
                                    window.location.reload();
                                }, 2250);
                            } else {
                                setTimeout(function() {
                                    window.location.href = "{{ url()->previous() }}";
                                }, 2250);
                            }
                        @else
                            if (response.previous) {
                                showSwalAlert("Berhasil!", response.message, "success");
                                $('#modal-form').modal('hide');
                                setTimeout(function() {
                                    window.location.reload();
                                }, 2250);
                            } else {
                                window.location.href = response.url;
                            }
                        @endif
                    } else {
                        showSwalAlert("Gagal!", response.message, "error");
                    }
                },
                error: function(xhr, status, error) {
                    $.each(xhr.responseJSON.errors, function(field, messages) {
                        var $field = $('[name="' + field + '"]');
                        $field.addClass('is-invalid');
                        $('#error-' + field).text(messages[0]).show();
                        $field.on('keyup change', function() {
                            $(this).removeClass('is-invalid');
                            $('#error-' + field).text('').hide();
                        });
                        if ($field.hasClass('select2')) {
                            $field.next().find('.select2-selection').addClass(
                                'border border-danger');
                            $field.change(function() {
                                $(this).next().find('.select2-selection')
                                    .removeClass('border border-danger');
                            });
                        }
                    });
                }
            });
        });

        $('input[type="file"].custom-file-input').change(function(e) {
            const fileName = e.target.value.split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
            previewImage(e.target);
        });

        function showSwalAlert(title, text, icon) {
            Swal.fire({
                title: title,
                text: text,
                icon: icon,
                showConfirmButton: false,
                timer: 1750
            });
        }

        $('#remove-image').click(function() {
            $('#preview-image').hide().attr('src', '');
            $('[name="gambar"]').val('').next('.custom-file-label').html('Pilih File');
            $('[name="delete_image"]').val('1');
            $(this).hide();
        });

        function previewImage(input) {
            const preview = document.getElementById('preview-image');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $(preview).show().attr('src', e.target.result);
                    $('#remove-image').show();
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                $(preview).hide().attr('src', '');
                $('#remove-image').hide();
            }
        }

        function deleteData(id) {
            Swal.fire({
                title: "Apakah Anda yakin?",
                text: "Anda tidak akan bisa mengembalikannya!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#28a745",
                cancelButtonColor: "#dc3545",
                confirmButtonText: "Ya, hapus!",
                cancelButtonText: "Tidak, batalkan!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#remove-' + id).submit();
                }
            });
        }

        function cancelAdd(id) {
            if (id > 0) {
                window.location.href = "{{ url()->previous() }}";
            } else {
                $('a[href="#tab2"]').removeClass('active');
                $('a[href="#tab1"]').addClass('active');
                $('#tab2').removeClass('active show');
                $('#tab1').addClass('active show');
                $('#form-data .form-control').val('').change().removeClass('is-invalid');
            }
        }
    </script>
</body>

</html>