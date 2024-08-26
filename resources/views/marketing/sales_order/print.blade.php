<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sales Order {{ $salesOrder->so_number }}</title>
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
    <div class="watermark {{ $salesOrder->status == 'Request' || $salesOrder->status == 'Un Posted' ? '' : 'd-none' }}">DRAFT</div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-8 d-flex align-items-center gap-10">
                <img src="{{ asset('assets/images/icon-otp.png') }}" width="100" height="100" alt="Logo OTP">
                <small style="padding-left: 10px">
                    <b>PT. OLEFINA TIFAPLAS POLIKEMINDO</b><br />
                    Jl. Raya Serang KM 16.8 Desa Telaga, Kec. Cikupa<br />
                    Tangerang-Banten 15710<br />
                    Tlp. +62 21 5960801/05, Fax. +62 21 5960776<br />
                </small>
            </div>
            <div class="col-4 d-flex justify-content-end">
                FM-SM-MKT-03, Rev. 0, 01 September 2021
            </div>
        </div>

        <div class="row text-center">
            <div class="col-12">
                <strong class="fs-5">SALES ORDER</strong>
                <hr>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <table>
                    <tr>
                        <td style="width: 150px;">No. SO</td>
                        <td>:</td>
                        <td>{{ $salesOrder->so_number }}</td>
                    </tr>
                    <tr>
                        <td>No. Ref</td>
                        <td>:</td>
                        <td>{{ $salesOrder->reference_number }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal</td>
                        <td>:</td>
                        <td>{{ $salesOrder->date }}</td>
                    </tr>
                    <tr>
                        <td>Type</td>
                        <td>:</td>
                        <td>{{ $salesOrder->so_category }}</td>
                    </tr>
                    <tr>
                        <td>Nama Barang</td>
                        <td>:</td>
                        <td>{{ $product->description }}</td>
                    </tr>
                    <tr>
                        <td>Qty Order</td>
                        <td>:</td>
                        <td>{{ $salesOrder->qty . ' ' . $product->unit_code }}</td>
                    </tr>
                    <tr>
                        <td>Perforasi</td>
                        <td>:</td>
                        <td>{{ isset($product->perforasi) ? $product->perforasi : '-' }}</td>
                    </tr>
                    <tr>
                        <td>Warna</td>
                        <td>:</td>
                        <td>{{ $salesOrder->color }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Kirim</td>
                        <td>:</td>
                        <td>{{ $salesOrder->due_date }}</td>
                    </tr>
                    <tr>
                        <td>Keterangan</td>
                        <td>:</td>
                        <td>{{ $salesOrder->remarks }}</td>
                    </tr>
                    <tr>
                        <td>Sales</td>
                        <td>:</td>
                        <td>{{ $salesOrder->masterSalesman->name }}</td>
                    </tr>
                    <tr>
                        <td>Customer</td>
                        <td>:</td>
                        <td>{{ $salesOrder->masterCustomer->name }}</td>
                    </tr>
                    <tr>
                        <td>Kode Barang</td>
                        <td>:</td>
                        <td>{{ $salesOrder->cust_product_code }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <hr>
        <div class="row">
            <div class="col-4 text-center">
                <p class="mb-5">Menyetujui,</p>
                <p><b>({{ $salesOrder->masterSalesman->name }})</b></p>
            </div>
            <div class="col-4 text-center">
                <p class="mb-5">Mengetahui,</p>
                <p><b>(BUDI TRIADI)</b></p>
            </div>
            <div class="col-4 text-center">
                <p class="mb-5">Dibuat</p>
                <p><b>(.............)</b></p>
            </div>
        </div>

        <div class="row">
            <h6>This Document is automatically approved by system and authorized signature is not required</h6>
        </div>



    </div>
</body>

</html>
