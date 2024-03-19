@extends('layouts.master')

@section('konten')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">List Sales Order</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Marketing</a></li>
                                <li class="breadcrumb-item active">Sales Order</li>
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
                            <a href="{{ route('marketing.salesOrder.create') }}"
                                class="btn btn-primary waves-effect btn-label waves-light">
                                <i class="mdi mdi-plus-box label-icon"></i> Add New Data
                            </a>
                            <a href="#" class="btn btn-secondary waves-effect btn-label waves-light" data-search = ""
                                id="add_data" onclick="filterSearch(this)">
                                <i class="mdi mdi-file-multiple label-icon"></i> All Data
                            </a>
                            <a href="#" class="btn btn-success waves-effect btn-label waves-light"
                                data-search = "Stock" id="so_stock" onclick="filterSearch(this)">
                                <i class="mdi mdi-file-multiple label-icon"></i> SO Stock
                            </a>
                            <a href="#" class="btn btn-info waves-effect btn-label waves-light"
                                data-search = "Reguler" id="so_customer" onclick="filterSearch(this)">
                                <i class="mdi mdi-file-multiple label-icon"></i> SO Customer
                            </a>
                        </div>
                        <div class="card-body">
                            <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />

                            <div class="table-responsive">
                                <table id="so_customer_table" class="table table-hover table-bordered"
                                    style="font-size: small; min-width: 90rem;">
                                    <thead>
                                        <tr>
                                            <th class="align-middle text-center">
                                                <input type="checkbox" id="checkAllRows">
                                            </th>
                                            <th class="align-middle text-center">#</th>
                                            <th class="align-middle text-center" data-name="order_confirmation">
                                                Order<br>Confirmation</th>
                                            <th class="align-middle text-center" data-name="so_number">SO<br>Number</th>
                                            <th class="align-middle text-center" data-name="date">Date</th>
                                            <th class="align-middle text-center" data-name="so_type">SO Type</th>
                                            <th class="align-middle text-center" data-name="customer">Customer</th>
                                            <th class="align-middle text-center" data-name="salesman">Salesman</th>
                                            <th class="align-middle text-center" data-name="reference_number">
                                                Reference<br>Number</th>
                                            <th class="align-middle text-center">Product</th>
                                            <th class="align-middle text-center">Progress</th>
                                            <th class="align-middle text-center" data-name="status">Status</th>
                                            <th class="align-middle text-center">WO List</th>
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
            let dataTable = $('#so_customer_table').DataTable({
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
                // scrollX: true,
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
                aaSorting: [
                    [1, 'desc']
                ], // start to sort data in second column 
                ajax: {
                    url: baseRoute + '/marketing/salesOrder/',
                    data: function(d) {
                        d.search = $('input[type="search"]').val(); // Kirim nilai pencarian
                    }
                },
                columns: [{
                        data: 'bulk-action',
                        name: 'bulk-action',
                        // className: 'align-middle text-center',
                        orderable: false,
                        searchable: false
                    }, {
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        },
                        // className: 'align-middle text-center',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'id_order_confirmations',
                        name: 'id_order_confirmations',
                        // className: 'align-middle text-center',
                        orderable: true,
                    },
                    {
                        data: 'so_number',
                        name: 'so_number',
                        // className: 'align-middle text-center',
                        orderable: true,
                    },
                    {
                        data: 'date',
                        name: 'date',
                        // className: 'align-middle text-center',
                        orderable: true,
                    },
                    {
                        data: 'so_type',
                        name: 'so_type',
                        // className: 'align-middle text-center',
                        orderable: true,
                    },
                    {
                        data: 'customer',
                        name: 'customer',
                        // className: 'align-middle',
                        orderable: true,
                    },
                    {
                        data: 'salesman',
                        name: 'salesman',
                        // className: 'align-middle',
                        orderable: true,
                    },
                    {
                        data: 'reference_number',
                        name: 'reference_number',
                        // className: 'align-middle',
                        orderable: true,
                    },
                    {
                        data: 'description',
                        name: 'description',
                        orderable: true,
                        searchable: false
                    },
                    {
                        data: 'progress',
                        name: 'progress',
                        orderable: true,
                        searchable: false
                    },
                    {
                        data: 'status',
                        name: 'status',
                        // className: 'align-middle text-center',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'wo_list',
                        name: 'wo_list',
                        // className: 'align-middle text-center',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        // className: 'align-middle text-center',
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
                    }, {
                        width: '100px', // Menetapkan min-width ke 150px
                        targets: [6, 7], // Menggunakan class 'progress' pada kolom
                    }, {
                        width: '120px', // Menetapkan min-width ke 150px
                        targets: [9], // Menggunakan class 'progress' pada kolom
                    }, {
                        width: '150px', // Menetapkan min-width ke 150px
                        targets: [10], // Menggunakan class 'progress' pada kolom
                    },
                    {
                        width: '60px', // Menetapkan min-width ke 150px
                        targets: [4], // Menggunakan class 'progress' pada kolom
                    }, {
                        orderable: false,
                        targets: [0]
                    }
                ],
            });
        });
    </script>
@endpush
