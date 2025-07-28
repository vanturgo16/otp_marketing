@extends('layouts.master')

@section('konten')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Add Order Confirmation</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Marketing</a></li>
                                <li class="breadcrumb-item active">Add Order Confirmation</li>
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
                    <a href="{{ route('marketing.orderConfirmation.index') }}"
                        class="btn btn-primary waves-effect btn-label waves-light">
                        <i class="mdi mdi-arrow-left label-icon"></i> Back To List Data Order Confirmation
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <form action="{{ route('marketing.orderConfirmation.store') }}" method="POST">
                            @csrf
                            <div class="card-header">
                                <i class="mdi mdi-file-multiple-outline label-icon"></i> Add Order Confirmation
                            </div>

                            <div class="card-body">
                                <div class="mt-4 mt-lg-0">
                                    <div class="row mb-4 field-wrapper required-field">
                                        <label for="ocNumber" class="col-sm-3 col-form-label">OC Number</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="oc_number" id="ocNumber"
                                                value="{{ $kodeOtomatis }}" required readonly>
                                        </div>
                                    </div>
                                    <div class="row mb-4 field-wrapper required-field">
                                        <label for="date" class="col-sm-3 col-form-label">Date</label>
                                        <div class="col-sm-9">
                                            <input type="date" class="form-control" name="date" id="date"
                                                value="{{ date('Y-m-d') }}" required>
                                        </div>
                                    </div>
                                    <div class="row mb-4 field-wrapper required-field">
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
                                    <div class="row mb-4 field-wrapper required-field">
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
                                    <div class="row mb-4 field-wrapper required-field">
                                        <label for="termPaymentSelect" class="col-sm-3 col-form-label">Term Payment</label>
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
                                        <label for="currencySelect" class="col-sm-3 col-form-label">Currency</label>
                                        <div class="col-sm-9">
                                            <select class="form-control data-select2" name="id_master_currencies"
                                                id="currencySelect" style="width: 100%" required>
                                                <option value="">** Please select a Currency</option>
                                                @foreach ($currencies as $currency)
                                                    <option value="{{ $currency->id }}">{{ $currency->currency }}
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
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="table-responsive table-bordered">
                                        <table class="table mb-0" id="productTable">
                                            <tbody>
                                                <tr class="product-row">
                                                    <td>
                                                        <div class="judul mb-2 d-flex align-items-center"
                                                            style="background-color: aliceblue">
                                                            <button class="accordion-button" type="button"
                                                                onclick="toggleRow(this)">
                                                                <b class="fs-2">Add Product</b>
                                                            </button>
                                                            <div class="removeButtonContainer">
                                                                <button type="button"
                                                                    class="btn btn-sm btn-danger waves-effect waves-light"
                                                                    onclick="confirmDelete(this)">Remove
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="accordion-body-dynamic">
                                                            <div class="row mb-4 field-wrapper required-field">
                                                                <label for="typeProductSelect"
                                                                    class="col-sm-3 col-form-label">Type
                                                                    Product</label>
                                                                <div class="col-sm-9">
                                                                    <select
                                                                        class="form-control data-select2 typeProductSelect"
                                                                        name="type_product[]"
                                                                        onchange="fetchProducts(this);"
                                                                        style="width: 100%" required>
                                                                        <option value="">** Please
                                                                            select a Type Product</option>
                                                                        <option value="WIP">WIP</option>
                                                                        <option value="FG">FG</option>
                                                                        <option value="RM">RAW MATERIAL</option>
                                                                        <option value="AUX">AUXILIARY</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-4 field-wrapper required-field">
                                                                <label for="productSelect"
                                                                    class="col-sm-3 col-form-label">Product</label>
                                                                <div class="col-sm-9">
                                                                    <select class="form-control data-select2 productSelect"
                                                                        name="id_master_products[]"
                                                                        onchange="fethchProductDetail(this);"
                                                                        style="width: 100%" required>
                                                                        <option value="">** Please
                                                                            select a Product</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-4 field-wrapper">
                                                                <label for="cust_product_code"
                                                                    class="col-sm-3 col-form-label">Cust
                                                                    Product
                                                                    Code</label>
                                                                <div class="col-sm-9">
                                                                    <input type="text"
                                                                        class="form-control custProductCode"
                                                                        name="cust_product_code[]">
                                                                </div>
                                                            </div>
                                                            <div class="row mb-4 field-wrapper required-field">
                                                                <label for="qty"
                                                                    class="col-sm-3 col-form-label">Qty</label>
                                                                <div class="col-sm-9">
                                                                    <input type="text" class="form-control qty"
                                                                        name="qty[]" onkeyup="calculateSubtotal(this)"
                                                                        required>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-4 field-wrapper required-field">
                                                                <label for="unit"
                                                                    class="col-sm-3 col-form-label">Unit</label>
                                                                <div class="col-sm-9">
                                                                    <select class="form-control data-select2 unitSelect"
                                                                        name="id_master_units[]" style="width: 100%"
                                                                        required>
                                                                        <option value="" selected>**
                                                                            Please select a Unit</option>
                                                                        @foreach ($units as $unit)
                                                                            <option value="{{ $unit->id }}">
                                                                                {{ $unit->unit_code }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-4 field-wrapper required-field">
                                                                <label for="price"
                                                                    class="col-sm-3 col-form-label">Price</label>
                                                                <div class="col-sm-9 ">
                                                                    <input type="text" class="form-control price"
                                                                        name="price[]" onkeyup="calculateSubtotal(this)"
                                                                        required>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-4 field-wrapper required-field">
                                                                <label for="subtotal"
                                                                    class="col-sm-3 col-form-label">Subtotal</label>
                                                                <div class="col-sm-9">
                                                                    <input type="text" class="form-control subtotal"
                                                                        name="subtotal[]" required readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>

                                            <tfoot>
                                                <tr>
                                                    <th>
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <button type="button"
                                                                class="btn btn-info waves-effect waves-light"
                                                                onclick="cloneRow()">Add More Product</button>
                                                            <span class="fs-3">Total Price : <span
                                                                    id="totalAmount">0</span></span>
                                                            <input type="hidden" name="total_price" class="totalPrice">
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

                            <div class="card-footer">
                                <div class="row justify-content-end">
                                    <div class="col-sm-9">
                                        <div>
                                            <a href="{{ route('marketing.orderConfirmation.index') }}"
                                                class="btn btn-light w-md"><i class="fas fa-arrow-left"></i>&nbsp;
                                                Back</a>
                                            <input type="submit" class="btn btn-primary w-md" value="Save & Add More"
                                                name="save_add_more">
                                            <input type="submit" class="btn btn-success w-md" value="Save"
                                                name="save">
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

    <!-- Static Backdrop Modal -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Confirm to Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to <span class="text-danger">delete</span> this data?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger waves-effect btn-label waves-light"
                        onclick="removeRow()"><i class="mdi mdi-delete label-icon"></i>Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection
