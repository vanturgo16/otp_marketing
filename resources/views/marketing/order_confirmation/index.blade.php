@extends('layouts.master')

@section('konten')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">List Order Confirmation</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Marketing</a></li>
                                <li class="breadcrumb-item active">Order Confirmation</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible alert-label-icon label-arrow fade show" role="alert">
                    <i class="mdi mdi-check-all label-icon"></i><strong>Success</strong> - {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('fail'))
                <div class="alert alert-danger alert-dismissible alert-label-icon label-arrow fade show" role="alert">
                    <i class="mdi mdi-block-helper label-icon"></i><strong>Failed</strong> - {{ session('fail') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('warning'))
                <div class="alert alert-warning alert-dismissible alert-label-icon label-arrow fade show" role="alert">
                    <i class="mdi mdi-alert-outline label-icon"></i><strong>Warning</strong> - {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('info'))
                <div class="alert alert-info alert-dismissible alert-label-icon label-arrow fade show" role="alert">
                    <i class="mdi mdi-alert-circle-outline label-icon"></i><strong>Info</strong> - {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <a href="{{ route('marketing.orderConfirmation.create') }}"
                                class="btn btn-primary waves-effect btn-label waves-light">
                                <i class="mdi mdi-plus-box label-icon"></i> Add New Data
                            </a>
                        </div>
                        <div class="card-body">
                            <table id="" class="table table-bordered dt-responsive w-100 datatable-buttons"
                                style="font-size: smaller">
                                <thead>
                                    <tr>
                                        <th class="align-middle text-center">No</th>
                                        <th class="align-middle text-center">OC<br>Number</th>
                                        <th class="align-middle text-center">Date</th>
                                        <th class="align-middle text-center">Customer</th>
                                        <th class="align-middle text-center">Salesman</th>
                                        <th class="align-middle text-center">Total<br>Price</th>
                                        <th class="align-middle text-center">PPN</th>
                                        <th class="align-middle text-center">Status</th>
                                        <th class="align-middle text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 0; ?>
                                    @foreach ($orderConfirmations as $data)
                                        <?php $no++; ?>
                                        <tr>
                                            <td class="align-middle text-center">{{ $no }}</td>
                                            <td class="align-middle text-center">{{ $data->oc_number }}</td>
                                            <td class="align-middle text-center"><b>{{ $data->date }}</b></td>
                                            <td class="align-middle">{{ $data->customer }}</td>
                                            <td class="align-middle">{{ $data->salesman }}</td>
                                            <td class="align-middle text-center">{{ number_format($data->total_price, 0, ',', '.') }}
                                            </td>
                                            <td class="align-middle text-center">{{ $data->ppn }}</td>
                                            <td class="align-middle text-center">{{ $data->status }}</td>
                                            <td class="align-middle text-center">
                                                <div class="btn-group" role="group">
                                                    <button id="btnGroupDrop{{ $data->id }}" type="button"
                                                        class="btn btn-sm btn-primary dropdown-toggle"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        Action <i class="mdi mdi-chevron-down"></i>
                                                    </button>
                                                    <ul class="dropdown-menu"
                                                        aria-labelledby="btnGroupDrop{{ $data->id }}">
                                                        <li>
                                                            <a class="dropdown-item drpdwn-{{ $data->status == "Request" ? "scs" : "wrn"; }}" href="#"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#info{{ $data->id }}"><span
                                                                    class="mdi mdi-arrow-left-top-bold"></span> | {{ $data->status == "Request" ? "Posted" : "Un Posted"; }}</a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item drpdwn-scn" href="#"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#update{{ $data->id }}"><span
                                                                    class="mdi mdi-printer"></span> | Print</a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item drpdwn" href="#"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#update{{ $data->id }}"><span
                                                                    class="mdi mdi-eye"></span> | View Data</a>
                                                        </li>
                                                    </ul>
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

        </div>
    </div>
@endsection
