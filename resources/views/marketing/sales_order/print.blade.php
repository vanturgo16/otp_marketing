<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sales ORder {{ $salesOrder->so_number }}</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/icon-otp.png') }}">
    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-8 d-flex align-items-center gap-10">
                <strong class="fs-4">PT OLEFINA TIFAPLAS POLIKEMINDO</strong>
            </div>
            {{-- <div class="col-4 d-flex justify-content-end">
                FM-SM-MKT-02, Rev. 0, 01 September 2021
            </div> --}}
        </div>

        <div class="row text-center">
            <div class="col-12">
                <strong class="fs-3">SALES ORDER</strong>
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
                        <td>{{ $salesOrder->qty }}</td>
                    </tr>
                    <tr>
                        <td>Warna</td>
                        <td>:</td>
                        <td>{{ $salesOrder->color }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Kirim</td>
                        <td>:</td>
                        <td>-</td>
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
                <p class="mb-5">Pemesan</p>
                <p><b>(.............)</b></p>
            </div>
        </div>

        <div class="row">
            <h6>This Document is automatically approved by system and authorized signature is not required</h6>
        </div>



    </div>
</body>

</html>
