<?php

namespace App\Http\Controllers\Marketing;

use Browser;
use DataTables;
use Carbon\Carbon;
use App\Models\MstUnits;
use App\Models\MstCustomers;
use App\Models\MstSalesmans;
use Illuminate\Http\Request;
use App\Models\ppic\workOrder;
use App\Traits\AuditLogsTrait;
use App\Models\MstTermPayments;
use Illuminate\Support\Facades\DB;
use App\Models\MstCustomersAddress;
use App\Http\Controllers\Controller;
use App\Models\Marketing\salesOrder;
use App\Models\ppic\workOrderDetail;
use App\Models\Marketing\InputPOCust;
use Illuminate\Support\Facades\Crypt;
use App\Models\Marketing\salesOrderDetail;
use App\Models\Marketing\orderConfirmation;
use Illuminate\Database\Eloquent\JsonEncodingException;

class salesOrderController extends Controller
{
    use AuditLogsTrait;
    public function saveLogs($activityLog = null)
    {
        //Audit Log
        $username = auth()->user()->email;
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $location = '0';
        $access_from = Browser::browserName();
        $activity = $activityLog;
        $this->auditLogs($username, $ipAddress, $location, $access_from, $activity);
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (request()->ajax()) {
            $orderColumn = $request->input('order')[0]['column'];
            $orderDirection = $request->input('order')[0]['dir'];
            $columns = ['', 'id', 'id_order_confirmations', 'so_number', 'date', 'so_type', 'customer', 'salesman', 'reference_number', 'description', 'due_date', 'status', '', ''];

            // Query dasar
            $query = DB::table('sales_orders as a')
                ->leftJoin('master_customers as b', 'a.id_master_customers', '=', 'b.id')
                ->leftJoin('master_salesmen as c', 'a.id_master_salesmen', '=', 'c.id')
                // ->join('sales_order_details as d', 'a.so_number', '=', 'd.id_sales_orders')
                ->join(
                    \DB::raw(
                        '(SELECT id, product_code, description, id_master_units, \'FG\' as type_product FROM master_product_fgs WHERE status = \'Active\' UNION ALL SELECT id, wip_code as product_code, description, id_master_units, \'WIP\' as type_product FROM master_wips WHERE status = \'Active\') e'
                    ),
                    function ($join) {
                        // $join->on('d.id_master_products', '=', 'e.id');
                        // $join->on('d.type_product', '=', 'e.type_product');
                        $join->on('a.id_master_products', '=', 'e.id');
                        $join->on('a.type_product', '=', 'e.type_product');
                    }
                )
                // ->join('master_units as f', 'd.id_master_units', '=', 'f.id')
                ->join('master_units as f', 'a.id_master_units', '=', 'f.id')
                // ->select('a.id', 'a.id_order_confirmations', 'a.so_number', 'a.date', 'a.so_type', 'b.name as customer', 'c.name as salesman', 'a.reference_number', 'a.due_date', 'a.status', 'd.qty', 'd.outstanding_delivery_qty', 'e.product_code', 'e.description', 'f.unit_code')
                ->select('a.id', 'a.id_order_confirmations', 'a.so_number', 'a.date', 'a.so_type', 'b.name as customer', 'c.name as salesman', 'a.reference_number', 'a.due_date', 'a.status', 'a.qty', 'a.qty_results', 'a.outstanding_delivery_qty', 'e.product_code', 'e.description', 'f.unit_code')
                ->orderBy($columns[$orderColumn], $orderDirection);

            if ($request->has('type')) {
                if ($request->input('type') <> '') {
                    $query->where('a.so_type', '=', $request->input('type'));
                } else {
                    $query->where('a.so_type', '<>', $request->input('type'));
                }
            }

            // Handle pencarian
            if ($request->has('search') && $request->input('search')) {
                $searchValue = $request->input('search');
                $query->where(function ($query) use ($searchValue) {
                    $query->where('a.id_order_confirmations', 'like', '%' . $searchValue . '%')
                        ->orWhere('a.so_number', 'like', '%' . $searchValue . '%')
                        ->orWhere('a.date', 'like', '%' . $searchValue . '%')
                        // ->orWhere('a.so_type', 'like', '%' . $searchValue . '%')
                        ->orWhere('b.name', 'like', '%' . $searchValue . '%')
                        ->orWhere('c.name', 'like', '%' . $searchValue . '%')
                        ->orWhere('a.reference_number', 'like', '%' . $searchValue . '%')
                        ->orWhere('e.description', 'like', '%' . $searchValue . '%')
                        ->orWhere('a.due_date', 'like', '%' . $searchValue . '%')
                        ->orWhere('a.status', 'like', '%' . $searchValue . '%');
                });
            }

            return DataTables::of($query)
                ->addColumn('action', function ($data) {
                    return view('marketing.sales_order.action', compact('data'));
                })
                ->addColumn('bulk-action', function ($data) {
                    $checkBox = ($data->status == 'Request' || $data->status == 'Un Posted') ? '<input class="rowCheckboxIndex" type="checkbox" name="checkbox" data-so-number="' . $data->so_number . '" />' : '';
                    return $checkBox;
                })
                ->addColumn('progress', function ($data) {
                    $qty_results = $data->qty_results == null ? 0 : $data->qty_results;
                    $qty_cancel = $data->outstanding_delivery_qty == null ? 0 : $data->outstanding_delivery_qty;
                    $qty = $data->qty . ' ' . $data->unit_code;
                    $delivery_qty = $qty_results . ' ' . $data->unit_code;
                    $outstanding_qty = ($data->qty - ($qty_results + $qty_cancel)) . ' ' . $data->unit_code;
                    $cancel_qty = ($qty_cancel) . ' ' . $data->unit_code;

                    $dueDate = Carbon::parse($data->due_date);
                    $today = Carbon::today(); // Today's date
                    if ($today->isBefore($dueDate)) {
                        $daysDifference = $dueDate->diffInDays($today);
                        $deadline = "H-{$daysDifference} delayed";
                    } elseif ($today->isAfter($dueDate)) {
                        $daysDifference = $today->diffInDays($dueDate);
                        $deadline = "H+{$daysDifference} delayed";
                    } else {
                        $deadline = "Delivery Now";
                    }
                    if ($data->status == 'Closed') {
                        $deadline = "-";
                    }
                    if ($data->status <> 'Request') {
                        return '<span style="font-size: small;width: 100%"><b>Due Date: </b>' . $data->due_date . '<br><span class="text-primary"><b>Qty: </b>' . $qty  . ' ' . '</span><br><span class="text-info"><b>Delivered Qty: </b>' . $delivery_qty . '</span><br><span class="text-dark"><b>Outstanding Qty: </b>' . $outstanding_qty . '</span><br><span class="text-danger"><b>Cancel Qty: </b>' . $cancel_qty . '</span><br><b>Deadline: ' . $deadline . '</b></span>';
                    } else {
                        return '<span style="font-size: small;width: 100%"><b>-</b></span>';
                    }
                })
                ->addColumn('description', function ($data) {
                    return $data->product_code . ' - ' . $data->description;
                })
                ->addColumn('status', function ($data) {
                    $badgeColor = $data->status == 'Request' ? 'secondary' : ($data->status == 'Un Posted' ? 'warning' : ($data->status == 'Closed' ? 'info' : ($data->status == 'Finish' ? 'primary' : 'success')));
                    return '<span class="badge bg-' . $badgeColor . '" style="font-size: smaller;width: 100%">' . $data->status . '</span>';
                })
                ->addColumn('statusLabel', function ($data) {
                    return $data->status;
                })
                ->addColumn('wo_list', function ($data) {
                    $woList = $data->status == 'Request' || $data->status == 'Un Posted' ? 'Please Wait SO Posted' : 'WO&nbspList';
                    $woHref = $data->status == 'Request' || $data->status == 'Un Posted' ? '#' : route('marketing.salesOrder.generateWO', encrypt($data->so_number));
                    return '<a href="' . $woHref . '" class="btn btn-danger btn-sm waves-effect waves-light" style="font-size: smaller;width: 100%">' . $woList . '</a>';
                })
                ->rawColumns(['bulk-action', 'progress', 'status', 'statusLabel', 'wo_list'])
                ->make(true);
        }
        return view('marketing.sales_order.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // $kodeOtomatis = $this->generateCode();
        $orderPO = $this->getAllOrder_PO();
        $customers = $this->getAllCustomers();
        $salesmans = $this->getAllSalesman();
        $termPayments = $this->getAllTermPayment();
        $units = $this->getAllUnit();

        // dd($orderPO);

        return view('marketing.sales_order.create', compact('orderPO', 'customers', 'salesmans', 'termPayments', 'units'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());

        if ($request->input('selected_rows')) {
            // Mendapatkan indeks baris yang terceklis
            $selectedRows = $request->input('selected_rows', []);

            // Mendapatkan data dari baris yang terceklis
            foreach ($selectedRows as $index) {
                $data_product = [
                    'type_product' => $request->input("type_product.$index"),
                    'id_master_products' => $request->input("id_master_products.$index"),
                    'cust_product_code' => $request->input("cust_product_code.$index"),
                    'qty' => $request->input("qty.$index"),
                    'id_master_units' => $request->input("id_master_units.$index"),
                    'price' => $request->input("price.$index"),
                ];
            }
        } else {
            $data_product = [
                'type_product' => $request->type_product,
                'id_master_products' => $request->id_master_products,
                'cust_product_code' => $request->cust_product_code,
                'qty' => $request->qty,
                'id_master_units' => $request->id_master_units,
                'price' => $request->price,
            ];
        }

        DB::beginTransaction();
        try {
            // Simpan data ke dalam tabel sales_orders
            $sales_order = salesOrder::create([
                'id_order_confirmations' => $request->id_order_confirmations,
                'so_number' => $request->so_number,
                'date' => $request->date,
                // 'transaction_type' => $request->transaction_type,
                'so_type' => $request->so_type,
                'so_category' => $request->so_category,
                'type_product' => $data_product['type_product'],
                'id_master_products' => $data_product['id_master_products'],
                'cust_product_code' => $data_product['cust_product_code'],
                'qty' => $data_product['qty'],
                'id_master_units' => $data_product['id_master_units'],
                'price' => $data_product['price'],
                'total_price' => $request->total_price,
                'due_date' => $request->due_date,
                // 'outstanding_delivery_qty' => $request->outstanding_delivery_qty,
                'id_master_customers' => $request->id_master_customers,
                'id_master_customer_addresses' => $request->id_master_customer_addresses,
                'id_master_salesmen' => $request->id_master_salesmen,
                'reference_number' => $request->reference_number,
                'ppn' => $request->ppn,
                'color' => $request->color,
                'non_invoiceable' => $request->non_invoiceable,
                // 'perforasi' => $request->perforasi,
                'status' => $request->status,
                'id_master_term_payments' => $request->id_master_term_payments,
                // 'id_master_currencies' => $request->id_master_currencies,
                'remarks' => $request->remark,
                // 'approval_by' => $request->approval_by,
                // 'unit_rate' => $request->unit_rate,
                // 'entry_currency' => $request->entry_currency,
                // 'exchange_rate' => $request->exchange_rate,
                // 'posting_currency' => $request->posting_currency,
                // Sesuaikan dengan kolom-kolom lain yang ada pada tabel order_confirmation
            ]);

            // // Mendapatkan indeks baris yang terceklis
            // $selectedRows = $request->input('selected_rows', []);

            // // Mendapatkan data dari baris yang terceklis
            // foreach ($selectedRows as $index) {
            //     // Simpan data ke dalam tabel sales_order_details
            //     salesOrderDetail::create([
            //         'id_sales_orders' => $request->so_number,
            //         'type_product' => $request->input("type_product.$index"),
            //         'id_master_products' => $request->input("id_master_products.$index"),
            //         'cust_product_code' => $request->input("cust_product_code.$index"),
            //         'qty' => $request->input("qty.$index"),
            //         'id_master_units' => $request->input("id_master_units.$index"),
            //         'price' => $request->input("price.$index"),
            //         'subtotal' => $request->input("subtotal.$index"),
            //         'due_date' => $request->due_date,
            //         // 'outstanding_delivery_qty' => $request->outstanding_delivery_qty,
            //         // Sesuaikan dengan kolom-kolom lain yang ada pada tabel order_confirmation_detail
            //     ]);
            // }

            $this->saveLogs('Adding New Sales Order : ' . $request->so_number);

            DB::commit();

            if ($request->has('save_add_more')) {
                return redirect()->route('marketing.salesOrder.create')->with(['success' => 'Success Create New Sales Order ' . $request->so_number]);
            } else {
                return redirect()->route('marketing.salesOrder.index')->with(['success' => 'Success Create New Sales Order ' . $request->so_number]);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with(['fail' => $e . 'Failed to Create New Sales Order! ' . $request->so_number]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(salesOrder $salesOrder, $encryptedSONumber)
    {
        // Dekripsi data
        $so_number = Crypt::decrypt($encryptedSONumber);
        $sales_order = salesOrder::with('masterSalesman', 'masterCustomer', 'masterTermPAyment', 'masterUnit')
            ->where('so_number', $so_number)
            ->first();
        $customer_addresses = MstCustomersAddress::where('id_master_customers', $sales_order->id_master_customers)->where('id', $sales_order->id_master_customer_addresses)->first();

        $typeProduct = $sales_order->type_product;
        $idProduct = $sales_order->id_master_products;
        if ($typeProduct == 'WIP') {
            $product = DB::table('master_wips as a')
                ->select('a.id', 'a.description', 'a.id_master_units')
                // ->join('master_units as b', 'a.id_master_units', '=', 'b.id')
                ->where('a.id', $idProduct)
                ->first();
        } else if ($typeProduct == 'FG') {
            $product = DB::table('master_product_fgs as a')
                ->select('a.id', 'a.description', 'a.id_master_units', 'a.sales_price as price')
                // ->join('master_units as b', 'a.id_master_units', '=', 'b.id')
                ->where('a.id', $idProduct)
                ->first();
        }

        // echo json_encode($sales_order);
        // exit;
        return view('marketing.sales_order.show', compact('sales_order', 'customer_addresses', 'product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(salesOrder $salesOrder, $encryptedSONumber)
    {
        // Dekripsi data
        $SONumber = Crypt::decrypt($encryptedSONumber);

        $orderPO = $this->getAllOrder_PO();
        $customers = $this->getAllCustomers();
        $salesmans = $this->getAllSalesman();
        $termPayments = $this->getAllTermPayment();
        $units = $this->getAllUnit();

        $salesOrder = salesOrder::with('salesOrderDetails')
            ->where('so_number', $SONumber)
            ->first();

        $customer_addresses = MstCustomersAddress::where('id_master_customers', $salesOrder->id_master_customers)->get();

        // dd($customerAddresses);
        // echo json_encode($orderPO);exit;

        return view('marketing.sales_order.edit', compact('orderPO', 'customers', 'customer_addresses', 'salesmans', 'termPayments', 'salesOrder', 'units'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, salesOrder $salesOrder)
    {
        // dd(count($request->all()));
        // dd($request->all());

        if ($request->input('selected_rows')) {
            // Mendapatkan indeks baris yang terceklis
            $selectedRows = $request->input('selected_rows', []);

            // Mendapatkan data dari baris yang terceklis
            foreach ($selectedRows as $index) {
                $data_product = [
                    'type_product' => $request->input("type_product.$index"),
                    'id_master_products' => $request->input("id_master_products.$index"),
                    'cust_product_code' => $request->input("cust_product_code.$index"),
                    'qty' => $request->input("qty.$index"),
                    'id_master_units' => $request->input("id_master_units.$index"),
                    'price' => $request->input("price.$index"),
                ];
            }
        } else if ($request->input('id_order_confirmation') == '') {
            $data_product = [
                'type_product' => $request->type_product,
                'id_master_products' => $request->id_master_products,
                'cust_product_code' => $request->cust_product_code,
                'qty' => $request->qty,
                'id_master_units' => $request->id_master_units,
                'price' => $request->price,
            ];
        }

        DB::beginTransaction();
        try {
            // Simpan data ke dalam tabel sales_orders
            $sales_order = salesOrder::where('so_number', $request->so_number)->first();
            // echo json_encode($sales_order);exit;
            if ($request->input('selected_rows') || $request->input('id_order_confirmation') == '') {
                $sales_order->update([
                    'id_order_confirmations' => $request->id_order_confirmations,
                    // 'so_number' => $request->so_number,
                    'date' => $request->date,
                    // 'transaction_type' => $request->transaction_type,
                    // 'so_type' => $request->so_type,
                    'so_category' => $request->so_category,
                    'type_product' => $data_product['type_product'],
                    'id_master_products' => $data_product['id_master_products'],
                    'cust_product_code' => $data_product['cust_product_code'],
                    'qty' => $data_product['qty'],
                    'id_master_units' => $data_product['id_master_units'],
                    'price' => $data_product['price'],
                    'total_price' => $request->total_price,
                    'due_date' => $request->due_date,
                    // 'outstanding_delivery_qty' => $request->outstanding_delivery_qty,
                    'id_master_customers' => $request->id_master_customers,
                    'id_master_customer_addresses' => $request->id_master_customer_addresses,
                    'id_master_salesmen' => $request->id_master_salesmen,
                    'reference_number' => $request->reference_number,
                    'ppn' => $request->ppn,
                    'color' => $request->color,
                    'non_invoiceable' => $request->non_invoiceable,
                    // 'perforasi' => $request->perforasi,
                    'status' => $request->status,
                    'id_master_term_payments' => $request->id_master_term_payments,
                    // 'id_master_currencies' => $request->id_master_currencies,
                    'remarks' => $request->remark,
                    // 'approval_by' => $request->approval_by,
                    // 'unit_rate' => $request->unit_rate,
                    // 'entry_currency' => $request->entry_currency,
                    // 'exchange_rate' => $request->exchange_rate,
                    // 'posting_currency' => $request->posting_currency,
                    // Sesuaikan dengan kolom-kolom lain yang ada pada tabel order_confirmation
                ]);
            } else {
                $sales_order->update([
                    'id_order_confirmations' => $request->id_order_confirmations,
                    // 'so_number' => $request->so_number,
                    'date' => $request->date,
                    // 'transaction_type' => $request->transaction_type,
                    // 'so_type' => $request->so_type,
                    'so_category' => $request->so_category,
                    'type_product' => $sales_order->type_product,
                    'id_master_products' => $sales_order->id_master_products,
                    'cust_product_code' => $sales_order->cust_product_code,
                    'qty' => $sales_order->qty,
                    'id_master_units' => $sales_order->id_master_units,
                    'price' => $sales_order->price,
                    'total_price' => $request->total_price,
                    'due_date' => $request->due_date,
                    // 'outstanding_delivery_qty' => $request->outstanding_delivery_qty,
                    'id_master_customers' => $request->id_master_customers,
                    'id_master_customer_addresses' => $request->id_master_customer_addresses,
                    'id_master_salesmen' => $request->id_master_salesmen,
                    'reference_number' => $request->reference_number,
                    'ppn' => $request->ppn,
                    'color' => $request->color,
                    'non_invoiceable' => $request->non_invoiceable,
                    // 'perforasi' => $request->perforasi,
                    'status' => $request->status,
                    'id_master_term_payments' => $request->id_master_term_payments,
                    // 'id_master_currencies' => $request->id_master_currencies,
                    'remarks' => $request->remark,
                    // 'approval_by' => $request->approval_by,
                    // 'unit_rate' => $request->unit_rate,
                    // 'entry_currency' => $request->entry_currency,
                    // 'exchange_rate' => $request->exchange_rate,
                    // 'posting_currency' => $request->posting_currency,
                    // Sesuaikan dengan kolom-kolom lain yang ada pada tabel order_confirmation
                ]);
            }

            $this->saveLogs('Edit Sales Order : ' . $request->so_number);

            DB::commit();

            if ($request->has('save_add_more')) {
                return redirect()->back()->with(['success' => 'Success Update Sales Order ' . $request->so_number]);
            } else {
                return redirect()->route('marketing.salesOrder.index')->with(['success' => 'Success Update Sales Order ' . $request->so_number]);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with(['fail' => $e . 'Failed to Update Sales Order! ' . $request->so_number]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(salesOrder $salesOrder)
    {
        //
    }

    public function getAllOrder_PO()
    {
        // $combinedDataOrder_PO = DB::table('input_po_customer')
        //     ->select('po_number as order')
        //     ->where('status', 'Posted')
        //     ->unionAll(
        //         DB::table('order_confirmations')
        //             ->select('oc_number as order')
        //             ->where('status', 'Posted')
        //     )
        //     ->get();
        $combinedDataOrder_PO = DB::table('order_confirmations')
            ->select('oc_number as order')
            ->where('status', 'Posted')
            ->get();

        foreach ($combinedDataOrder_PO as $key => $value) {
            // Ambil detail produk dari sales_order_detail
            $salesOrderDetails = DB::table('sales_orders as a')
                // ->join('sales_order_details as b', 'a.so_number', '=', 'b.id_sales_orders')
                // ->select('a.id_order_confirmations', 'a.so_number', 'b.id_sales_orders', 'b.type_product', 'b.id_master_products')
                ->select('a.id_order_confirmations', 'a.so_number', 'a.type_product', 'a.id_master_products')
                ->where('a.id_order_confirmations', $value->order)
                ->count();

            if (substr($value->order, 0, 2) == 'PO') {
                // Ambil detail produk dari input_po_customer_detail
                $orders = DB::table('input_po_customer_details')
                    ->where('po_number', $value->order)
                    ->count();
            } else if (substr($value->order, 0, 2) == 'KO') {
                // Ambil detail produk dari order_confirmation_detail
                $orders = DB::table('order_confirmation_details')
                    ->where('oc_number', $value->order)
                    ->count();
            }
            // Tambahkan properti baru ke $combinedDataOrder_PO
            $value->sales_order_details = $salesOrderDetails;
            $value->all_product_details = $orders;

            // Skip jika selisih sama dengan 0
            if (($value->all_product_details - $value->sales_order_details) === 0) {
                unset($combinedDataOrder_PO[$key]);
            }
        }
        // Reset index array setelah unset
        // $combinedDataOrder_PO = array_values($combinedDataOrder_PO);

        // return response()->json(['customers' => $customers]);
        return $combinedDataOrder_PO;
    }

    public function getAllCustomers()
    {
        $customers = MstCustomers::select('id', 'customer_code', 'name')
            ->where('status', 'active')
            ->orderBy('customer_code', 'asc')
            ->get();
        // return response()->json(['customers' => $customers]);
        return $customers;
    }

    public function getAllSalesman()
    {
        $salesmans = MstSalesmans::select('id', 'name')
            ->where('is_active', 1)
            ->orderBy('name', 'asc')
            ->get();

        return $salesmans;
    }

    public function getAllTermPayment()
    {
        $termPayments = MstTermPayments::select('id', 'term_payment')
            ->where('is_active', 1)
            ->orderBy('term_payment', 'asc')
            ->get();

        return $termPayments;
    }

    public function getAllUnit()
    {
        $units = MstUnits::select('*')
            ->where('is_active', 1)
            ->orderBy('unit', 'asc')
            ->get();

        return $units;
    }

    public function getOrderDetail()
    {
        $order_number = request()->get('order_number');
        $order = '';
        if ($order_number != '') {
            if (substr($order_number, 0, 2) == 'PO') {
                $order = InputPOCust::with('inputPOCustomerDetails', 'inputPOCustomerDetails.masterUnit', 'masterCustomerAddress')->where('po_number', $order_number)->first();
            } else if (substr($order_number, 0, 2) == 'KO') {
                $order = orderConfirmation::with('orderConfirmationDetails', 'orderConfirmationDetails.masterUnit', 'masterCustomerAddress')->where('oc_number', $order_number)->first();
            }
        }
        $customers = $this->getAllCustomers();
        $salesmans = $this->getAllSalesman();
        $termPayments = $this->getAllTermPayment();
        $combinedDataProducts = DB::table('master_product_fgs')
            ->select('id', 'product_code', 'description', 'id_master_units', DB::raw("'FG' as type_product"))
            ->where('status', 'Active')
            ->unionAll(
                DB::table('master_wips')
                    ->select('id', 'wip_code as product_code', 'description', 'id_master_units', DB::raw("'WIP' as type_product"))
                    ->where('status', 'Active')
            )
            ->get();

        $compareData = $this->compareDetails($order_number);

        return response()->json(['order' => $order, 'customers' => $customers, 'salesmans' => $salesmans, 'termPayments' => $termPayments, 'products' => $combinedDataProducts, 'compare' => $compareData]);
    }

    public function getCustomerDetail()
    {
        $idCustomer = request()->get('idCustomer');
        $customer = '';
        if ($idCustomer != '') {
            $customer = MstCustomers::where('id', $idCustomer)->first();
        }
        $salesmans = $this->getAllSalesman();
        $termPayments = $this->getAllTermPayment();
        $customer_addresses = MstCustomersAddress::where('id_master_customers', $idCustomer)->get();

        return response()->json(['customer' => $customer, 'salesmans' => $salesmans, 'termPayments' => $termPayments, 'customer_addresses' => $customer_addresses]);
    }

    public function generateSONumber()
    {
        $so_type = request()->get('so_type');
        $prefix = 'SO'; // Prefix yang diinginkan
        // $currentMonthYear = now()->format('ymd'); // Format tahun dan bulan saat ini
        $currentYear = now()->format('y'); // Format tahun saat ini
        $suffixLength = 6; // Panjang angka di bagian belakang

        $suffix = $so_type == 'Reguler' ? 'RG' : ($so_type == 'Sample' ? 'SP' : ($so_type == 'Raw Material' ? 'RM' : ($so_type == 'Machine' ? 'MC' : ($so_type == 'Stock' ? 'ST' : ''))));

        $latestCode = salesOrder::where('so_number', 'like', "{$prefix}{$currentYear}%")
            ->whereRaw("right(`so_number`, 2) = '{$suffix}'")
            ->orderBy('so_number', 'desc')
            ->value('so_number');

        $lastNumber = $latestCode ? intval(substr($latestCode, -1 * $suffixLength)) : 0;

        $newNumber = $lastNumber + 1;

        $newCode = $prefix . $currentYear . str_pad($newNumber, $suffixLength, '0', STR_PAD_LEFT) . $suffix;

        // Gunakan $newCode sesuai kebutuhan Anda
        return response()->json(['code' => $newCode]);
    }

    public function getDataProduct()
    {
        $typeProduct = request()->get('typeProduct');
        if ($typeProduct == 'WIP') {
            $products = DB::table('master_wips as a')
                ->where('a.status', 'Active')
                ->select('a.id', 'a.wip_code', 'a.description')
                ->get();
        } else if ($typeProduct == 'FG') {
            $products = DB::table('master_product_fgs as a')
                ->where('a.status', 'Active')
                ->select('a.id', 'a.product_code', 'a.description')
                ->get();
        }
        return response()->json(['products' => $products]);
    }

    public function getProductDetail()
    {
        $typeProduct = request()->get('typeProduct');
        $idProduct = request()->get('idProduct');
        if ($typeProduct == 'WIP') {
            $product = DB::table('master_wips as a')
                ->select('a.id', 'a.description', 'a.id_master_units')
                // ->join('master_units as b', 'a.id_master_units', '=', 'b.id')
                ->where('a.id', $idProduct)
                ->first();
        } else if ($typeProduct == 'FG') {
            $product = DB::table('master_product_fgs as a')
                ->select('a.id', 'a.description', 'a.id_master_units', 'a.sales_price as price')
                // ->join('master_units as b', 'a.id_master_units', '=', 'b.id')
                ->where('a.id', $idProduct)
                ->first();
        }
        return response()->json(['product' => $product]);
    }

    public function compareDetails($idOrderConfirmation)
    {
        // Ambil detail produk dari sales_order_detail
        $salesOrderDetails = DB::table('sales_orders as a')
            // ->join('sales_order_details as b', 'a.so_number', '=', 'b.id_sales_orders')
            // ->select('a.id_order_confirmations', 'a.so_number', 'b.id_sales_orders', 'b.type_product', 'b.id_master_products')
            ->select('a.id_order_confirmations', 'a.so_number', 'a.type_product', 'a.id_master_products')
            ->where('a.id_order_confirmations', $idOrderConfirmation)
            ->get();

        return $salesOrderDetails;
    }

    public function getDataSalesOrder()
    {
        $so_number = request()->get('so_number');
        $sales_order = salesOrder::with('orderConfirmationDetails', 'orderConfirmationDetails.masterUnit')
            ->where('so_number', $so_number)
            ->first();

        $customer_addresses = '';
        $detail_product = '';
        $order_number = $sales_order->id_order_confirmations;
        if ($order_number != null || $order_number != '') {
            if (substr($order_number, 0, 2) == 'PO') {
                $customer_addresses = InputPOCust::with('inputPOCustomerDetails', 'inputPOCustomerDetails.masterUnit', 'masterCustomerAddress')->where('po_number', $order_number)->first();
            } else if (substr($order_number, 0, 2) == 'KO') {
                $customer_addresses = orderConfirmation::with('orderConfirmationDetails', 'orderConfirmationDetails.masterUnit', 'masterCustomerAddress')->where('oc_number', $order_number)->first();
            }
        } else {
            $customer_addresses = salesOrder::with('masterCustomerAddress')->where('so_number', $so_number)->first();
            $typeProduct = $sales_order->type_product;
            $idProduct = $sales_order->id_master_products;
            if ($typeProduct == 'WIP') {
                $detail_product = DB::table('master_wips as a')
                    ->select('a.id', 'a.description', 'a.id_master_units')
                    // ->join('master_units as b', 'a.id_master_units', '=', 'b.id')
                    ->where('a.id', $idProduct)
                    ->first();
            } else if ($typeProduct == 'FG') {
                $detail_product = DB::table('master_product_fgs as a')
                    ->select('a.id', 'a.description', 'a.id_master_units', 'a.sales_price as price')
                    // ->join('master_units as b', 'a.id_master_units', '=', 'b.id')
                    ->where('a.id', $idProduct)
                    ->first();
            }
        }

        $combinedDataProducts = DB::table('master_product_fgs')
            ->select('id', 'product_code', 'description', 'id_master_units', DB::raw("'FG' as type_product"))
            ->where('status', 'Active')
            ->unionAll(
                DB::table('master_wips')
                    ->select('id', 'wip_code as product_code', 'description', 'id_master_units', DB::raw("'WIP' as type_product"))
                    ->where('status', 'Active')
            )
            ->get();

        $compareData = $this->compareDetails($sales_order->id_order_confirmations);

        return response()->json(['order' => $sales_order, 'products' => $combinedDataProducts, 'compare' => $compareData, 'customer_addresses' => $customer_addresses, 'detail_product' => $detail_product]);
    }

    public function bulkPosted(Request $request)
    {
        $so_numbers = $request->input('so_numbers');

        DB::beginTransaction();
        try {
            // Lakukan logika untuk melakukan bulk update status di sini

            // Contoh: Update status menjadi 'Posted'
            salesOrder::whereIn('so_number', $so_numbers)
                ->update(['status' => 'Posted', 'updated_at' => now()]);

            DB::commit();
            $this->saveLogs('Changed Sales Order ' . implode(', ', $so_numbers) . ' to posted');

            return response()->json(['message' => 'Change to posted successful', 'type' => 'success'], 200);
        } catch (\Exception $e) {
            // Tangani kesalahan jika diperlukan
            return response()->json(['error' => 'Error updating to posted', 'type' => 'error'], 500);
        }
    }

    public function bulkUnPosted(Request $request)
    {
        $so_numbers = $request->input('so_numbers');

        DB::beginTransaction();
        try {
            // Lakukan logika untuk melakukan bulk update status di sini

            // Contoh: Update status menjadi 'Posted'
            salesOrder::whereIn('so_number', $so_numbers)
                ->update(['status' => 'Un Posted', 'updated_at' => now()]);

            $this->saveLogs('Changed Sales Order ' . implode(', ', $so_numbers) . ' to unposted');

            DB::commit();
            return response()->json(['message' => 'Change to unposted successful', 'type' => 'success'], 200);
        } catch (\Exception $e) {
            // Tangani kesalahan jika diperlukan
            return response()->json(['error' => 'Error updating to unposted', 'type' => 'error'], 500);
        }
    }

    public function bulkDeleted(Request $request)
    {
        $so_numbers = $request->input('so_numbers');

        try {
            // Hapus data POCustomer sesuai so_number
            salesOrder::whereIn('so_number', $so_numbers)->delete();

            $this->saveLogs('Deleted Sales Order ' . implode(', ', $so_numbers));

            return response()->json(['message' => 'Successfully deleted data', 'type' => 'success'], 200);
        } catch (\Exception $e) {
            // Tangani kesalahan jika diperlukan
            return response()->json(['error' => 'Failed to delete data', 'type' => 'error'], 500);
        }
    }

    public function print($encryptedSONumber)
    {
        $so_number = Crypt::decrypt($encryptedSONumber);
        $salesOrder = salesOrder::with('masterSalesman', 'masterCustomer')
            ->where('so_number', $so_number)
            ->first();

        $typeProduct = $salesOrder->type_product;
        $idProduct = $salesOrder->id_master_products;
        if ($typeProduct == 'WIP') {
            $product = DB::table('master_wips as a')
                ->select('a.id', 'a.description', 'a.id_master_units', 'b.unit_code')
                ->join('master_units as b', 'a.id_master_units', '=', 'b.id')
                ->where('a.id', $idProduct)
                ->first();
        } else if ($typeProduct == 'FG') {
            $product = DB::table('master_product_fgs as a')
                ->select('a.id', 'a.description', 'a.id_master_units', 'a.sales_price as price', 'b.unit_code')
                ->join('master_units as b', 'a.id_master_units', '=', 'b.id')
                ->where('a.id', $idProduct)
                ->first();
        }
        // echo json_encode($product);
        // exit;
        return view('marketing.sales_order.print', compact('salesOrder', 'product'));
    }

    public function generateWO($encryptedSONumber)
    {
        $so_number = Crypt::decrypt($encryptedSONumber);
        $salesOrder = salesOrder::with('masterSalesman', 'masterCustomer')
            ->where('so_number', $so_number)
            ->first();

        $typeProduct = $salesOrder->type_product;
        $idProduct = $salesOrder->id_master_products;
        if ($typeProduct == 'WIP') {
            $product = DB::table('master_wips as a')
                ->select('a.*', 'b.group_sub_code', 'b.name')
                ->join('master_group_subs as b', 'a.id_master_group_subs', '=', 'b.id')
                ->where('a.id', $idProduct)
                ->first();

            $product_ref = DB::table('master_wip_ref_wips as a')
                ->select('a.*')
                ->where('a.id_master_wips', $idProduct)
                ->first();

            $product_ref_rm = DB::table('master_wip_refs as a')
                ->select('a.*', 'b.id_master_units')
                ->join('master_raw_materials as b', 'a.id_master_raw_materials', '=', 'b.id')
                ->where('a.id_master_wips', $idProduct)
                ->get();

            // $data['type_product_material'] = 'WIP';
            $data['type_product_material'] = $product_ref->id_master_wips_material == '0' ? null : 'WIP';
            // $data['id_master_products_material'] = $product_ref->id_master_wips_material;
            $data['id_master_products_material'] = $product_ref->id_master_wips_material == '0' ? null : $product_ref->id_master_wips_material;
            // $data['qty_needed'] = $product_ref->id_master_wips_material;
            $data['id_master_units_needed'] = $product_ref->master_units_id;
            $data['qty_results'] = $product_ref->qty_results;
        } else if ($typeProduct == 'FG') {
            $product = DB::table('master_product_fgs as a')
                ->select('a.*', 'b.group_sub_code', 'b.name', 'c.id as id_master_process_productions')
                ->join('master_group_subs as b', 'a.id_master_group_subs', '=', 'b.id')
                ->join('master_process_productions as c', 'b.group_sub_code', '=', 'c.process_code')
                ->where('a.id', $idProduct)
                ->first();

            $product_ref = DB::table('master_product_fg_refs as a')
                ->select('a.*')
                ->where('a.id_master_product_fgs', $idProduct)
                ->first();

            $data['type_product_material'] = $product_ref->type_ref;
            // $data['qty_needed'] = $product_ref->id_master_wips_material;
            $data['id_master_units_needed'] = $product_ref->master_units_id;
            if ($product_ref->type_ref == 'WIP') {
                $data['id_master_products_material'] = $product_ref->id_master_wips;
            } else if ($product_ref->type_ref == 'FG') {
                $data['id_master_products_material'] = $product_ref->id_master_fgs;
            } else {
                $data['id_master_products_material'] = null;
            }
            $data['qty_results'] = $product_ref->qty_results;
        }

        $woCheck = DB::table('work_orders as a')
            ->select('a.*')
            ->where('a.id_sales_orders', $salesOrder->id)
            ->get();

        if ($data['qty_results'] <> null) {
            $qty_needed = $salesOrder->qty / $data['qty_results'];
        } else {
            $qty_needed = null;
        }
        // echo json_encode($salesOrder->qty / $data['qty_results']);
        // exit;

        if (count($woCheck) == 0) {
            $wo_number = $this->generateWONumber($product->group_sub_code);

            DB::beginTransaction();
            try {
                $data = [
                    'id_sales_orders' => $salesOrder->id,
                    'wo_number' => $wo_number,
                    'id_master_process_productions' => $product->id_master_process_productions,
                    // 'id_master_work_centers' => $salesOrder->id,
                    'type_product' => $salesOrder->type_product,
                    'id_master_products' => $salesOrder->id_master_products,
                    'type_product_material' => $data['type_product_material'],
                    'id_master_products_material' => $data['id_master_products_material'],
                    'qty' => $salesOrder->qty,
                    'id_master_units' => $salesOrder->id_master_units,
                    // 'qty_results' => $salesOrder->id,
                    'qty_needed' => $qty_needed,
                    'id_master_units_needed' => $data['id_master_units_needed'],
                    'start_date' => date('Y-m-d'),
                    'finish_date' => date('Y-m-d', strtotime('+7 days')),
                    'status' => 'Request',
                    // 'note' => $salesOrder->id,
                    // 'waste' => $salesOrder->id,
                    // 'from_stock' => $salesOrder->id,
                ];

                $work_order = workOrder::create($data);

                if (isset($product_ref_rm) && count($product_ref_rm) > 0) {
                    $id_work_order = workOrder::where('wo_number', $wo_number)->first();

                    // Simpan data
                    foreach ($product_ref_rm as $product_rm) {
                        workOrderDetail::create([
                            'id_work_orders' => $id_work_order->id,
                            'type_product' => 'RM',
                            'id_master_products' => $product_rm->id_master_raw_materials,
                            'qty' => $product_rm->weight,
                            'id_master_units' => $product_rm->id_master_units,
                        ]);
                    }
                }

                $this->saveLogs('Adding New Work Order : ' . $wo_number);

                DB::commit();
                // return redirect()->route('marketing.Order.index')->with(['success' => 'Success Create New Sales Order ' . $request->so_number]);
            } catch (\Exception $e) {
                DB::rollback();
                // return redirect()->back()->with(['fail' => $e . 'Failed to Create New Sales Order! ' . $request->so_number]);
            }
        } else {
            $wo_number = $woCheck[0]->wo_number;
        }

        // echo json_encode($data);
        // exit;
        return redirect()->route('ppic.workOrder.viewWO', encrypt($so_number));
    }

    public function generateWONumber($proccessProduction)
    {
        $prefix = 'WO' . $proccessProduction; // Prefix yang diinginkan
        $suffixLength = 5; // Panjang angka di bagian belakang

        $latestCode = WorkOrder::orderBy(DB::raw("RIGHT(wo_number, 5)"), 'desc')
            ->value('wo_number');

        $lastNumber = $latestCode ? intval(substr($latestCode, -1 * $suffixLength)) : 0;

        $newNumber = $lastNumber + 1;

        $newCode = $prefix . str_pad($newNumber, $suffixLength, '0', STR_PAD_LEFT);

        // Gunakan $newCode sesuai kebutuhan Anda
        return $newCode;
    }

    public function showWO(workOrder $workOrder, $encryptedSONumber)
    {
        // Dekripsi data
        $so_number = Crypt::decrypt($encryptedSONumber);
        $sales_order = DB::table('sales_orders as a')
            ->select('a.*')
            ->where('a.so_number', $so_number)
            ->first();

        $list_wo = workOrder::with('masterUnit', 'masterUnitNeeded', 'masterProcessProduction', 'masterWorkCenter')
            ->where('id_sales_orders', function ($query) use ($so_number) {
                $query->select('id')
                    ->from('sales_orders')
                    ->where('so_number', $so_number);
            })
            ->join(
                \DB::raw(
                    '(SELECT id as id_prod, product_code, description, id_master_units as unit, \'FG\' as t_product FROM master_product_fgs WHERE status = \'Active\' UNION ALL SELECT id as id_prod, wip_code as product_code, description, id_master_units as unit, \'WIP\' as t_product FROM master_wips WHERE status = \'Active\') b'
                ),
                function ($join) {
                    $join->on('work_orders.id_master_products', '=', 'b.id_prod');
                    $join->on('work_orders.type_product', '=', 'b.t_product');
                }
            )
            ->leftJoin(
                \DB::raw(
                    '(SELECT id as id_prod_needed, product_code as pc_needed, description as desc_needed, id_master_units as unit_needed, \'FG\' as t_product_needed FROM master_product_fgs WHERE status = \'Active\' UNION ALL SELECT id as id_prod_needed, wip_code as pc_needed, description as desc_needed, id_master_units as unit_needed, \'WIP\' as t_product_needed FROM master_wips WHERE status = \'Active\') c'
                ),
                function ($join) {
                    $join->on('work_orders.id_master_products_material', '=', 'c.id_prod_needed');
                    $join->on('work_orders.type_product_material', '=', 'c.t_product_needed');
                }
            )
            ->get();

        // echo json_encode($list_wo);
        // exit;
        return view('marketing.work_order.list', compact('so_number', 'sales_order', 'list_wo'));
    }

    public function cancelQty(Request $request)
    {
        $so_number = $request->input('so_number');
        $qty = $request->input('qty');
        $cancel_qty = $request->input('cancel_qty');

        DB::beginTransaction();
        try {
            // Lakukan logika untuk melakukan cancel qty di sini

            // Contoh: Update status menjadi 'Posted'
            salesOrder::where('so_number', $so_number)
                ->update(['outstanding_delivery_qty' => $cancel_qty, 'updated_at' => now()]);

            DB::commit();
            $this->saveLogs('Cancel Qty in ' . $so_number . '. Qty cancel : ' . $cancel_qty);

            return response()->json(['message' => 'Cancel qty successful', 'type' => 'success'], 200);
        } catch (\Exception $e) {
            // Tangani kesalahan jika diperlukan
            return response()->json(['error' => 'Error cancel qty', 'type' => 'error'], 500);
        }
    }
}
