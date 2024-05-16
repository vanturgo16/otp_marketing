<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Order Confirmation {{ $orderConfirmation->oc_number }}</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/icon-otp.png') }}">
    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-8 d-flex align-items-center gap-10">
                <img src="http://eks.olefinatifaplas.my.id/img/otp-icon.jpg" width="100" height="100">
                <small style="padding-left: 10px">
                    Jl. Raya Serang KM 16.8 Desa Telaga, Kec. Cikupa<br />
                    Tangerang-Banten 15710<br />
                    Tlp. +62 21 5960801/05, Fax. +62 21 5960776<br />
                </small>
            </div>
            {{-- <div class="col-4 d-flex justify-content-end">
                FM-SM-MKT-02, Rev. 0, 01 September 2021
            </div> --}}
        </div>

        <div class="row text-center">
            <h1>ORDER CONFIRMATION</h1>
            <p>Nomor: {{ $orderConfirmation->oc_number }}</p>
        </div>

        <div class="row d-flex justify-content-between pb-3">
            <div class="col-8">Tanggal : 2024-01-23</div>
            <div class="col-4" style="border: 1px solid black">
                <p class="mb-1">Kepada Yth, </p>
                <p class="mb-1">{{ $orderConfirmation->masterSalesman->name }}</p>
            </div>
        </div>

        <div class="row">
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-10">
                    <thead class="table-light">
                        <tr>
                            <td>No</td>
                            <td>Nama Barang</td>
                            <td>Ukuran</td>
                            <td>Jumlah</td>
                            <td>Harga</td>
                            <td>Total Harga</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orderConfirmation->orderConfirmationDetails as $detail)
                            @php
                                $productName = getProductName($detail->type_product, $detail->id_master_product);
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $productName }}</td>
                                <td>{{ $detail->masterUnit->unit_code }}</td>
                                <td class="text-end">{{ $detail->qty }}</td>
                                <td class="text-end">{{ number_format($detail->price, 0, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row">
            <ul style="list-style-type: '- ';">
                <li>Penyerahan (Toleransi +/- 10%)</li>
                <li>Syarat Pembayaran :</li>
                Batas waktu pengaduan tentang kondisi barang yang disebabkan cacat dari pabrik kami (masalah kualitas),
                harap diinformasikan kepada
                kami selambat-lambatnya 30 hari setelah tanggal penerimaan barang dengan ketentuan cantumkan nomor
                label, box dan jumlahnya.
            </ul>
        </div>
        <hr>
        <div class="row">
            <div class="col-4 text-center">
                <p class="mb-5">Penerima Order,</p>
                <p>(.............)</p>
            </div>
            <div class="col-4 text-center">
                <p class="mb-5">Menyetujui,</p>
                <p>(.............)</p>
            </div>
            <div class="col-4 text-center">
                <p class="mb-5">Pemesan</p>
                <p>(.............)</p>
            </div>
        </div>

        <div class="row">
            <h6>*Pembayaran harap ditransfer ke :</h6>
        </div>



    </div>
</body>

</html>
