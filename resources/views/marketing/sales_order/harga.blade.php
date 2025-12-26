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
                                            <td class="text-center">
                                                {{ $loop->iteration }}
                                            </td>
                                            <td class="text-center">
                                                {{ $item->so_number }}
                                            </td>
                                            <td class="text-center">
                                                {{ $item->petugas }}
                                            </td>
                                            <td class="text-center">
                                                {{ $item->harga_sblm }}
                                            </td>
                                            <td class="text-center">
                                                {{ $item->price }}
                                            </td>
                                            <td class="text-center">
                                                {{ $item->tgl_update_harga }}
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
@endsection
