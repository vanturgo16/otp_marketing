$(document).ready(function () {
    $('.data-select2').select2({
        width: 'resolve', // need to override the changed default
        theme: "classic"
    });

    // Panggil fungsi saat halaman dimuat
    toggleElementsVisibility();

    // ketika option customer berubah
    $('#orderSelect').change(function () {
        let order_number = $(this).val();

        // mengambil data customer, salesman, term payment dan ppn sesuai order_number yang dipilih
        $.ajax({
            url: baseRoute + '/marketing/salesOrder/get-order-detail',
            type: 'GET',
            dataType: 'json',
            data: {
                order_number: order_number
            },
            success: function (response) {
                // console.log(response);
                if (response.order != '') {
                    let idMasterCustomer = response.order.id_master_customers;
                    let idMasterSalesman = response.order.id_master_salesmen;
                    let idMasterTermPayment = response.order.id_master_term_payments;
                    let ppn = response.order.ppn;
                    let masterCustomerAddress = response.order.master_customer_address;

                    // Cek apakah hanya ada satu data di master_customer_address
                    if (masterCustomerAddress.length === 1) {
                        // Jika hanya satu data, atur option sebagai selected dan disabled
                        let optionsCustomerAddress = `<option value="${masterCustomerAddress[0].id}" selected>${masterCustomerAddress[0].address}</option>`;
                        $('#customerAddressSelect').html(optionsCustomerAddress);
                        $('#customerAddressSelect').prop('disabled', true);
                    } else {
                        // Jika lebih dari satu data, buat options seperti biasa
                        let optionsCustomerAddress = `<option value="">** Please select a Customer Address</option>${masterCustomerAddress.map(address => `<option value="${address.id}">${address.address}</option>`).join('')}`;
                        $('#customerAddressSelect').html(optionsCustomerAddress);
                        $('#customerAddressSelect').prop('disabled', false);
                    }

                    let optionsCustomer = `<option value="">** Please select a Customer</option>${response.customers.map(customer => `<option value="${customer.id}" ${idMasterCustomer == customer.id ? 'selected' : ''}>${customer.name}</option>`).join('')}`;
                    $('#customerSelect').html(optionsCustomer);

                    let isCustomerDisabled = response.customers.some(customer => idMasterCustomer == customer.id);
                    $('#customerSelect').prop('disabled', isCustomerDisabled);

                    // let optionsCustomerAddress = `<option value="">** Please select a Customer Address</option>${response.order.master_customer_address.map(address => `<option value="${address.id}">${address.address}</option>`).join('')}`;
                    // $('#customerAddressSelect').html(optionsCustomerAddress);

                    let optionsSalesman = `<option value="">** Please select a Salesman</option>${response.salesmans.map(salesman => `<option value="${salesman.id}" ${idMasterSalesman == salesman.id ? 'selected' : ''}>${salesman.name}</option>`).join('')}`;
                    $('#salesmanSelect').html(optionsSalesman);

                    let isSalesmanDisabled = response.salesmans.some(salesman => idMasterSalesman == salesman.id);
                    $('#salesmanSelect').prop('disabled', isSalesmanDisabled);

                    let optionsTermPayment = `<option value="">** Please select a Term Payment</option>${response.termPayments.map(termPayment => `<option value="${termPayment.id}" ${idMasterTermPayment == termPayment.id ? 'selected' : ''}>${termPayment.term_payment}</option>`).join('')}`;
                    $('#termPaymentSelect').html(optionsTermPayment);

                    let isTermPaymentDisabled = response.termPayments.some(termPayment => idMasterTermPayment == termPayment.id);
                    $('#termPaymentSelect').prop('disabled', isTermPaymentDisabled);

                    let optionsPpn = `<option value="">** Please select a Ppn</option>` +
                        `<option value="Include" ${ppn == 'Include' ? 'selected' : ''}>Include</option>` +
                        `<option value="Exclude" ${ppn == 'Exclude' ? 'selected' : ''}>Exclude</option>`;
                    $('#ppnSelect').html(optionsPpn);

                    let $ppnSelect = $('#ppnSelect');
                    if ($ppnSelect != '') {
                        $ppnSelect.prop('disabled', true);
                    } else {
                        $ppnSelect.prop('disabled', false);
                    }

                    $('#reference_number').prop('readonly', true);
                    $('#reference_number').val('-');
                    $('#product_list').removeClass('d-none');
                    // Tambahkan kelas d-none jika tidak ada order
                    $('#addProduct').addClass('d-none');

                    // Menghapus atribut required pada elemen-elemen di dalam #addProduct
                    $('#addProduct .required-field').find('select, input').prop('required', false);
                    lastCheckedCheckbox = null;

                    $('.typeProductSelect').html('<select class="form-control data-select2 typeProductSelect"name="type_product" onchange="fetchProducts(this);" style="width: 100%"required><option value="">** Please select a Type Product</option><option value="WIP">WIP</option><option value="FG">FG</option></select>');
                    $('.productSelect').html('<select class="form-control data-select2 productSelect"name="id_master_products" onchange="fethchProductDetail(this);"style="width: 100%" required><option value="">** Please select a Product</option></select>');
                    $('.custProductCode').val('');
                    $('.qty').val('');
                    getAllUnit()
                        .then(response => {
                            // Lakukan sesuatu dengan response
                            let optionsUnit = `<option value="">** Please select a Unit</option>${response.map(unit => `<option value="${unit.id}">${unit.unit}</option>`).join('')}`;
                            // unitSelect.html(optionsUnit);
                            $('.unitSelect').html(optionsUnit);
                        })
                        .catch(error => {
                            // Tangani kesalahan
                            console.error(error);
                        });
                    $('.price').val('');
                    $('.total_price').val('');

                    getDetailOrder(order_number, response);
                } else {
                    let optionsCustomer = `<option value="">** Please select a Customer</option>${response.customers.map(customer => `<option value="${customer.id}">${customer.name}</option>`).join('')}`;
                    $('#customerSelect').html(optionsCustomer);
                    $('#customerSelect').prop('disabled', false);

                    let optionsCustomerAddress = `<option value="">** Please select a Customers Address</option>`;
                    $('#customerAddressSelect').html(optionsCustomerAddress);
                    $('#customerAddressSelect').prop('disabled', false);

                    let optionsSalesman = `<option value="">** Please select a Salesman</option>${response.salesmans.map(salesman => `<option value="${salesman.id}">${salesman.name}</option>`).join('')}`;
                    $('#salesmanSelect').html(optionsSalesman);
                    $('#salesmanSelect').prop('disabled', false);

                    let optionsTermPayment = `<option value="">** Please select a Term Payment</option>${response.termPayments.map(termPayment => `<option value="${termPayment.id}">${termPayment.term_payment}</option>`).join('')}`;
                    $('#termPaymentSelect').html(optionsTermPayment);
                    $('#termPaymentSelect').prop('disabled', false);

                    let optionsPpn = `<option value="">** Please select a Ppn</option>` +
                        `<option value="Include">Include</option>` +
                        `<option value="Exclude">Exclude</option>`;
                    $('#ppnSelect').html(optionsPpn);
                    $('#ppnSelect').prop('disabled', false);

                    $('#reference_number').prop('readonly', false);
                    $('#product_list').addClass('d-none');
                    // Hapus kelas d-none jika ada order
                    $('#addProduct').removeClass('d-none');

                    // Tambahkan atribut required pada elemen-elemen di dalam #addProduct
                    $('#addProduct .required-field').find('select, input').prop('required', true);
                    lastCheckedCheckbox = null;

                    $('#productTable tbody, #productTable tfoot').empty();
                    $('#productTable').append('<tr><td class="text-center" colspan="9">There is no data yet, please select Order Confirmation</td></tr>');
                    $('#total-Price').val('');
                    $('.total_price').val('');
                }
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    })

    $('#soTypeSelect').change(function () {
        let so_type = $(this).val();

        if (so_type != '') {
            $.ajax({
                url: baseRoute + '/marketing/salesOrder/generate-so-number',
                type: 'GET',
                dataType: 'json',
                data: {
                    so_type: so_type
                },
                success: function (response) {
                    // console.log(response);
                    $('#so_number').val(response.code)
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        } else {
            $('#so_number').val('')
        }

        // Panggil fungsi saat halaman dimuat
        toggleElementsVisibility();
    });

    // ketika option customer berubah
    $('#customerSelect').change(function () {
        let idCustomer = $(this).val();

        // mengambil data address, salesman, term payment dan ppn sesuai idCustomer yang dipilih
        $.ajax({
            url: baseRoute + '/marketing/salesOrder/get-customer-detail',
            type: 'GET',
            dataType: 'json',
            data: {
                idCustomer: idCustomer
            },
            success: function (response) {
                // console.log(response);
                let masterCustomerAddress = response.customer_addresses;
                if (response.customer != '') {
                    let idMasterSalesman = response.customer.id_master_salesmen;
                    let idMasterTermPayment = response.customer.id_master_term_payments;
                    let ppn = response.customer.ppn;

                    // Cek apakah hanya ada satu data di master_customer_address
                    if (masterCustomerAddress.length === 1) {
                        // Jika hanya satu data, atur option sebagai selected dan disabled
                        let optionsCustomerAddress = `<option value="${masterCustomerAddress[0].id}" selected>${masterCustomerAddress[0].address}</option>`;
                        $('#customerAddressSelect').html(optionsCustomerAddress);
                        $('#customerAddressSelect').prop('disabled', true);
                    } else {
                        // Jika lebih dari satu data, buat options seperti biasa
                        let optionsCustomerAddress = `<option value="">** Please select a Customer Address</option>${masterCustomerAddress.map(address => `<option value="${address.id}">${address.address}</option>`).join('')}`;
                        $('#customerAddressSelect').html(optionsCustomerAddress);
                        $('#customerAddressSelect').prop('disabled', false);
                    }

                    let optionsSalesman = `<option value="">** Please select a Salesman</option>${response.salesmans.map(salesman => `<option value="${salesman.id}" ${idMasterSalesman == salesman.id ? 'selected' : ''}>${salesman.name}</option>`).join('')}`;
                    $('#salesmanSelect').html(optionsSalesman);

                    let isSalesmanDisabled = response.salesmans.some(salesman => idMasterSalesman == salesman.id);
                    $('#salesmanSelect').prop('disabled', isSalesmanDisabled);

                    let optionsTermPayment = `<option value="">** Please select a Term Payment</option>${response.termPayments.map(termPayment => `<option value="${termPayment.id}" ${idMasterTermPayment == termPayment.id ? 'selected' : ''}>${termPayment.term_payment}</option>`).join('')}`;
                    $('#termPaymentSelect').html(optionsTermPayment);

                    let isTermPaymentDisabled = response.termPayments.some(termPayment => idMasterTermPayment == termPayment.id);
                    $('#termPaymentSelect').prop('disabled', isTermPaymentDisabled);

                    let optionsPpn = `<option value="">** Please select a Ppn</option>` +
                        `<option value="Include" ${ppn == 'Include' ? 'selected' : ''}>Include</option>` +
                        `<option value="Exclude" ${ppn == 'Exclude' ? 'selected' : ''}>Exclude</option>`;
                    $('#ppnSelect').html(optionsPpn);

                    let $ppnSelect = $('#ppnSelect');
                    if ($ppnSelect != '') {
                        $ppnSelect.prop('disabled', true);
                    } else {
                        $ppnSelect.prop('disabled', false);
                    }
                } else {
                    let optionsCustomerAddress = `<option value="">** Please select a Customer Address</option>${masterCustomerAddress.map(address => `<option value="${address.id}">${address.address}</option>`).join('')}`;
                    $('#customerAddressSelect').html(optionsCustomerAddress);
                    $('#customerAddressSelect').prop('disabled', false);

                    let optionsSalesman = `<option value="">** Please select a Salesman</option>${response.salesmans.map(salesman => `<option value="${salesman.id}">${salesman.name}</option>`).join('')}`;
                    $('#salesmanSelect').html(optionsSalesman);
                    $('#salesmanSelect').prop('disabled', false);

                    let optionsTermPayment = `<option value="">** Please select a Term Payment</option>${response.termPayments.map(termPayment => `<option value="${termPayment.id}">${termPayment.term_payment}</option>`).join('')}`;
                    $('#termPaymentSelect').html(optionsTermPayment);
                    $('#termPaymentSelect').prop('disabled', false);

                    let optionsPpn = `<option value="">** Please select a Ppn</option>` +
                        `<option value="Include">Include</option>` +
                        `<option value="Exclude">Exclude</option>`;
                    $('#ppnSelect').html(optionsPpn);
                    $('#ppnSelect').prop('disabled', false);
                }
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    })

    function addDays(date, days) {
        const copy = new Date(Number(date))
        copy.setDate(date.getDate() + days)
        return copy
    }

    $('#date').change(function () {
        const date = new Date($(this).val());
        const newDate = addDays(date, 14);
        const due_date = newDate.toISOString().split('T')[0];

        $('#due_date').val(due_date)
    });

    // Check All functionality
    $('#checkAllRows').change(function () {
        $('.rowCheckbox').prop('checked', this.checked);
        toggleHighlight();
        calculateTotalPriceTable();
    });

    // $(document).on('change', '.rowCheckbox', function () {
    //     // $(this).closest('tr').toggleClass('table-success', this.checked);
    //     if (!this.checked) {
    //         $('#checkAllRows').prop('checked', false);
    //     } else {
    //         // Check if all checkboxes in tbody are checked
    //         if ($('.rowCheckbox:checked').length === $('.rowCheckbox').length) {
    //             $('#checkAllRows').prop('checked', true);
    //         }
    //     }
    //     toggleHighlight();
    //     calculateTotalPriceTable();
    // });

    let lastCheckedCheckbox = null;

    $(document).on('change', '.rowCheckbox', function () {
        if (this.checked) {
            // Jika checkbox dicentang, periksa apakah sudah ada checkbox yang dicentang sebelumnya
            if (lastCheckedCheckbox !== null && lastCheckedCheckbox !== this) {
                // Jika sudah ada checkbox yang dicentang, batalkan centang dan tampilkan pesan
                alert('Hanya satu baris yang dapat dipilih dalam satu waktu. Harap hapus centang pada baris sebelumnya sebelum memilih yang baru.');
                $(this).prop('checked', false);
            } else {
                // Setel checkbox yang baru dicentang sebagai checkbox terakhir yang dicentang
                lastCheckedCheckbox = this;
            }
        } else {
            // Jika checkbox dicentang, hapus referensi ke checkbox terakhir yang dicentang
            lastCheckedCheckbox = null;
        }

        // Lakukan aksi lain setelah perubahan checkbox
        if (!this.checked) {
            $('#checkAllRows').prop('checked', false);
        } else {
            // Check if all checkboxes in tbody are checked
            if ($('.rowCheckbox:checked').length === $('.rowCheckbox').length) {
                $('#checkAllRows').prop('checked', true);
            }
        }
        toggleHighlight();
        calculateTotalPriceTable();
    });

    // Toggle checkbox when clicking on the row
    $(document).on('click', '#productTable tbody tr', function (event) {
        // Check if the click is on the checkbox, if yes, don't toggle the checkbox
        if (!$(event.target).is(':checkbox')) {
            const checkbox = $(this).find('.rowCheckbox');
            checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
            // toggleHighlight();
            // calculateTotalPriceTable();
        }
    });

    // $("form").submit(function () {
    //     $("select").removeAttr("disabled");
    // });

    $(document).on('submit', '#formSalesOrder', function (e) {
        e.preventDefault(); // Mencegah formulir terkirim secara default
        let so_type = $('#soTypeSelect').val();
        let order_confirmation = $('#orderSelect').val();

        // Cek apakah ada setidaknya satu produk yang dicentang
        var selectedRows = $('.rowCheckbox:checked');

        console.log(order_confirmation);
        // Lakukan tindakan berdasarkan kondisi
        if (order_confirmation !== '' && order_confirmation != '0') {
            if (selectedRows.length === 0) {
                // Jika tidak ada produk yang dicentang, tampilkan pesan kesalahan
                alert('Pilih setidaknya satu produk untuk melanjutkan.');
                return; // Hentikan eksekusi fungsi
            }
        }

        // Lanjutkan dengan pengiriman form
        $("select").removeAttr("disabled");
        if (so_type == "Stock") {
            $("#customerSelect, #customerAddressSelect, #salesmanSelect").attr("disabled", true);
        }
        this.submit();
    });

    $(document).on('change', '.qty', function () {
        calculateTotalPrice()
    });


    const pathArray = window.location.pathname.split("/");
    const segment_3 = pathArray[3];
    if (segment_3 == 'show') {
        viewSalesOrder();
    } else if (segment_3 == 'edit') {
        editSalesOrder();
    }
});

function filterSearch(button) {
    let search = $(button).attr('data-search');
    // Ambil DataTable instance
    var dataTable = $('#so_customer_table').DataTable();

    // Atur nilai pencarian untuk "SO Customer"
    dataTable.search(search).draw();

    // Perbarui tampilan DataTable
    dataTable.draw();
}

// Fungsi untuk menampilkan/sembunyikan elemen berdasarkan nilai so_type
function toggleElementsVisibility() {
    var soType = $('#soTypeSelect').val(); // Ambil nilai so_type dari elemen dengan ID so_type

    // Cek nilai so_type dan sesuaikan tampilan elemen
    if (soType === 'Stock') {
        $('.customerSection').hide(); // Sembunyikan bagian customer
        $('.customerAddressSection').hide(); // Sembunyikan bagian customer address
        $('.salesmanSection').hide(); // Sembunyikan bagian salesman
    } else {
        $('.customerSection').show(); // Tampilkan bagian customer
        $('.customerAddressSection').show(); // Tampilkan bagian customer address
        $('.salesmanSection').show(); // Tampilkan bagian salesman
    }
}

function toggle(element) {
    $(element).slideToggle(500);
}

// Toggle row highlight based on checkbox state
function toggleHighlight() {
    $('.rowCheckbox').each(function () {
        $(this).closest('tr').toggleClass('table-success', this.checked);
    });
}

// Calculate total price based on checked rows
function calculateTotalPriceTable() {
    let totalPrice = 0;
    $('.rowCheckbox:checked').each(function () {
        // Menghilangkan titik sebelum mengonversi ke float
        let subtotalText = $(this).closest('tr').find('.subtotal').text().replace(/\./g, "");
        totalPrice += parseFloat(subtotalText);
    });
    // $('#totalPrice').text(totalPrice.toFixed(2));
    $('#totalPrice').text(totalPrice.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1."));
    $('#total-Price').val(totalPrice);
    $('.total_price').val(totalPrice);
}

function getDetailOrder(order_number, response) {
    // console.log(response);
    if (response.order != null) {
        let products = response.products

        // Mendapatkan detail dari respons AJAX
        if (order_number.substring(0, 2) == 'PO') {
            var details = response.order.input_p_o_customer_details;
        } else if (order_number.substring(0, 2) == 'KO') {
            var details = response.order.order_confirmation_details;
        }

        $('#productTable tbody, #productTable tfoot').empty();

        // Mengisi baris baru sesuai dengan detail
        for (let i = 0; i < details.length; i++) {
            // Fungsi untuk mendapatkan produk sesuai dengan tipe dan kode produk
            function getFilteredProduct(type, code) {
                return products.filter(product => product.type_product === type && product.id === code);
            }

            // Fungsi untuk menampilkan hasil pencarian
            function displaySearchResult(type, code) {
                // Mendapatkan hasil pencarian
                let result = getFilteredProduct(type, code);
                return result[0]['description'];
                // Menampilkan hasil pencarian (misalnya, di konsol)
                // console.log(result);
                // Jika ingin menampilkan hasil pada elemen HTML, sesuaikan kode di sini
            }

            // Contoh pemanggilan fungsi dengan tipe dan kode produk tertentu
            let description = displaySearchResult(details[i].type_product, details[i].id_master_product);
            const custProductCode = details[i].cust_product_code !== null ? details[i].cust_product_code : '';

            // Check if the product is in response.compare
            let isInCompare = response.compare.some(compareProduct =>
                compareProduct.id_master_products === details[i].id_master_product && compareProduct.type_product === details[i].type_product
            );

            // Add a condition to show or hide the checkbox
            let checkboxHtml = isInCompare ? '' : '<input type="checkbox" class="rowCheckbox" name="selected_rows[]" value="' + i + '">';
            let tableColor = isInCompare ? 'table-danger' : '';

            $('#productTable').append('<tr class="row-check ' + tableColor + '"><td class="text-center">' + (i + 1) + '</td>  <td class="text-center"><input type="text" class="form-control d-none" name="type_product[]" value="' + details[i].type_product + '" readonly>' + details[i].type_product + '</td> <td><input type="text" class="form-control d-none" name="id_master_products[]" value="' + details[i].id_master_product + '" readonly>' + description + '</td> <td><input type="text" class="form-control d-none" name="cust_product_code[]" value="' + custProductCode + '" readonly>' + custProductCode + '</td> <td><input type="text" class="form-control d-none" name="id_master_units[]" value="' + details[i].master_unit.id + '" readonly>' + details[i].master_unit.unit + '</td> <td class="text-center"><input type="text" class="form-control d-none" name="qty[]" value="' + details[i].qty + '" readonly>' + details[i].qty + '</td> <td class="text-end"><input type="text" class="form-control d-none" name="price[]" value="' + details[i].price + '" readonly>' + details[i].price.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.") + '</td> <td class="text-end"><input type="text" class="form-control d-none" name="subtotal[]" value="' + details[i].subtotal + '" readonly><span class="subtotal">' + details[i].subtotal.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.") + '</span></td><td class="text-center align-middle">' + checkboxHtml + '</td></tr>');

        }

        // $('#productTable').append('<tfoot><tr><td colspan="7" class="text-end text-black">Total Price</td><td class="text-end" colspan="2"><span id="totalPrice">0<input type="text" class="form-control" name="total_price" id="total-Price" hidden></span></td></tr></tfoot>');
    }
}

function fetchProducts(selectElement) {
    let selectedType = $(selectElement).val();
    let productSelect = $('.productSelect');
    let unitSelect = $('.unitSelect');
    let options = '<option value="">** Please select a Product</option>';
    // let optionsUnit = '<option value="">** Please select a Unit</option>';
    // console.log(selectedType);

    // Cara menggunakannya
    getAllUnit()
        .then(response => {
            // Lakukan sesuatu dengan response
            let optionsUnit = `<option value="">** Please select a Unit</option>${response.map(unit => `<option value="${unit.id}">${unit.unit}</option>`).join('')}`;
            // unitSelect.html(optionsUnit);
            unitSelect.html(optionsUnit);
        })
        .catch(error => {
            // Tangani kesalahan
            console.error(error);
        });

    // Hanya membuat permintaan AJAX jika tipe dipilih
    if (selectedType) {
        $.ajax({
            url: baseRoute + '/marketing/salesOrder/get-data-product',
            type: 'GET',
            dataType: 'json',
            data: {
                typeProduct: selectedType
            },
            success: function (response) {
                // Tanggapi dengan mengisi opsi produk sesuai data dari server
                // console.log(response);
                $.each(response.products, function (index, product) {
                    options += '<option value="' + product.id + '">' + product.description + '</option>';
                });
                productSelect.html(options);

                // price.val('');
                calculateTotalPrice(selectElement)
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    } else {
        productSelect.html(options);
        getAllUnit()
            .then(response => {
                // Lakukan sesuatu dengan response
                let optionsUnit = `<option value="">** Please select a Unit</option>${response.map(unit => `<option value="${unit.id}">${unit.unit}</option>`).join('')}`;
                // unitSelect.html(optionsUnit);
                unitSelect.html(optionsUnit);
            })
            .catch(error => {
                // Tangani kesalahan
                console.error(error);
            });
    }
    $('.custProductCode').val('');
    $('.qty').val('');
    $('.price').val('');
    $('.total_price').val('');
}

function fethchProductDetail(selectElement) {
    let typeProduct = $('.typeProductSelect').val();
    let selectedProductId = $(selectElement).val();

    if (selectedProductId) {
        $.ajax({
            url: baseRoute + '/marketing/salesOrder/get-product-detail',
            type: 'GET',
            dataType: 'json',
            data: {
                typeProduct: typeProduct,
                idProduct: selectedProductId
            },
            success: function (response) {
                // console.log(response);
                let unitSelect = $('.unitSelect');
                let idUnit = response.product.id_master_units;
                getAllUnit()
                    .then(response => {
                        // Lakukan sesuatu dengan response
                        let optionsUnit = `<option value="">** Please select a Unit</option>${response.map(unit => `<option value="${unit.id}"${idUnit == unit.id ? 'selected' : ''}>${unit.unit}</option>`).join('')}`;
                        unitSelect.html(optionsUnit);
                    })
                    .catch(error => {
                        // Tangani kesalahan
                        console.error(error);
                    });
                $('.price').val(Math.floor(response.product.price)) || 0;
                $('.custProductCode').focus();
                calculateTotalPrice(selectElement);
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }
    calculateTotalPrice(selectElement);
}

function calculateTotalPrice(selectElement) {
    let qty = $('.qty').val() || 0;
    let price = $('.price').val() || 0;

    let total_price = qty * price;
    $('.total_price').val(total_price.toFixed(0));
}

function getAllUnit() {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: baseRoute + '/marketing/salesOrder/get-all-unit',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                resolve(response);
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
                reject(error);
            }
        });
    });
}

function editSalesOrder() {
    so_number = $('#so_number').val();

    if (so_number) {
        $.ajax({
            url: baseRoute + '/marketing/salesOrder/get-data-sales-order',
            type: 'GET',
            dataType: 'json',
            data: {
                so_number: so_number,
            },
            success: function (response) {
                console.log(response);
                getDetailOrder(response.order.id_order_confirmations, response);
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }
}
