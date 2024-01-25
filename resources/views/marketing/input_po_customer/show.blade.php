@extends('layouts.master')

@section('konten')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">View PO Customer</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Marketing</a></li>
                                <li class="breadcrumb-item active">View PO Customer</li>
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
                    <a href="{{ route('marketing.inputPOCust.index') }}"
                        class="btn btn-primary waves-effect btn-label waves-light">
                        <i class="mdi mdi-arrow-left label-icon"></i> Back To List Data PO Customer
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <form action="{{ route('marketing.inputPOCust.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-header">
                                <i class="mdi mdi-file-multiple-outline label-icon"></i> View PO Customer
                            </div>

                            <div class="card-body">
                                <div class="mt-4 mt-lg-0">
                                    <dl class="row">
                                        <dt class="col-sm-3 mb-2"><label>PO Number</label></dt>
                                        <dd class="col-sm-9 mb-2" id="po_number">{{ $inputPOCustomer->po_number }}</dd>

                                        <dt class="col-sm-3 mb-2"><label>Date</label></dt>
                                        <dd class="col-sm-9 mb-2">
                                            {{ \Carbon\Carbon::parse($inputPOCustomer->date)->isoFormat('dddd, D MMMM YYYY', 'ID') }}
                                        </dd>

                                        <dt class="col-sm-3 mb-2"><label>Customer</label></dt>
                                        <dd class="col-sm-9 mb-2">{{ $inputPOCustomer->masterCustomer->customer_code }} -
                                            {{ $inputPOCustomer->masterCustomer->name }}</dd>

                                        <dt class="col-sm-3 mb-2"><label>Salesman</label></dt>
                                        <dd class="col-sm-9 mb-2">{{ $inputPOCustomer->masterSalesman->name }}</dd>

                                        <dt class="col-sm-3 mb-2"><label>Term Payment</label></dt>
                                        <dd class="col-sm-9 mb-2">{{ $inputPOCustomer->masterTermPayment->term_payment }}
                                        </dd>

                                        <dt class="col-sm-3 mb-2"><label>Currency</label></dt>
                                        <dd class="col-sm-9 mb-2">{{ $inputPOCustomer->masterCurrencies->currency }}</dd>

                                        <dt class="col-sm-3 mb-2"><label>Ppn</label></dt>
                                        <dd class="col-sm-9 mb-2">{{ $inputPOCustomer->ppn }}</dd>

                                        <dt class="col-sm-3 mb-2"><label>Remark</label></dt>
                                        <dd class="col-sm-9 mb-2">{{ $inputPOCustomer->remark }}</dd>

                                        <dt class="col-sm-3 mb-2"><label>Status</label></dt>
                                        <dd class="col-sm-9 mb-2">{{ $inputPOCustomer->status }}</dd>
                                    </dl>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="table-responsive table-bordered">
                                            <table class="table table-striped table-hover" id="productTable">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th>#</th>
                                                        <th>Type <br>Product</th>
                                                        <th>Product</th>
                                                        <th>Cust Product Code</th>
                                                        <th>Unit</th>
                                                        <th>Qty</th>
                                                        <th>Price</th>
                                                        <th>Subtotal</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th colspan="8">
                                                            <div class="d-flex align-items-center justify-content-between">
                                                                <a href="{{ route('marketing.inputPOCust.index') }}"
                                                                    class="btn btn-light w-md"><i
                                                                        class="fas fa-arrow-left"></i>&nbsp;
                                                                    Back</a>
                                                                <span class="fs-3">Total Price : <span
                                                                        id="totalAmount"></span></span>
                                                            </div>
                                                        </th>
                                                    </tr>
                                                </tfoot>
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
