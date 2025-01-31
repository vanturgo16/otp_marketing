<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Konfirmasi Order {{ $orderConfirmation->oc_number }}</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/icon-otp.png') }}">
    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />

    <style>
        /* Watermark CSS */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1;
            font-size: 100px;
            color: rgba(0, 0, 0, 0.1);
            pointer-events: none;
            user-select: none;
        }

        /* Ensure content is above the watermark */
        .content {
            position: relative;
            z-index: 2;
        }
    </style>
</head>

<body>
    <div class="watermark {{ $orderConfirmation->status == 'Request' || $orderConfirmation->status == 'Un Posted' ? '' : 'd-none' }}">DRAFT</div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-8 d-flex align-items-center gap-10">
                <img src="{{ asset('assets/images/icon-otp.png') }}" width="100" height="100" alt="Logo OTP">
                <small style="padding-left: 10px">
                    <b>PT. OLEFINA TIFAPLAS POLIKEMINDO</b><br />
                    Jl. Raya Serang KM 16.8 Desa Telaga, Kec. Cikupa<br />
                    Tangerang-Banten 15710<br />
                    Tlp. +62 21 595663567, Fax. 0<br />
                </small>
            </div>
            <div class="col-4 d-flex justify-content-end">
                FM-SM-MKT-02, Rev. 0, 01 September 2021
            </div>
        </div>

        <div class="row d-flex justify-content-between pb-3">
            <div class="col-3 align-self-end"><b>Tanggal : {{ $orderConfirmation->date }}</b></div>
            <div class="col-5 text-center align-self-center">
                <h5>KONFIRMASI ORDER</h5>
                <p>Nomor: {{ $orderConfirmation->oc_number }}</p>
            </div>
            <div class="col-4" style="border: 1px solid black">
                <p class="mb-0">Kepada Yth, </p>
                <p class="mb-0"><b>{{ $orderConfirmation->masterCustomer->name }}</b></p>
                <p class="mb-0">{{ $orderConfirmation->masterCustomer->customerAddress->address }}
                    {{ $orderConfirmation->masterCustomer->customerAddress->postal_code }}</p>
                <p class="mb-0">Telp : {{ $orderConfirmation->masterCustomer->customerAddress->mobile_phone }}</p>
            </div>
        </div>

        <div class="row">
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-10">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">No</th>
                            <th>Nama Barang</th>
                            <th class="text-center">Jumlah</th>
                            <th class="text-center">Satuan</th>
                            <th></th>
                            <th class="text-center">Harga</th>
                            <th></th>
                            <th class="text-center">Total Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalQty = 0;
                            $totalHarga = 0;
                        @endphp
                        @foreach ($orderConfirmation->orderConfirmationDetails as $detail)
                            @php
                                $productName = getProductName($detail->type_product, $detail->id_master_product);
                                $totalQty += $detail->qty;
                                $totalHarga += $detail->subtotal;
                            @endphp
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $productName }}</td>
                                <td class="text-center">{{ $detail->qty }}</td>
                                <td class="text-center">{{ $detail->masterUnit->unit_code }}</td>
                                <td class="text-center">
                                    {{ $orderConfirmation->masterCustomer->currency->currency_code }}</td>
                                <td class="d-flex justify-content-between">
                                    <span>Rp</span>
                                    <span>{{ number_format($detail->price, 0, ',', '.') }}</span>
                                </td>
                                <td class="text-center">
                                    {{ $orderConfirmation->masterCustomer->currency->currency_code }}</td>
                                <td class="d-flex justify-content-between">
                                    <span>Rp</span>
                                    <span>{{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <th class="text-center" colspan="2">JUMLAH</th>
                        <th class="text-center">{{ $totalQty }}</th>
                        <th class="text-center"></th>
                        <th></th>
                        <th class="text-center"></th>
                        <th class="text-center"> {{ $orderConfirmation->masterCustomer->currency->currency_code }}</th>
                        <th class="d-flex justify-content-between">
                            <span>Rp</span>
                            <span>{{ number_format($totalHarga, 0, ',', '.') }}</span>
                        </th>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="row">
            <ul style="list-style-type: '- ';">
                <li>Penyerahan (Toleransi +/- 10%)</li>
                <li>PPN : {{ $orderConfirmation->ppn }}</li>
                <li>Syarat Pembayaran : {{ $orderConfirmation->masterCustomer->termPayment->term_payment }}</li>
                <li>Keterangan</li>
                Batas waktu pengaduan tentang kondisi barang yang disebabkan cacat dari pabrik kami (masalah
                kualitas), harap diinformasikan kepada kami selambat-lambatnya 30 hari setelah tanggal penerimaan
                barang dengan ketentuan cantumkan nomor label, box dan jumlahnya.
            </ul>
        </div>
        <hr>
        <div class="row">
            <div class="col-4 text-center">
                <p class="mb-5">Penerima Order,</p>
                <p><b>{{ $orderConfirmation->masterSalesman->name }}</b></p>
            </div>
            <div class="col-4 text-center">
                <p class="mb-5">Menyetujui,</p>
                <p><b>BUDI TRIADI</b></p>
            </div>
            <div class="col-4 text-center">
                <p class="mb-5">Pemesan</p>
                <p><b>.............</b></p>
            </div>
        </div>
    </div>
</body>

</html>
