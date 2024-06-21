@extends('layouts/main')
@section('content')
    <div class="container">
        @if (count($carousel) >= 1)
            <div class="card my-4" style="border-radius: 8px;">
                <div class="card-body" style="padding: 0;">
                    <div id="carouselIndicators" class="carousel slide" data-ride="carousel">
                        <ol class="carousel-indicators">
                            @foreach ($carousel as $key => $item)
                                @if ($key <= 4)
                                    <li data-target="#carouselIndicators" data-slide-to="{{ $key }}"
                                        class="{{ $key == 0 ? 'active' : '' }}"></li>
                                @endif
                            @endforeach
                        </ol>
                        <div class="carousel-inner" style="border-radius: 8px;">
                            @foreach ($carousel as $key => $item)
                                @if ($key <= 4)
                                    <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                        <img class="d-block w-100" src="{{ asset('upload_images/' . $item->gambar) }}"
                                            alt="{{ $item->judul }}"
                                            style="width: 100%; height: 300px; border-radius: 8px;">
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        <a class="carousel-control-prev" href="#carouselIndicators" role="button" data-slide="prev">
                            <span class="carousel-control-custom-icon" aria-hidden="true" style="border-radius: 8px;">
                                <i class="fas fa-chevron-left"></i>
                            </span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#carouselIndicators" role="button" data-slide="next"
                            style="border-radius: 8px;">
                            <span class="carousel-control-custom-icon" aria-hidden="true">
                                <i class="fas fa-chevron-right"></i>
                            </span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>
                </div>
            </div>
        @endif
        <div class="card card-danger card-outline {{ count($carousel) <= 1 ? 'my-4' : ''  }}">
            <div class="card-header py-2">
                <p class="card-title m-0" style="padding-left: 0; font-size: 20px; font-weight: bold;">Produk</p>
            </div>
            <div class="card-body px-2 pt-0">
                <div class="row">
                    <div class="col-md-6 mb-4 m-lg-0 pl-0">
                        <div class="table-responsive">
                            <table id="dt-product" class="table" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($product->chunk(9) as $chunk)
                                        <tr>
                                            <td>
                                                <div class="row row-cols-lg-3 justify-content-center">
                                                    @foreach ($chunk as $item)
                                                        @php
                                                            $stok =
                                                                $item->stok -
                                                                \App\Models\OrderDetail::countProductsSold($item->id);
                                                        @endphp
                                                        <div class="col mb-3">
                                                            <div class="card h-100">
                                                                <div class="custom-control custom-checkbox position-absolute"
                                                                    style="top: 0.5rem; left: 0.5rem; {{ $stok <= 0 ? 'display: none;' : '' }}">
                                                                    <input
                                                                        class="custom-control-input custom-control-input-danger"
                                                                        type="checkbox" id="{{ 'check_' . md5($item->id) }}"
                                                                        value="{{ base64_encode($item->id) }}">
                                                                    <label for="{{ 'check_' . md5($item->id) }}"
                                                                        class="custom-control-label"></label>
                                                                </div>
                                                                @if ($item->diskon)
                                                                    <div class="badge bg-white position-absolute"
                                                                        style="top: 0.5rem; right: 0.5rem">
                                                                        {{ $item->diskon }}%
                                                                    </div>
                                                                @endif
                                                                <img class="card-img-top"
                                                                    src="{{ $item->gambar ? asset('upload_images/' . $item->gambar) : asset('assets/img/product-450x300.jpg') }}"
                                                                    alt="" style="width: 100%; height: 150px;">
                                                                <div class="card-body">
                                                                    <div class="text-center">
                                                                        <p
                                                                            style="font-weight: bold; margin-bottom: 4px; font-size: 18px;">
                                                                            {{ Str::limit($item->nama, 50) }}
                                                                        </p>
                                                                        <p style="margin-bottom: 4px;">
                                                                            @php
                                                                                $harga_diskon =
                                                                                    $item->diskon >= 1
                                                                                        ? intval($item->harga) -
                                                                                            intval(
                                                                                                ($item->harga *
                                                                                                    $item->diskon) /
                                                                                                    100,
                                                                                            )
                                                                                        : $item->harga;
                                                                            @endphp
                                                                            @if ($item->diskon >= 1 || $stok <= 0)
                                                                                <del
                                                                                    class="text-muted">{{ 'Rp' . number_format($item->harga, 0, ',', '.') }}</del>
                                                                            @endif
                                                                            @if ($stok <= 0)
                                                                                <span>Habis</span>
                                                                            @else
                                                                                <span>{{ 'Rp' . number_format($harga_diskon, 0, ',', '.') }}</span>
                                                                            @endif
                                                                        </p>
                                                                        <p style="font-size: 12px;">Terjual
                                                                            {{ \App\Models\OrderDetail::countMonthlyProductsSold($item->id) }}
                                                                            / Bulan
                                                                        </p>
                                                                    </div>
                                                                    <div style="display: none;">
                                                                        <input type="text" class="nama_produk"
                                                                            name="{{ 'nama_produk_' . base64_encode($item->id) }}"
                                                                            value="{{ $item->nama }}">
                                                                        <input type="text" class="stok"
                                                                            name="{{ 'stok_' . base64_encode($item->id) }}"
                                                                            value="{{ $stok }}">
                                                                        <input type="text" class="harga_produk"
                                                                            name="{{ 'harga_produk_' . base64_encode($item->id) }}"
                                                                            value="{{ $item->diskon >= 1 ? $harga_diskon : intval($item->harga) }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <form action="{{ route('add.order') }}" method="POST" enctype="multipart/form-data" id="form-data"
                            class="pt-3">
                            @csrf
                            <button type="button" class="btn btn-outline-danger mb-3" style="font-weight: bold;"
                                onclick="addProductOrder()"><i class="fas fa-check-double"></i> Daftar
                                Pesanan</button>
                            <div class="table-responsive">
                                <table id="dt-order" class="table table-striped" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Produk</th>
                                            <th style="width: 25%;">Qty</th>
                                            <th>Level</th>
                                            <th>Harga</th>
                                            <th style="width: 5%; color: #fff;">#</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <div id="form-group-order" class="mt-2" style="display: none;">
                                <div class="form-group mb-1">
                                    <label for="total_harga">Total Harga</label>
                                    <input type="text" class="form-control" id="total_harga" name="total_harga"
                                        value="" placeholder="Total Harga" autocomplete="off" disabled>
                                    <span id="error-total_harga" class="error invalid-feedback"></span>
                                </div>
                                <div class="form-group mb-1">
                                    <label for="total_bayar">Total Pembayaran</label>
                                    <input type="text" class="form-control" id="total_bayar" name="total_bayar"
                                        placeholder="Total Pembayaran" value="" autocomplete="off" disabled>
                                    <span id="error-total_bayar" class="error invalid-feedback"></span>
                                </div>
                                <div class="form-group mb-1" style="display: none;">
                                    <label for="opsi_pengiriman">Opsi Pengiriman</label>
                                    <select name="opsi_pengiriman" id="opsi_pengiriman"
                                        class="form-control select2 select2-danger"
                                        data-dropdown-css-class="select2-danger" style="width: 100%;">
                                        <option value="">Opsi Pengiriman</option>
                                        @foreach (['1' => 'Diambil di Toko', '2' => 'Dikirim ke Alamat Tujuan'] as $key => $value)
                                            <option value="{{ $key }}" {{ $key == 1 ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    <span id="error-opsi_pengiriman" class="error invalid-feedback"></span>
                                </div>
                                <div class="form-group mb-1">
                                    <label for="nama_pelanggan">Nama Pembeli</label>
                                    <input type="text" class="form-control" id="nama_pelanggan" name="nama_pelanggan"
                                        placeholder="Nama Pembeli" value="" autocomplete="off">
                                    <span id="error-nama_pelanggan" class="error invalid-feedback"></span>
                                </div>
                                <div class="form-group mb-1">
                                    <label for="telepon_pelanggan">Telepon</label>
                                    <input type="text" class="form-control" id="telepon_pelanggan"
                                        name="telepon_pelanggan" placeholder="Telepon" value=""
                                        autocomplete="off">
                                    <span id="error-telepon_pelanggan" class="error invalid-feedback"></span>
                                </div>
                                <div id="opsi_kirim_2" class="form-group mb-1" style="display: none;">
                                    <label for="keterangan">Keterangan</label>
                                    <textarea class="form-control" name="keterangan" id="keterangan" placeholder="Keterangan (Alamat Pembeli)"
                                        autocomplete="off"></textarea>
                                    <span id="error-keterangan" class="error invalid-feedback"></span>
                                </div>
                                <div class="form-group mt-3">
                                    @foreach ([
            'status_pesanan' => 1,
            'total_pembayaran' => '',
            'metode_pembayaran' => 2,
            'biaya_pengiriman' => '0',
            'member' => 2,
        ] as $key => $value)
                                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                    @endforeach
                                    <button type="button" class="btn btn-outline-danger" onclick="payOrder()"
                                        style="font-weight: bold;"><i class="fas fa-cart-plus"></i>
                                        Buat Pesanan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        #dt-product thead th {
            vertical-align: bottom;
            border: none;
            padding-top: 0;
            display: none;
        }

        #dt-product tbody td {
            border-top: none;
        }

        #dt-product_filter {
            display: none;
        }

        #dt-order thead th {
            border-bottom: none;
        }

        #form-group-order .form-group label {
            font-size: 12px;
        }
    </style>
    <script>
        $(function() {
            var table = $('#dt-product').DataTable({
                "paging": true,
                "pageLength": 1,
                "lengthChange": false,
                "searching": true,
                "ordering": false,
                "info": false,
                "autoWidth": false,
                "responsive": false,
                "language": {
                    "emptyTable": "",
                    "zeroRecords": ""
                }
            });

            $('#search').on('keyup', function() {
                table.search($(this).val()).draw();
            });

            var orderTable = $('#dt-order').DataTable({
                "paging": false,
                "searching": false,
                "info": false,
                "autoWidth": false,
                "responsive": false,
                "ordering": false,
                "language": {
                    "emptyTable": "<small class='text-muted'>Pilih minimal satu produk untuk ditambahkan ke pesanan.</small>",
                }
            });

            $('#dt-order tbody').on('keyup', 'input[type="text"]', function() {
                var id = $(this).attr('name').split('_')[1];
                var stock = parseInt($('[name="stok_' + id + '"]').val());
                var quantity = parseInt($('[name="qty_' + id + '"]').val());
                var price = parseFloat($('[name="harga_produk_' + id + '"]').val());

                if (isNaN(quantity) || quantity <= 0) {
                    $('[name="qty_' + id + '"]').val('');
                    var total = price;
                } else if (quantity > stock) {
                    $('[name="qty_' + id + '"]').val(stock);
                    var total = stock * price;
                } else {
                    var total = quantity * price;
                }

                $('[name="qty_' + id + '"]').closest('tr').find('td:nth-last-child(2)').text('Rp' + total
                    .toLocaleString('id-ID'));

                updateTotalPrice();
            });

            $('[name="metode_pembayaran"]').change(function() {
                if ($(this).val() == 1) {
                    $('#form-group-payment').show();
                } else {
                    $('#form-group-payment').hide();
                }
                $(this).next().find('.select2-selection').removeClass('border border-danger');
                $('#error-metode_pembayaran').text('').hide();
            });

            $('[name="biaya_pengiriman"]').keyup(function() {
                var totalPrice = parseFloat($('#total_harga').val().replace(/[^\d]/g, '')) || 0;
                var additionalCost = parseInt($(this).val());

                if (isNaN(additionalCost) || additionalCost <= 0) {
                    var totalPayment = totalPrice;
                } else {
                    var totalPayment = totalPrice + additionalCost;
                }

                $('#total_bayar').val('Rp' + totalPayment.toLocaleString('id-ID'));

                if ($('[name="jumlah_pembayaran"]').val()) {
                    $('[name="jumlah_pembayaran"]').trigger('keyup');
                }
            });

            $('[name="jumlah_pembayaran"]').keyup(function() {
                var totalPayment = parseFloat($('#total_bayar').val().replace(/[^\d]/g, '')) || 0;
                var payment = parseInt($(this).val().replace(/[^\d]/g, '')) || 0;

                var change;
                if (isNaN(payment) || payment <= totalPayment) {
                    change = payment == totalPayment ? 0 : '';
                    $('#jumlah_kembalian').val(change);
                } else {
                    change = payment - totalPayment;
                    $('#jumlah_kembalian').val('Rp' + change.toLocaleString('id-ID'));
                }

                $('[name="jumlah_pembayaran"]').removeClass('is-invalid');
                $('#error-jumlah_pembayaran').text('').hide();
            });

            $('[name="opsi_pengiriman"]').change(function() {
                if ($(this).val() == 2) {
                    $('#opsi_kirim_2').show();
                } else {
                    $('#opsi_kirim_2').hide();
                }
            });

        });

        function addProductOrder() {
            var checkboxes = $('input[type="checkbox"]:checked');

            if (checkboxes.length > 0) {
                checkboxes.each(function() {
                    var productId = $(this).val();
                    var productName = $('[name="nama_produk_' + productId + '"]').val();
                    var productPrice = parseFloat($('[name="harga_produk_' + productId + '"]').val());
                    var productQuantity = 1;

                    var isProductExists = false;
                    $('#dt-order').DataTable().rows().every(function() {
                        var rowData = this.data();
                        if (rowData[0].includes(productId)) {
                            isProductExists = true;
                            return false;
                        }
                    });

                    if (!isProductExists) {
                        var newRow = [
                            '<input type="hidden" class="id_produk" name="id_produk[]" value="' + productId +
                            '">' + productName,
                            '<input type="text" class="form-control" name="qty_' + productId +
                            '" placeholder="1" value="' +
                            productQuantity + '" autocomplete="off">',
                            '<select class="form-control select2 select2-danger" name="level_' + productId +
                            '" data-dropdown-css-class="select2-danger" style="width: 100%;">' +
                            '<option value="">Level</option>' +
                            '<option value="Tidak Pedas">Tidak Pedas</option>' +
                            '<option value="Pedas Sedang">Pedas Sedang</option>' +
                            '<option value="Sangat Pedas">Sangat Pedas</option>' +
                            '</select>',
                            'Rp' + (productPrice * productQuantity).toLocaleString('id-ID'),
                            '<button type="button" class="btn btn-danger btn-sm" onclick="removeProduct(this)"><i class="fas fa-trash"></i></button>'
                        ];

                        var table = $('#dt-order').DataTable();
                        table.row.add(newRow).draw(false);

                        $('select[name="level_' + productId + '"]').select2({
                            theme: 'bootstrap4'
                        });

                        updateTotalPrice();

                        var total = parseFloat($('#total').text().replace(/[^\d]/g, ''));
                        total += productPrice * productQuantity;
                        $('#total').text('Rp' + total.toLocaleString('id-ID')).trigger('change');

                        $('#form-group-order').show();
                    }
                });
            } else {
                showSwalAlert("Pilih Produk", "Tambahkan setidaknya satu produk ke pesanan.", "warning");
            }
        }

        function removeProduct(button) {
            var row = $(button).closest('tr');
            var price = parseFloat(row.find('td:eq(2)').text().replace(/[^\d]/g, ''));
            var quantity = parseInt(row.find('input[type="text"]').val());
            var total = parseFloat($('#total').text().replace(/[^\d]/g, ''));
            total -= price * quantity;
            $('#total').text('Rp' + total.toLocaleString('id-ID'));
            $('#dt-order').DataTable().row(row).remove().draw();
            updateTotalPrice();
            if ($('#dt-order').DataTable().rows().count() === 0) {
                $('#form-group-order').hide();
            }
        }

        function updateTotalPrice() {
            var totalPrice = 0;
            $('#dt-order tbody tr').each(function() {
                var priceText = $(this).find('td:nth-last-child(2)').text().trim().replace(/[^\d]/g, '');
                var price = parseFloat(priceText);
                if (!isNaN(price)) {
                    totalPrice += price;
                }
            });
            $('#total_harga').val('Rp' + totalPrice.toLocaleString('id-ID'));
            $('#total_bayar').val('Rp' + totalPrice.toLocaleString('id-ID'));
        }

        function payOrder() {
            var totalPayment = parseInt($('#total_bayar').val().replace(/[^\d]/g, ''));
            var isValid = true;

            $('.id_produk').each(function() {
                var productId = $(this).val();
                var stock = parseInt($('[name="stok_' + productId + '"]').val());
                var productName = $(this).closest('tr').find('td:first').text().trim();
                if (stock <= 0) {
                    $('[name="qty_' + productId + '"]').addClass('is-invalid');
                    showSwalAlert("Produk Habis", productName + " stok tidak tersedia.", "warning");
                    isValid = false;
                    return;
                }
            });

            if (isValid) {
                $('[name="total_pembayaran"]').val(totalPayment);
                $('#form-data').submit();
            }
        }
    </script>
@endsection
