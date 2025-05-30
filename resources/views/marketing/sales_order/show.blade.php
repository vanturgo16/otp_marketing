@extends('layouts.master')

@section('konten')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">View Sales Order</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Marketing</a></li>
                                <li class="breadcrumb-item active">View Sales Order</li>
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

            <div class="row pb-3">
                <div class="col-12">
                    <a href="{{ route('marketing.salesOrder.index') }}"
                        class="btn btn-primary waves-effect btn-label waves-light">
                        <i class="mdi mdi-arrow-left label-icon"></i> Back To List Data Sales Order
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <form action="{{ route('marketing.salesOrder.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-header">
                                <i class="mdi mdi-file-multiple-outline label-icon"></i> View Sales Order
                            </div>

                            <div class="card-body">
                                <div class="mt-4 mt-lg-0">
                                    <dl class="row">
                                        <dt class="col-sm-3 mb-2"><label>Order Confirmation</label></dt>
                                        <dd class="col-sm-9 mb-2">{{ $sales_order->id_order_confirmation }}
                                        </dd>

                                        <dt class="col-sm-3 mb-2"><label>SO Number</label></dt>
                                        <dd class="col-sm-9 mb-2" id="so_number">{{ $sales_order->so_number }}</dd>

                                        <dt class="col-sm-3 mb-2"><label>SO Category</label></dt>
                                        <dd class="col-sm-9 mb-2" id="so_number">{{ $sales_order->so_type }}</dd>

                                        <dt class="col-sm-3 mb-2"><label>Date</label></dt>
                                        <dd class="col-sm-9 mb-2">
                                            {{ \Carbon\Carbon::parse($sales_order->date)->isoFormat('dddd, D MMMM YYYY', 'ID') }}
                                        </dd>

                                        <dt class="col-sm-3 mb-2"><label>Customer</label></dt>
                                        <dd class="col-sm-9 mb-2">{{ $sales_order->masterCustomer->customer_code }} -
                                            {{ $sales_order->masterCustomer->name }}</dd>

                                        <dt class="col-sm-3 mb-2"><label>Customer Address</label></dt>
                                        <dd class="col-sm-9 mb-2">{{ $customer_addresses->address }}</dd>

                                        <dt class="col-sm-3 mb-2"><label>Salesman</label></dt>
                                        <dd class="col-sm-9 mb-2">{{ $sales_order->masterSalesman->name }}</dd>

                                        <dt class="col-sm-3 mb-2"><label>Reference Number (PO)</label></dt>
                                        <dd class="col-sm-9 mb-2">{{ $sales_order->reference_number }}</dd>

                                        <dt class="col-sm-3 mb-2"><label>Due Date</label></dt>
                                        <dd class="col-sm-9 mb-2">
                                            {{ \Carbon\Carbon::parse($sales_order->due_date)->isoFormat('dddd, D MMMM YYYY', 'ID') }}
                                        </dd>

                                        <dt class="col-sm-3 mb-2"><label>Color</label></dt>
                                        <dd class="col-sm-9 mb-2">{{ $sales_order->color }}</dd>

                                        <dt class="col-sm-3 mb-2"><label>Non Invoiceable</label></dt>
                                        <dd class="col-sm-9 mb-2">{{ $sales_order->non_invoiceable }}</dd>

                                        <dt class="col-sm-3 mb-2"><label>Remark</label></dt>
                                        <dd class="col-sm-9 mb-2">{{ $sales_order->remark }}</dd>

                                        <dt class="col-sm-3 mb-2"><label>Ppn</label></dt>
                                        <dd class="col-sm-9 mb-2">{{ $sales_order->ppn }}</dd>

                                        <dt class="col-sm-3 mb-2"><label>Term Payment</label></dt>
                                        <dd class="col-sm-9 mb-2">{{ $sales_order->masterTermPAyment->term_payment }}</dd>
                                    </dl>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="table-responsive table-bordered">
                                            <table class="table table-striped table-hover" id="productTable">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th>Type <br>Product</th>
                                                        <th>Product</th>
                                                        <th>Cust Product Code</th>
                                                        <th>Unit</th>
                                                        <th>Qty</th>
                                                        <th>Price</th>
                                                        <th>Total Price</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="text-center">{{ $sales_order->type_product }}</td>
                                                        @php
                                                            $perforasi = $product->perforasi === '' ? '' : ($product->perforasi === null ? ' | Perforasi: -' : ' | Perforasi: ' . $product->perforasi);
                                                            $group_sub_code = !isset($product->group_sub_code) ? '' : ($product->group_sub_code === null ? ' | Group Sub: -' : ' | Group Sub: ' . $product->group_sub_code);
                                                        @endphp
                                                        <td>{{ $product->description . $perforasi . $group_sub_code }}</td>
                                                        <td>{{ $sales_order->cust_product_code }}</td>
                                                        <td class="text-center">{{ $sales_order->masterUnit->unit_code }}
                                                        </td>
                                                        <td class="text-center">{{ $sales_order->qty }}</td>
                                                        <td class="text-end">{{ $sales_order->price }}</td>
                                                        <td class="text-end">{{ $sales_order->total_price }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <!-- end col -->
                                </div>
                                <!-- end row -->
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
