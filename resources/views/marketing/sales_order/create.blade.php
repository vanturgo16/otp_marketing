@extends('layouts.master')

@section('konten')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Add Sales Order</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Marketing</a></li>
                                <li class="breadcrumb-item active">Add Sales Order</li>
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
                        <form action="{{ route('marketing.salesOrder.store') }}" method="POST" id="formSalesOrder">
                            @csrf
                            <div class="card-header">
                                <i class="mdi mdi-file-multiple-outline label-icon"></i> Add Sales ORder
                            </div>

                            <div class="card-body">
                                <div class="mt-4 mt-lg-0">
                                    <div class="row mb-4 field-wrapper">
                                        <label for="orderSelect" class="col-sm-3 col-form-label">Order Confirmation</label>
                                        <div class="col-sm-9">
                                            <select class="form-control data-select2" name="id_order_confirmations"
                                                id="orderSelect" style="width: 100%">
                                                <option value="">** Please select a Order Confirmation</option>
                                                @foreach ($orderPO as $data)
                                                    <option value="{{ $data->order }}">{{ $data->order }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-4 field-wrapper required-field">
                                        <label for="soTypeSelect" class="col-sm-3 col-form-label">SO Type</label>
                                        <div class="col-sm-9">
                                            <select class="form-control data-select2" name="so_type" id="soTypeSelect"
                                                style="width: 100%" required>
                                                <option value="">** Please select a SO Type</option>
                                                <option value="Reguler">Reguler</option>
                                                <option value="Sample">Sample</option>
                                                <option value="Raw Material">Raw Material</option>
                                                <option value="Machine">Machine</option>
                                                <option value="Stock">Stock</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-4 field-wrapper required-field">
                                        <label for="so_number" class="col-sm-3 col-form-label">SO Number</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="so_number" id="so_number"
                                                value="" required readonly>
                                        </div>
                                    </div>
                                    <div class="row mb-4 field-wrapper required-field">
                                        <label for="soCategorySelect" class="col-sm-3 col-form-label">SO Category</label>
                                        <div class="col-sm-9">
                                            <select class="form-control data-select2" name="so_category"
                                                id="soCategorySelect" style="width: 100%" required>
                                                <option value="">** Please select a SO Category</option>
                                                <option value="Stock">Stock</option>
                                                <option value="S/W">S/W</option>
                                                <option value="CF">CF</option>
                                                <option value="Bag">Bag</option>
                                                <option value="Box">Box</option>
                                                <option value="Return">Return</option>
                                                <option value="Selongsong">Selongsong</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-4 field-wrapper required-field">
                                        <label for="date" class="col-sm-3 col-form-label">Date</label>
                                        <div class="col-sm-9">
                                            <input type="date" class="form-control" name="date" id="date"
                                                value="{{ date('Y-m-d') }}" required>
                                        </div>
                                    </div>
                                    <div class="row mb-4 customerSection field-wrapper required-field">
                                        <label for="customerSelect" class="col-sm-3 col-form-label">Customer</label>
                                        <div class="col-sm-9">
                                            <select class="form-control data-select2" name="id_master_customers"
                                                id="customerSelect" style="width: 100%" required>
                                                <option value="">** Please select a Customers</option>
                                                @foreach ($customers as $customer)
                                                    <option value="{{ $customer->id }}">{{ $customer->customer_code }} -
                                                        {{ $customer->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-4 customerAddressSection field-wrapper required-field">
                                        <label for="customerAddressSelect" class="col-sm-3 col-form-label">Customer
                                            Address</label>
                                        <div class="col-sm-9">
                                            <select class="form-control data-select2" name="id_master_customer_addresses"
                                                id="customerAddressSelect" style="width: 100%" required>
                                                <option value="">** Please select a Customers Address</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-4 salesmanSection field-wrapper required-field">
                                        <label for="salesmanSelect" class="col-sm-3 col-form-label">Salesman</label>
                                        <div class="col-sm-9">
                                            <select class="form-control data-select2" name="id_master_salesmen"
                                                id="salesmanSelect" style="width: 100%" required>
                                                <option value="">** Please select a Salesman</option>
                                                @foreach ($salesmans as $salesman)
                                                    <option value="{{ $salesman->id }}">{{ $salesman->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-4 field-wrapper">
                                        <label for="reference_number" class="col-sm-3 col-form-label">Reference Number
                                            (PO)</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="reference_number"
                                                id="reference_number" value="">
                                        </div>
                                    </div>
                                    <div class="row mb-4 field-wrapper">
                                        <label for="due_date" class="col-sm-3 col-form-label">Due Date</label>
                                        <div class="col-sm-9">
                                            <input type="date" class="form-control" name="due_date" id="due_date"
                                                value="{{ now()->addDays(14)->format('Y-m-d') }}">
                                        </div>
                                    </div>
                                    <div class="row mb-4 field-wrapper required-field">
                                        <label for="colorSelect" class="col-sm-3 col-form-label">Color</label>
                                        <div class="col-sm-9">
                                            <select class="form-control data-select2" name="color" id="colorSelect"
                                                style="width: 100%" required>
                                                <option value="">** Please select a Color</option>
                                                <option value="Y">Y</option>
                                                <option value="N">N</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-4 field-wrapper required-field">
                                        <label for="nonInvoiceableSelect" class="col-sm-3 col-form-label">Non
                                            Invoiceable</label>
                                        <div class="col-sm-9">
                                            <select class="form-control data-select2" name="non_invoiceable"
                                                id="nonInvoiceableSelect" style="width: 100%" required>
                                                <option value="">** Please select a Non Invoiceable</option>
                                                <option value="Y">Y</option>
                                                <option value="N">N</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-4 field-wrapper">
                                        <label for="remark" class="col-sm-3 col-form-label">Remark</label>
                                        <div class="col-sm-9">
                                            <textarea class="form-control" name="remark" id="remark" rows="5"></textarea>
                                        </div>
                                    </div>
                                    <div class="row mb-4 field-wrapper required-field">
                                        <label for="statusOrder" class="col-sm-3 col-form-label">Status</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="status" id="statusOrder"
                                                value="Request" required readonly>
                                            <input type="text" class="form-control" name="total_price"
                                                id="total-Price" hidden>
                                        </div>
                                    </div>
                                </div>
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
                                                    <td class="align-middle text-center">
                                                        <input type="checkbox" id="checkAllRows">
                                                    </td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-center" colspan="9">There is no data yet, please
                                                        select Order Confirmation</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- end col -->
                            </div>
                            <!-- end row -->

                            <div class="card-header pb-0" style="cursor: pointer" id="headerPayment"
                                onclick="toggle('#bodyPayment')">
                                <h4><i class="mdi mdi-checkbox-marked-outline"></i> Payment</h4>
                            </div>
                            <div class="card-body" id="bodyPayment">
                                <div class="mt-4 mt-lg-0">
                                    <div class="row mb-4 field-wrapper required-field">
                                        <label for="termPaymentSelect" class="col-sm-3 col-form-label">Term
                                            Payment</label>
                                        <div class="col-sm-9">
                                            <select class="form-control data-select2" name="id_master_term_payments"
                                                id="termPaymentSelect" style="width: 100%" required>
                                                <option value="">** Please select a Term Payment</option>
                                                @foreach ($termPayments as $termPayment)
                                                    <option value="{{ $termPayment->id }}">
                                                        {{ $termPayment->term_payment }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-4 field-wrapper required-field">
                                        <label for="ppnSelect" class="col-sm-3 col-form-label">Ppn</label>
                                        <div class="col-sm-9">
                                            <select class="form-control data-select2" name="ppn" id="ppnSelect"
                                                style="width: 100%" required>
                                                <option value="">** Please select a Ppn</option>
                                                <option value="Include">Inclue</option>
                                                <option value="Exclude">Exclude</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="card-footer">
                                <div class="row justify-content-end">
                                    <div class="col-sm-9">
                                        <div>
                                            <a href="{{ route('marketing.salesOrder.index') }}"
                                                class="btn btn-light w-md"><i class="fas fa-arrow-left"></i>&nbsp;
                                                Back</a>
                                            <input type="submit" class="btn btn-primary w-md saveSalesOrder"
                                                value="Save & Add More" name="save_add_more">
                                            <input type="submit" class="btn btn-success w-md saveSalesOrder"
                                                value="Save" name="save">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
