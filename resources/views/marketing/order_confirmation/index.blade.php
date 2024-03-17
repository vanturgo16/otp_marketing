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

            <div class="alert alert-success alert-dismissible alert-label-icon label-arrow fade show d-none" role="alert"
                id="alertSuccess">
                <i class="mdi mdi-check-all label-icon"></i><strong>Success</strong> - <span
                    class="alertMessage">{{ session('success') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>

            <div class="alert alert-danger alert-dismissible alert-label-icon label-arrow fade show d-none" role="alert"
                id="alertFail">
                <i class="mdi mdi-block-helper label-icon"></i><strong>Failed</strong> - <span
                    class="alertMessage">{{ session('fail') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
                            <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />

                            <table id="order_confirmation_table"
                                class="table table-hover table-bordered dt-responsive w-100 datatable-buttons"
                                style="font-size: small">
                                <thead>
                                    <tr>
                                        <th class="align-middle text-center">
                                            <input type="checkbox" id="checkAllRows">
                                        </th>
                                        <th class="align-middle text-center">#</th>
                                        <th class="align-middle text-center" data-name="po_number">OC<br>Number</th>
                                        <th class="align-middle text-center" data-name="date">Date</th>
                                        <th class="align-middle text-center" data-name="customer">Customer</th>
                                        <th class="align-middle text-center" data-name="salesman">Salesman</th>
                                        <th class="align-middle text-center" data-name="total_price">Total<br>Price</th>
                                        <th class="align-middle text-center" data-name="ppn">PPN</th>
                                        <th class="align-middle text-center" data-name="status">Status</th>
                                        <th class="align-middle text-center">Action</th>
                                    </tr>
                                </thead>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Static Backdrop Modal -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Confirm to Posted</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to posted this data?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary waves-effect btn-label waves-light"
                        onclick="bulkPosted()"><i class="mdi mdi-arrow-right-top-bold label-icon"></i>Posted</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Static Backdrop Modal PDF -->
    <div class="modal fade" id="modalPDF" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Preview or Print</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center fs-1">
                    <a href="#" class="btn btn-primary waves-effect waves-light w-sm preview" target="_blank"
                        rel="noopener noreferrer">
                        <i class="mdi mdi-search-web d-block fs-1"></i> Preview
                    </a>
                    <a href="#" class="btn btn-success waves-effect waves-light w-sm print" target="_blank"
                        rel="noopener noreferrer">
                        <i class="mdi mdi-printer d-block fs-1"></i> Print
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var i = 1;
            let dataTable = $('#order_confirmation_table').DataTable({
                dom: '<"top d-flex"<"position-absolute top-0 end-0 d-flex"fl>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>><"clear:both">',
                initComplete: function(settings, json) {
                    // Setelah DataTable selesai diinisialisasi
                    // Tambahkan elemen kustom ke dalam DOM
                    $('.top').prepend(
                        `<div class='pull-left col-sm-12 col-md-5'><div class="btn-group mb-4"><button type="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-checkbox-multiple-marked-outline"></i> Bulk Actions</button><ul class="dropdown-menu"><li><button class="dropdown-item" data-status="Request" onclick="showModal(this, 'Delete');"><i class="mdi mdi-trash-can"></i> Delete</button></li><li><button class="dropdown-item" data-status="Request" onclick="showModal(this);"><i class="mdi mdi-check-bold"></i> Posted</button></li></ul></div></div>`
                    );
                },
                processing: true,
                serverSide: true,
                language: {
                    lengthMenu: "_MENU_",
                    search: "",
                    searchPlaceholder: "Search",
                },
                pageLength: 5,
                lengthMenu: [
                    [5, 10, 20, 25, 50, 100],
                    [5, 10, 20, 25, 50, 100]
                ],
                columnDefs: [{
                    'orderable': false,
                    'targets': 0
                }], // hide sort icon on header of first column
                aaSorting: [
                    [2, 'desc']
                ], // start to sort data in second column 
                ajax: {
                    url: baseRoute + '/marketing/orderConfirmation/',
                    data: function(d) {
                        d.search = $('input[type="search"]').val(); // Kirim nilai pencarian
                    }
                },
                columns: [{
                        data: 'bulk-action',
                        name: 'bulk-action',
                        className: 'align-middle text-center',
                        orderable: false,
                        searchable: false
                    }, {
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        },
                        className: 'align-middle text-center',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'oc_number',
                        name: 'oc_number',
                        className: 'align-middle text-center',
                        orderable: true,
                    },
                    {
                        data: 'date',
                        name: 'date',
                        className: 'align-middle text-center',
                        orderable: true,
                    },
                    {
                        data: 'customer',
                        name: 'customer',
                        className: 'align-middle',
                        orderable: true,
                    },
                    {
                        data: 'salesman',
                        name: 'salesman',
                        className: 'align-middle',
                        orderable: true,
                    },
                    {
                        data: 'total_price',
                        name: 'total_price',
                        className: 'align-middle text-end',
                        render: function(data, type, row) {
                            return Number(data).toLocaleString('id-ID');
                        },
                        orderable: true,
                    },
                    {
                        data: 'ppn',
                        name: 'ppn',
                        className: 'align-middle text-center',
                        orderable: true,
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: 'align-middle text-center',
                        orderable: true,
                    },
                    {
                        data: 'action',
                        name: 'action',
                        className: 'align-middle text-center',
                        orderable: false,
                        searchable: false
                    },
                ],
                createdRow: function(row, data, dataIndex) {
                    // Tambahkan class "table-success" ke tr jika statusnya "Posted"
                    if (data.statusLabel === 'Posted') {
                        $(row).addClass('table-success');
                    }
                },
                bAutoWidth: false,
                columnDefs: [{
                    width: "10%",
                    targets: [3]
                }]
            });
        });
    </script>
@endpush
