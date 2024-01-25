@extends('layouts.master')

@section('konten')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Edit PO Customer</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Marketing</a></li>
                                <li class="breadcrumb-item active">Edit PO Customer</li>
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
                        <form action="{{ route('marketing.inputPOCust.store') }}" method="POST">
                            @csrf
                            <div class="card-header">
                                <i class="mdi mdi-file-multiple-outline label-icon"></i> Add PO Customer
                            </div>

                            <div class="card-body">
                                <div class="mt-4 mt-lg-0">
                                    <div class="row mb-4 field-wrapper required-field">
                                        <label for="po_number" class="col-sm-3 col-form-label">PO Number</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="po_number" id="po_number"
                                                value="{{ $inputPOCustomer->po_number }}" required readonly>
                                        </div>
                                    </div>
                                    <div class="row mb-4 field-wrapper required-field">
                                        <label for="date" class="col-sm-3 col-form-label">Date</label>
                                        <div class="col-sm-9">
                                            <input type="date" class="form-control" name="date" id="date"
                                                value="{{ date($inputPOCustomer->date) }}" required>
                                        </div>
                                    </div>
                                    <div class="row mb-4 field-wrapper required-field">
                                        <label for="customerSelect" class="col-sm-3 col-form-label">Customer</label>
                                        <div class="col-sm-9">
                                            <select class="form-control data-select2" name="id_master_customers"
                                                id="customerSelect" required>
                                                <option value="">** Please select a Customers</option>
                                                @foreach ($customers as $customer)
                                                    <option value="{{ $customer->id }}"
                                                        {{ $customer->id == $inputPOCustomer->id_master_customers ? 'selected' : '' }}>
                                                        {{ $customer->customer_code }} -
                                                        {{ $customer->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-4 field-wrapper required-field">
                                        <label for="salesmanSelect" class="col-sm-3 col-form-label">Salesman</label>
                                        <div class="col-sm-9">
                                            <select class="form-control data-select2" name="id_master_salesmen"
                                                id="salesmanSelect" required>
                                                <option value="">** Please select a Salesman</option>
                                                @foreach ($salesmans as $salesman)
                                                    <option value="{{ $salesman->id }}"
                                                        {{ $salesman->id == $inputPOCustomer->id_master_salesmen ? 'selected' : '' }}>
                                                        {{ $salesman->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-4 field-wrapper required-field">
                                        <label for="termPaymentSelect" class="col-sm-3 col-form-label">Term Payment</label>
                                        <div class="col-sm-9">
                                            <select class="form-control data-select2" name="id_master_term_payments"
                                                id="termPaymentSelect" required>
                                                <option value="">** Please select a Term Payment</option>
                                                @foreach ($termPayments as $termPayment)
                                                    <option value="{{ $termPayment->id }}"
                                                        {{ $termPayment->id == $inputPOCustomer->id_master_term_payments ? 'selected' : '' }}>
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
                                                id="currencySelect" required>
                                                <option value="">** Please select a Currency</option>
                                                @foreach ($currencies as $currency)
                                                    <option value="{{ $currency->id }}"
                                                        {{ $currency->id == $inputPOCustomer->id_master_currencies ? 'selected' : '' }}>
                                                        {{ $currency->currency }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-4 field-wrapper required-field">
                                        <label for="ppnSelect" class="col-sm-3 col-form-label">Ppn</label>
                                        <div class="col-sm-9">
                                            <select class="form-control data-select2" name="ppn" id="ppnSelect"
                                                required>
                                                <option value="">** Please select a Ppn</option>
                                                <option value="Include"
                                                    {{ $inputPOCustomer->ppn == 'Include' ? 'selected' : '' }}>Inclue
                                                </option>
                                                <option value="Exclude"
                                                    {{ $inputPOCustomer->ppn == 'Exclude' ? 'selected' : '' }}>Exclude
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-4 field-wrapper">
                                        <label for="remark" class="col-sm-3 col-form-label">Remark</label>
                                        <div class="col-sm-9">
                                            <textarea class="form-control" name="remark" id="remark" rows="5">{{ $inputPOCustomer->remark }}</textarea>
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
                                                @foreach ($inputPOCustomer->inputPOCustomerDetails as $detail)
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
                                                                        onclick="removeRow(this)">Remove
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
                                                                            onchange="fetchProducts(this);"required>
                                                                            <option value="">** Please
                                                                                select a Type Product</option>
                                                                            <option value="WIP"
                                                                                {{ $detail->type_product == 'WIP' ? 'selected' : '' }}>
                                                                                WIP</option>
                                                                            <option value="FG"
                                                                                {{ $detail->type_product == 'FG' ? 'selected' : '' }}>
                                                                                FG</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-4 field-wrapper required-field">
                                                                    <label for="productSelect"
                                                                        class="col-sm-3 col-form-label">Product</label>
                                                                    <div class="col-sm-9">
                                                                        <select
                                                                            class="form-control data-select2 productSelect"
                                                                            name="id_master_products[]"
                                                                            onchange="fethchProductDetail(this);" required>
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
                                                                            name="cust_product_code[]" value="{{ $detail->cust_product_code }}">
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-4 field-wrapper required-field">
                                                                    <label for="qty"
                                                                        class="col-sm-3 col-form-label">Qty</label>
                                                                    <div class="col-sm-9">
                                                                        <input type="number" class="form-control qty"
                                                                            name="qty[]"
                                                                            onkeyup="calculateSubtotal(this)" value="{{ $detail->qty }}" required>
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-4 field-wrapper required-field">
                                                                    <label for="unit"
                                                                        class="col-sm-3 col-form-label">Unit</label>
                                                                    <div class="col-sm-9">
                                                                        <select
                                                                            class="form-control data-select2 unitSelect"
                                                                            name="id_master_units[]" required>
                                                                            <option value="" selected>**
                                                                                Please select a Unit</option>
                                                                            @foreach ($units as $unit)
                                                                                <option value="{{ $unit->id }}" {{ $detail->id_master_units == $unit->id ? 'selected':'' }}>
                                                                                    {{ $unit->unit }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-4 field-wrapper required-field">
                                                                    <label for="price"
                                                                        class="col-sm-3 col-form-label">Price</label>
                                                                    <div class="col-sm-9 ">
                                                                        <input type="number" class="form-control price"
                                                                            name="price[]" value="{{ $detail->price }}" required>
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-4 field-wrapper required-field">
                                                                    <label for="subtotal"
                                                                        class="col-sm-3 col-form-label">Subtotal</label>
                                                                    <div class="col-sm-9">
                                                                        <input type="text"
                                                                            class="form-control subtotal"
                                                                            name="subtotal[]" value="{{ $detail->subtotal }}" required readonly>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
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
                                                            <input type="hidden" name="total_price" class="totalPrice" value="{{ $inputPOCustomer->total_price }}">
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
                                            <a href="{{ route('marketing.inputPOCust.index') }}"
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

    <div class="modal" id="confirmDeleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Penghapusan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus baris ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteButton">Hapus</button>
                </div>
            </div>
        </div>
    </div>
@endsection
