<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Nota Pembayaran</title>
    <style>
        @page {
            size: 80mm 297mm;
            margin: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f9f9f9;
            color: #333;
        }

        .container {
            max-width: 80mm;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin: 0;
            color: #333;
        }

        p,
        li {
            margin: 5px 0;
            font-size: 12px;
            text-align: justify;
        }

        ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        strong {
            font-weight: bold;
        }

        hr {
            border: none;
            border-top: 1px solid #ccc;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>MIE TOZ</h2>
        <span style="font-size: 8px; margin-bottom: 0; padding: 0; display: block; text-align: center;">Jl. Subang Pamanukan Food Court, Sukamulya, Kec. Pagaden, Kab. Subang | Telepon: 085719375279</span>
        <hr>
        <p><strong>Transaksi ID:</strong> {{ $data['order']->no_transaksi }}</p>
        <p><strong>Tanggal Pembayaran:</strong> {{ date('d F Y', strtotime($data['payment']->tanggal_pembayaran)) }}</p>
        @php
            $payment_methods = ['Tunai', 'Transfer Bank'];
        @endphp
        <p><strong>Metode Pembayaran:</strong> {{ $payment_methods[$data['payment']->metode_pembayaran - 1] ?? '-' }}
        </p>
        <p><strong>Total Pembayaran:</strong>
            {{ 'Rp' . number_format($data['payment']->total_pembayaran, 0, ',', '.') }}</p>

        @php
            $total_harga = 0;
        @endphp
        <hr>
        <ul>
            @foreach ($data['order_detail'] as $item)
                <li>
                    <strong>{{ $item->product->nama }}</strong> ({{ $item->level }}) - {{ $item->jumlah_produk }} pcs
                    <br>
                    <span style="font-style: italic;">Harga:</span>
                    {{ 'Rp' . number_format($item->harga_satuan, 0, ',', '.') }}
                    <br>
                    <span style="font-style: italic;">Subtotal:</span>
                    {{ 'Rp' . number_format($item->jumlah_produk * $item->harga_satuan, 0, ',', '.') }}
                </li>
                @php
                    $total_harga += $item->jumlah_produk * $item->harga_satuan;
                @endphp
            @endforeach
            <li><strong>Total Harga:</strong> {{ 'Rp' . number_format($total_harga, 0, ',', '.') }}</li>
            @if ($data['order']->biaya_pengiriman >= 1)
                <li><strong>Biaya Pengiriman:</strong>
                    {{ 'Rp' . number_format($data['order']->biaya_pengiriman, 0, ',', '.') }}</li>
            @endif
        </ul>
    </div>
    <script>
        window.onload = function() {
            window.print();
            window.onafterprint = function() {
                window.history.back();
            }
        }
    </script>
</body>

</html>
