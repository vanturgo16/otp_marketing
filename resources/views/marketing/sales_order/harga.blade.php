@extends('layouts.master')

@section('konten')
<div class="page-content">
    <div class="container-fluid">

        {{-- Page Title --}}
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">
                        Data Perubahan Harga Sales Order
                    </h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">Marketing</li>
                            <li class="breadcrumb-item active">
                                Data Perubahan Harga Sales Order
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <div class="table-responsive">
                            <table id="datatable"
                                   class="table table-bordered table-striped dt-responsive nowrap w-100">
                                
                                <thead class="text-center">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">SO Number</th>
                                        <th class="text-center">Petugas</th>
                                        <th class="text-center">Harga Sebelum</th>
                                        <th class="text-center">Harga Sesudah</th>
                                        <th class="text-center">Tanggal Update</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse ($harga as $item)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td class="text-center">{{ $item->so_number }}</td>
                                            <td class="text-center">{{ $item->petugas }}</td>
                                            <td class="text-center">
                                                {{ $item->harga_sblm }}
                                            </td>
                                            <td class="text-center">
                                                {{ $item->price }}

                                            </td>
                                            <td class="text-center">
                                                {{ \Carbon\Carbon::parse($item->tgl_update_harga)->format('d-m-Y H:i') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">
                                                Data tidak tersedia
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>

                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ===================== DATATABLES CSS ===================== --}}
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">

{{-- ===================== DATATABLES JS ====================== --}}
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

{{-- ===================== INIT DATATABLE ===================== --}}
<script>
    $(document).ready(function () {
        $('#datatable').DataTable({
            responsive: true,
            autoWidth: false,
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            order: [[5, 'desc']],
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Data tidak tersedia",
                zeroRecords: "Data tidak ditemukan",
                paginate: {
                    first: "Awal",
                    last: "Akhir",
                    next: "›",
                    previous: "‹"
                }
            }
        });
    });
</script>

@endsection
