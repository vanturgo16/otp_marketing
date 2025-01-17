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
                            <button type="button" class="btn btn-light waves-effect btn-label waves-light"
                                id="modalExportData">
                                <i class="mdi mdi-export label-icon"></i> Export Data
                            </button>
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

    <!-- Static Backdrop Modal Export Data -->
    <div class="modal fade" id="exportData" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        role="dialog" aria-labelledby="exportDataLabel" aria-hidden="true">
        <form action="{{ route('marketing.orderConfirmation.exportData') }}" method="POST">
            @csrf
            @method('GET')
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exportDataLabel">Export Data Order Confirmation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-2">
                            <label for="start_date" class="col-sm-3 col-form-label">Start Date</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" name="start_date" id="start_date"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label for="end_date" class="col-sm-3 col-form-label">End Date</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" name="end_date" id="end_date"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label for="status" class="col-sm-3 col-form-label">Status</label>
                            <div class="col-sm-9">
                                <select class="form-control data-select2" name="status" id="statusSelectOption"
                                    style="width: 100%" required>
                                    <option value="">** Please select a Status</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary waves-effect btn-label waves-light">
                            <i class="mdi mdi-export label-icon"></i> Export
                        </button>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        // Fungsi untuk menyimpan posisi halaman saat ini
        function saveCurrentPage(menuKey) {
            let pageInfo = $('#order_confirmation_table').DataTable().page.info();
            sessionStorage.setItem(`currentPage_${menuKey}`, pageInfo.page);
        }

        // Fungsi untuk mengambil posisi halaman yang tersimpan
        function getSavedPage(menuKey) {
            return sessionStorage.getItem(`currentPage_${menuKey}`);
        }

        // Fungsi untuk menghapus posisi halaman yang tersimpan
        function clearAllSavedPages() {
            sessionStorage.removeItem('currentPage_po_customer');
            sessionStorage.removeItem('currentPage_order_confirmation');
            sessionStorage.removeItem('currentPage_sales_order');
        }

        $(document).ready(function() {
            // Gantilah 'menuKey' sesuai halaman yang sedang dibuka ('po_customer', 'order_confirmation', atau 'sales_order')
            const menuKey = 'order_confirmation';
            const savedPage = getSavedPage(menuKey) || 0;

            var i = 1;
            let dataTable = $('#order_confirmation_table').DataTable({
                dom: '<"top d-flex"<"position-absolute top-0 end-0 d-flex"fl>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>><"clear:both">',
                displayStart: savedPage * 20, // Atur posisi halaman berdasarkan nilai tersimpan
                initComplete: function(settings, json) {
                    // Setelah DataTable selesai diinisialisasi
                    // Tambahkan elemen kustom ke dalam DOM
                    $('.top').prepend(
                        `<div class='pull-left col-sm-12 col-md-5'><div class="btn-group mb-4"><button type="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-checkbox-multiple-marked-outline"></i> Bulk Actions</button><ul class="dropdown-menu"><li><button class="dropdown-item" data-status="Request" onclick="showModal(this, 'Delete');"><i class="mdi mdi-trash-can"></i> Delete</button></li><li><button class="dropdown-item" data-status="Request" onclick="showModal(this);"><i class="mdi mdi-check-bold"></i> Posted</button></li></ul></div></div>`
                    );
                    if (savedPage > 0) {
                        dataTable.page(parseInt(savedPage)).draw('page');
                    }
                },
                processing: true,
                serverSide: true,
                language: {
                    lengthMenu: "_MENU_",
                    search: "",
                    searchPlaceholder: "Search",
                },
                pageLength: 20,
                lengthMenu: [
                    [5, 10, 20, 25, 50, 100, 200],
                    [5, 10, 20, 25, 50, 100, 200]
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
                    } else if (data.statusLabel === 'Closed') {
                        $(row).addClass('table-info');
                    } else if (data.statusLabel === 'Finish') {
                        $(row).addClass('table-primary');
                    }
                },
                bAutoWidth: false,
                columnDefs: [{
                    width: "10%",
                    targets: [3]
                }]
            });

            // Simpan nomor halaman saat ini ketika pengguna berpindah halaman
            $('#order_confirmation_table').on('draw.dt', function() {
                saveCurrentPage(menuKey);
            });
        });

        // Tambahkan event listener pada semua link di dalam #sidebar-menu
        document.querySelectorAll('#sidebar-menu a').forEach(link => {
            link.addEventListener('click', function() {
                clearAllSavedPages();
            });
        });

        // Seleksi tag <a> pertama di dalam .card-header dan tambahkan event click
        $('.card-header a:first').on('click', function() {
            clearAllSavedPages();
        });

        setTimeout(function() {
            const alertElement = $('.alert');
            alertElement.addClass('d-none');
        }, 3000); // Menyembunyikan setelah 3 detik (3000 milidetik)
    </script>
@endpush
