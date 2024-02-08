<?php

namespace App\Http\Controllers\Marketing;

use Browser;
use DataTables;
use App\Models\MstCustomers;
use App\Models\MstSalesmans;
use Illuminate\Http\Request;
use App\Traits\AuditLogsTrait;
use App\Models\MstTermPayments;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Marketing\salesOrder;
use App\Models\Marketing\InputPOCust;
use App\Models\Marketing\orderConfirmation;
use App\Models\marketing\salesOrderDetail;

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
            $columns = ['', '', 'so_number', 'date', 'customer', 'salesman', 'due_date', 'reference_number', 'status', ''];

            // Query dasar
            $query = DB::table('sales_orders as a')
                ->join('master_customers as b', 'a.id_master_customers', '=', 'b.id')
                ->join('master_salesmen as c', 'a.id_master_salesmen', '=', 'c.id')
                ->select('a.id', 'a.so_number', 'a.date', 'b.name as customer', 'c.name as salesman', 'a.due_date', 'a.reference_number', 'a.status')
                ->orderBy($columns[$orderColumn], $orderDirection);

            // Handle pencarian
            if ($request->has('search') && $request->input('search')) {
                $searchValue = $request->input('search');
                $query->where(function ($query) use ($searchValue) {
                    $query->where('a.so_number', 'like', '%' . $searchValue . '%')
                        ->orWhere('a.date', 'like', '%' . $searchValue . '%')
                        ->orWhere('b.name', 'like', '%' . $searchValue . '%')
                        ->orWhere('c.name', 'like', '%' . $searchValue . '%')
                        ->orWhere('a.due_date', 'like', '%' . $searchValue . '%')
                        ->orWhere('a.reference_number', 'like', '%' . $searchValue . '%')
                        ->orWhere('a.status', 'like', '%' . $searchValue . '%');
                });
            }

            return DataTables::of($query)
                ->addColumn('action', function ($data) {
                    return view('marketing.sales_order.action', compact('data'));
                })
                ->addColumn('bulk-action', function ($data) {
                    $checkBox = $data->status == 'Request' ? '<input type="checkbox" name="checkbox" data-po-number="' . $data->po_number . '" />' : '';
                    return $checkBox;
                })
                ->addColumn('status', function ($data) {
                    $badgeColor = $data->status == 'Request' ? 'info' : 'success';
                    return '<span class="badge bg-' . $badgeColor . '" style="font-size: smaller;width: 100%">' . $data->status . '</span>';
                })
                ->rawColumns(['bulk-action', 'status'])
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

        return view('marketing.sales_order.create', compact('orderPO', 'customers', 'salesmans', 'termPayments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());

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
                'remark' => $request->remark,
                // 'approval_by' => $request->approval_by,
                // 'unit_rate' => $request->unit_rate,
                // 'entry_currency' => $request->entry_currency,
                // 'exchange_rate' => $request->exchange_rate,
                // 'posting_currency' => $request->posting_currency,
                // Sesuaikan dengan kolom-kolom lain yang ada pada tabel order_confirmation
            ]);

            // Mendapatkan indeks baris yang terceklis
            $selectedRows = $request->input('selected_rows', []);

            // Mendapatkan data dari baris yang terceklis
            foreach ($selectedRows as $index) {
                // Simpan data ke dalam tabel sales_order_details
                salesOrderDetail::create([
                    'id_sales_orders' => $request->so_number,
                    'type_product' => $request->input("type_product.$index"),
                    'id_master_products' => $request->input("id_master_products.$index"),
                    'cust_product_code' => $request->input("cust_product_code.$index"),
                    'qty' => $request->input("qty.$index"),
                    'id_master_units' => $request->input("id_master_units.$index"),
                    'price' => $request->input("price.$index"),
                    'subtotal' => $request->input("subtotal.$index"),
                    'due_date' => $request->due_date,
                    // 'outstanding_delivery_qty' => $request->outstanding_delivery_qty,
                    // Sesuaikan dengan kolom-kolom lain yang ada pada tabel order_confirmation_detail
                ]);
            }

            $this->saveLogs('Adding New Sales Order : ' . $request->so_number);

            DB::commit();

            if ($request->has('save_add_more')) {
                return redirect()->back()->with(['success' => 'Success Create New Sales Order' . $request->so_number]);
            } else {
                return redirect()->route('marketing.salesOrder.index')->with(['success' => 'Success Create New Sales Order' . $request->so_number]);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with(['fail' => $e . 'Failed to Create New Sales Order! ' . $request->so_number]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(salesOrder $salesOrder)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(salesOrder $salesOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, salesOrder $salesOrder)
    {
        //
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
        $combinedDataOrder_PO = DB::table('input_po_customer')
            ->select('po_number as order')
            ->where('status', 'Posted')
            ->unionAll(
                DB::table('order_confirmations')
                    ->select('oc_number as order')
                    ->where('status', 'Posted')
            )
            ->get();
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

    public function getOrderDetail()
    {
        $order_number = request()->get('order_number');
        if (substr($order_number, 0, 2) == 'PO') {
            $order = InputPOCust::with('inputPOCustomerDetails', 'inputPOCustomerDetails.masterUnit', 'masterCustomerAddress')->where('po_number', $order_number)->first();
        } else if (substr($order_number, 0, 2) == 'KO') {
            $order = orderConfirmation::with('orderConfirmationDetails', 'orderConfirmationDetails.masterUnit', 'masterCustomerAddress')->where('oc_number', $order_number)->first();
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

        return response()->json(['order' => $order, 'customers' => $customers, 'salesmans' => $salesmans, 'termPayments' => $termPayments, 'products' => $combinedDataProducts]);
    }

    public function generateSONumber()
    {
        $so_type = request()->get('so_type');
        $prefix = 'SO'; // Prefix yang diinginkan
        $currentMonthYear = now()->format('ymd'); // Format tahun dan bulan saat ini
        $suffixLength = 4; // Panjang angka di bagian belakang

        $suffix = $so_type == 'Reguler' ? 'RG' : ($so_type == 'Sample' ? 'SP' : ($so_type == 'Raw Material' ? 'RM' : ($so_type == 'Machine' ? 'MC' : ($so_type == 'Stock' ? 'ST' : ''))));

        $latestCode = salesOrder::where('so_number', 'like', "{$prefix}{$currentMonthYear}%")
            ->whereRaw("right(`so_number`, 2) = '{$suffix}'")
            ->orderBy('so_number', 'desc')
            ->value('so_number');

        $lastNumber = $latestCode ? intval(substr($latestCode, -1 * $suffixLength)) : 0;

        $newNumber = $lastNumber + 1;

        $newCode = $prefix . $currentMonthYear . str_pad($newNumber, $suffixLength, '0', STR_PAD_LEFT) . $suffix;

        // Gunakan $newCode sesuai kebutuhan Anda
        return response()->json(['code' => $newCode]);
    }
}
