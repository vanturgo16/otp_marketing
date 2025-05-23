<?php

namespace App\Http\Controllers\Marketing;

use App\Exports\ExportPOCustomer;
use PDF;
use Browser;
use DataTables;
use App\Models\MstUnits;
use App\Models\MstCustomers;
use App\Models\MstSalesmans;
use Illuminate\Http\Request;
use App\Models\MstCurrencies;
use App\Traits\AuditLogsTrait;
use App\Models\MstTermPayments;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Marketing\salesOrder;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Marketing\InputPOCust;
use Illuminate\Support\Facades\Crypt;
use App\Models\Marketing\InputPOCustDetail;
use App\Models\Marketing\orderConfirmation;
use App\Models\Marketing\orderConfirmationDetail;

class InputPOCustController extends Controller
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
            $columns = ['id', 'id_order_confirmations', 'so_number', 'date', 'so_type', 'so_category', 'customer', 'salesman', 'reference_number', 'description', 'price', 'qty', 'total_price', 'status'];

            // Query dasar
            $query = DB::table('sales_orders as a')
                ->leftJoin('master_customers as b', 'a.id_master_customers', '=', 'b.id')
                ->leftJoin('master_salesmen as c', 'a.id_master_salesmen', '=', 'c.id')
                // ->join('sales_order_details as d', 'a.so_number', '=', 'd.id_sales_orders')
                ->join(
                    \DB::raw(
                        '(SELECT id, product_code, description, id_master_units, \'FG\' as type_product, perforasi, weight FROM master_product_fgs WHERE status = \'Active\' UNION ALL SELECT id, wip_code as product_code, description, id_master_units, \'WIP\' as type_product, perforasi, weight FROM master_wips WHERE status = \'Active\' UNION ALL SELECT id, rm_code as product_code, description, id_master_units, \'RM\' as type_product, \'NULL\'as perforasi, weight FROM master_raw_materials WHERE status = \'Active\' UNION ALL SELECT id, code as product_code, description, id_master_units, \'AUX\' as type_product, \'NULL\' as perforasi, \'\' as weight FROM master_tool_auxiliaries) e'
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
                ->select('a.id', 'a.id_order_confirmations', 'a.so_number', 'a.date', 'a.so_type', 'a.so_category', 'b.name as customer', 'c.name as salesman', 'a.reference_number', 'a.price', 'a.total_price', 'a.due_date', 'a.status', 'a.qty', 'a.outstanding_delivery_qty', 'e.product_code', 'e.description', 'f.unit_code', 'e.perforasi', 'e.weight')
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
                        ->orWhere('a.so_category', 'like', '%' . $searchValue . '%')
                        ->orWhere('b.name', 'like', '%' . $searchValue . '%')
                        ->orWhere('c.name', 'like', '%' . $searchValue . '%')
                        ->orWhere('a.reference_number', 'like', '%' . $searchValue . '%')
                        ->orWhere('e.description', 'like', '%' . $searchValue . '%')
                        ->orWhere('a.price', 'like', '%' . $searchValue . '%')
                        ->orWhere('a.qty', 'like', '%' . $searchValue . '%')
                        ->orWhere('a.total_price', 'like', '%' . $searchValue . '%')
                        ->orWhere('a.status', 'like', '%' . $searchValue . '%');
                });
            }

            return DataTables::of($query)
                // ->addColumn('action', function ($data) {
                //     return view('marketing.input_po_customer.action', compact('data'));
                // })
                ->addColumn('bulk-action', function ($data) {
                    $checkBox = ($data->status == 'Request' || $data->status == 'Un Posted') ? '<input type="checkbox" name="checkbox" data-so-number="' . $data->so_number . '" />' : '';
                    return $checkBox;
                })
                ->addColumn('progress', function ($data) {
                    $qty = $data->qty . ' ' . $data->unit_code;
                    $outstanding_qty = $data->outstanding_delivery_qty . ' ' . $data->unit_code;
                    $delivery_qty = $data->qty . ' ' . $data->unit_code;
                    return '<span style="font-size: small;width: 100%"><b>Due Date: </b>' . $data->due_date . '<br><b>Qty: </b>' . $qty  . ' ' . '<br><b>Delivered Qty: </b>' . $outstanding_qty . '<br><b>Outstanding Qty: </b>' . $delivery_qty . '<br><b>Deadline: </b>' . $data->due_date . '</span>';
                })
                ->addColumn('description', function ($data) {
                    return $data->product_code . ' - ' . $data->description . ' | Perforasi: ' . $data->perforasi;
                })
                ->addColumn('status', function ($data) {
                    $badgeColor = $data->status == 'Request' ? 'secondary' : ($data->status == 'Un Posted' ? 'warning' : ($data->status == 'Closed' ? 'info' : ($data->status == 'Finish' ? 'primary' : 'success')));
                    return '<span class="badge bg-' . $badgeColor . '" style="font-size: smaller;width: 100%">' . $data->status . '</span>';
                })
                ->addColumn('statusLabel', function ($data) {
                    return $data->status;
                })
                // penambahan kg
                ->addColumn('kg', function ($data) {
                    $kg = ($data->weight != 0 && $data->weight != '') ? number_format($data->qty * $data->weight, 2, ',', '.') : '0,00';
                    return $kg . ' KG';
                })
                ->addColumn('wo_list', function ($data) {
                    $woList = $data->status == 'Request' ? 'Please Wait SO Posted' : 'WO&nbspList';
                    return '<button type="button" class="btn btn-danger btn-sm waves-effect waves-light" style="font-size: smaller;width: 100%">' . $woList . '</button>';
                })
                ->addColumn('basedPrice', function ($data) {
                    // Convert 'price' and 'weight' to float
                    $priceFloat = (float) str_replace(',', '', $data->price); // Menghilangkan koma pada price
                    $weightFloat = (float) $data->weight;

                    // Pastikan weight tidak 0 untuk menghindari division by zero
                    if ($weightFloat == 0) {
                        return 0;
                    }

                    // Lakukan pembagian
                    $result = $priceFloat / $weightFloat;

                    return number_format($result, 0, ',', '.');
                })
                ->rawColumns(['bulk-action', 'progress', 'status', 'statusLabel', 'wo_list', 'basedPrice', 'kg'])
                ->make(true);
        }
        return view('marketing.input_po_customer.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // $customers = MstCustomers::get();
        $customers = $this->getAllCustomers();
        $salesmans = $this->getAllSalesman();
        $termPayments = $this->getAllTermPayment();
        $currencies = $this->getAllCurrency();
        $units = $this->getAllUnit();
        $kodeOtomatis = $this->generateCode();

        return view('marketing.input_po_customer.create', compact('customers', 'salesmans', 'termPayments', 'currencies', 'units', 'kodeOtomatis'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());

        $data = request()->validate([
            'po_number' => 'required',
            'date' => 'required',
            'id_master_customers' => 'required',
            'id_master_salesmen' => 'required',
            'id_master_term_payments' => 'required',
            'id_master_currencies' => 'required',
            'ppn' => 'required',
            'status' => 'required',
            'type_product' => 'required',
            'id_master_products' => 'required',
            'qty' => 'required',
            'id_master_units' => 'required',
            'price' => 'required',
            'subtotal' => 'required',
            'total_price' => 'required',
        ]);

        DB::beginTransaction();
        try {
            // Simpan data ke dalam tabel order_confirmation
            $input_po_customer = InputPOCust::create([
                'po_number' => $request->po_number,
                'date' => $request->date,
                'total_price' => $request->total_price,
                'id_master_customers' => $request->id_master_customers,
                'id_master_salesmen' => $request->id_master_salesmen,
                'id_master_term_payments' => $request->id_master_term_payments,
                'id_master_currencies' => $request->id_master_currencies,
                'ppn' => $request->ppn,
                'remark' => $request->remark,
                'status' => $request->status,
                // Sesuaikan dengan kolom-kolom lain yang ada pada tabel order_confirmation
            ]);

            // Simpan data ke dalam tabel order_confirmation_detail
            foreach ($request->type_product as $key => $typeProduct) {
                InputPOCustDetail::create([
                    'po_number' => $request->po_number,
                    'type_product' => $typeProduct,
                    'id_master_product' => $request->id_master_products[$key],
                    'cust_product_code' => $request->cust_product_code[$key],
                    'qty' => $request->qty[$key],
                    'id_master_units' => $request->id_master_units[$key],
                    'price' => $request->price[$key],
                    'subtotal' => $request->subtotal[$key],
                    // Sesuaikan dengan kolom-kolom lain yang ada pada tabel order_confirmation_detail
                ]);
            }

            $this->saveLogs('Adding New Customer PO : ' . $request->po_number);

            DB::commit();

            if ($request->has('save_add_more')) {
                return redirect()->back()->with(['success' => 'Success Create New PO Customer' . $request->po_number]);
            } else {
                return redirect()->route('marketing.inputPOCust.index')->with(['success' => 'Success Create New PO Customer' . $request->po_number]);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with(['fail' => 'Failed to Create New PO Customer!' . $request->po_number]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(InputPOCust $inputPOCust, $encryptedPoNumber)
    {
        // Dekripsi data
        $poNumber = Crypt::decrypt($encryptedPoNumber);

        $customers = $this->getAllCustomers();
        $salesmans = $this->getAllSalesman();
        $termPayments = $this->getAllTermPayment();
        $currencies = $this->getAllCurrency();
        $units = $this->getAllUnit();

        $inputPOCustomer = InputPOCust::with('inputPOCustomerDetails.masterUnit', 'masterCustomer')
            ->where('po_number', $poNumber)
            ->first();

        return view('marketing.input_po_customer.show', compact('customers', 'salesmans', 'termPayments', 'currencies', 'units', 'inputPOCustomer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InputPOCust $inputPOCust, $encryptedPoNumber)
    {
        // Dekripsi data
        $poNumber = Crypt::decrypt($encryptedPoNumber);

        $customers = $this->getAllCustomers();
        $salesmans = $this->getAllSalesman();
        $termPayments = $this->getAllTermPayment();
        $currencies = $this->getAllCurrency();
        $units = $this->getAllUnit();

        $inputPOCustomer = InputPOCust::with('inputPOCustomerDetails')
            ->where('po_number', $poNumber)
            ->first();

        return view('marketing.input_po_customer.edit', compact('customers', 'salesmans', 'termPayments', 'currencies', 'units', 'inputPOCustomer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InputPOCust $inputPOCust)
    {
        // dd($request->po_number);

        $poNumber = $request->po_number;
        // Mulai transaksi database
        DB::beginTransaction();

        try {
            // Hapus detail POCustomerDetail sesuai po_number
            // InputPOCustDetail::where('po_number', $poNumber)->delete();

            // Update data POCustomer
            $poCustomer = InputPOCust::where('po_number', $poNumber)->first();
            $poCustomer->update([
                'po_number' => $request->po_number,
                'date' => $request->date,
                'total_price' => $request->total_price,
                'id_master_customers' => $request->id_master_customers,
                'id_master_salesmen' => $request->id_master_salesmen,
                'id_master_term_payments' => $request->id_master_term_payments,
                'id_master_currencies' => $request->id_master_currencies,
                'ppn' => $request->ppn,
                'remark' => $request->remark,
                'status' => $request->status,
                // tambahkan kolom lainnya sesuai kebutuhan
            ]);

            // Simpan detail baru ke dalam POCustomerDetail
            foreach ($request->type_product as $index => $typeProduct) {
                $idMasterProduct = $request->id_master_products[$index];

                // Cek apakah data dengan id_master_product sudah ada
                $existingDetail = InputPOCustDetail::where('po_number', $poNumber)
                    ->where('id_master_product', $idMasterProduct)
                    ->first();

                if ($existingDetail) {
                    // Jika sudah ada, update data tersebut
                    $existingDetail->update([
                        'type_product' => $typeProduct,
                        'id_master_product' => $request->id_master_products[$index],
                        'cust_product_code' => $request->cust_product_code[$index],
                        'qty' => $request->qty[$index],
                        'id_master_units' => $request->id_master_units[$index],
                        'price' => $request->price[$index],
                        'subtotal' => $request->subtotal[$index],
                    ]);
                } else {
                    // Jika belum ada, insert data baru
                    InputPOCustDetail::create([
                        'po_number' => $poNumber,
                        'type_product' => $typeProduct,
                        'id_master_product' => $request->id_master_products[$index],
                        'cust_product_code' => $request->cust_product_code[$index],
                        'qty' => $request->qty[$index],
                        'id_master_units' => $request->id_master_units[$index],
                        'price' => $request->price[$index],
                        'subtotal' => $request->subtotal[$index],
                    ]);
                }

                // InputPOCustDetail::create([
                //     'po_number' => $poNumber,
                //     'type_product' => $typeProduct,
                //     'id_master_product' => $request->id_master_products[$index],
                //     'cust_product_code' => $request->cust_product_code[$index],
                //     'qty' => $request->qty[$index],
                //     'id_master_units' => $request->id_master_units[$index],
                //     'price' => $request->price[$index],
                //     'subtotal' => $request->subtotal[$index],
                // ]);
            }

            // Hapus data POCustomerDetail yang tidak ada dalam data baru
            $existingProductIds = collect($request->id_master_products);
            InputPOCustDetail::where('po_number', $poNumber)
                ->whereNotIn('id_master_product', $existingProductIds)
                ->delete();


            $this->saveLogs('Edit Customer PO : ' . $poNumber);
            // Commit transaksi jika berhasil
            DB::commit();

            if ($request->has('update_add_more')) {
                return redirect()->back()->with(['success' => 'Success Update PO Customer ' . $poNumber]);
            } else {
                return redirect()->route('marketing.inputPOCust.index')->with(['success' => 'Success Update PO Customer' . $poNumber]);
            }
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollback();
            return redirect()->back()->with(['fail' => 'Failed to Update PO Customer!' . $poNumber]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InputPOCust $inputPOCust)
    {
        //
    }

    //
    public function getData(Request $request)
    {
        // Query dasar
        $query = DB::table('input_po_customer as a')
            ->join('master_customers as b', 'a.id_master_customers', '=', 'b.id')
            ->join('master_salesmen as c', 'a.id_master_salesmen', '=', 'c.id')
            ->select('a.id', 'a.po_number', 'a.date', 'b.name as customer', 'c.name as salesman', 'a.total_price', 'a.ppn', 'a.status')
            ->orderBy('a.po_number', 'desc');

        return DataTables::of($query)
            ->addColumn('action', function ($data) {
                return view('marketing.input_po_customer.action', compact('data'));
            })
            ->make(true);
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

    public function getAllCurrency()
    {
        $currencies = MstCurrencies::select('id', 'currency')
            ->where('is_active', 1)
            ->orderBy('currency', 'asc')
            ->get();

        return $currencies;
    }

    public function getAllUnit()
    {
        $units = MstUnits::select('id', 'unit')
            ->where('is_active', 1)
            ->orderBy('unit', 'asc')
            ->get();

        return $units;
    }

    public function getCustomerDetail()
    {
        $idCustomer = request()->get('idCustomer');
        $customer = MstCustomers::where('id', $idCustomer)->first();
        $salesmans = $this->getAllSalesman();
        $termPayments = $this->getAllTermPayment();
        $currencies = $this->getAllCurrency();

        return response()->json(['customer' => $customer, 'salesmans' => $salesmans, 'termPayments' => $termPayments, 'currencies' => $currencies]);
    }

    public function generateCode()
    {
        $prefix = 'PO'; // Prefix yang diinginkan
        $currentMonthYear = now()->format('ymd'); // Format tahun dan bulan saat ini
        $suffixLength = 4; // Panjang angka di bagian belakang

        $latestCode = InputPOCust::where('po_number', 'like', "{$prefix}{$currentMonthYear}%")
            ->orderBy('po_number', 'desc')
            ->value('po_number');

        $lastNumber = $latestCode ? intval(substr($latestCode, -1 * $suffixLength)) : 0;

        $newNumber = $lastNumber + 1;

        $newCode = $prefix . $currentMonthYear . str_pad($newNumber, $suffixLength, '0', STR_PAD_LEFT);

        // Gunakan $newCode sesuai kebutuhan Anda
        // return response()->json(['code' => $newCode]);
        return $newCode;
    }

    public function generateCodeKO()
    {
        $prefix = 'KO'; // Prefix yang diinginkan
        $currentMonthYear = now()->format('ymd'); // Format tahun dan bulan saat ini
        $suffixLength = 4; // Panjang angka di bagian belakang

        $latestCode = orderConfirmation::where('oc_number', 'like', "{$prefix}{$currentMonthYear}%")
            ->orderBy('oc_number', 'desc')
            ->value('oc_number');

        $lastNumber = $latestCode ? intval(substr($latestCode, -1 * $suffixLength)) : 0;

        $newNumber = $lastNumber + 1;

        $newCode = $prefix . $currentMonthYear . str_pad($newNumber, $suffixLength, '0', STR_PAD_LEFT);

        // Gunakan $newCode sesuai kebutuhan Anda
        // return response()->json(['code' => $newCode]);
        return $newCode;
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

    public function getDataPOCustomer()
    {
        $poNumber = request()->get('po_number');
        $customerId = request()->get('customer_select');
        $customer = $customerId ? MstCustomers::find($customerId) : null;
        // $customer = MstCustomers::find(request()->get('customer_select'));
        $salesmans = $this->getAllSalesman();
        $termPayments = $this->getAllTermPayment();
        $currencies = $this->getAllCurrency();
        $units = $this->getAllUnit();
        $inputPOCustomer = InputPOCust::with('inputPOCustomerDetails', 'inputPOCustomerDetails.masterUnit')
            ->where('po_number', $poNumber)
            ->first();

        $combinedDataProducts = DB::table('master_product_fgs')
            ->select('id', 'product_code', 'description', 'id_master_units', DB::raw("'FG' as type_product"))
            ->where('status', 'Active')
            ->unionAll(
                DB::table('master_wips')
                    ->select('id', 'wip_code as product_code', 'description', 'id_master_units', DB::raw("'WIP' as type_product"))
                    ->where('status', 'Active')
            )
            ->get();

        // You can then use $combinedData as needed.

        return response()->json(['inputPOCustomer' => $inputPOCustomer, 'salesmans' => $salesmans, 'termPayments' => $termPayments, 'currencies' => $currencies, 'units' => $units, 'products' => $combinedDataProducts, 'customer' => $customer]);
    }

    public function bulkPosted(Request $request)
    {
        $poNumbers = $request->input('po_numbers');

        DB::beginTransaction();
        try {
            // Lakukan logika untuk melakukan bulk update status di sini

            // Contoh: Update status menjadi 'Posted'
            InputPOCust::whereIn('po_number', $poNumbers)
                ->update(['status' => 'Posted', 'updated_at' => now()]);

            // Ambil data yang diupdate beserta detailnya
            // $updatedData = InputPOCust::with('inputPOCustomerDetails')
            //     ->whereIn('po_number', $poNumbers)
            //     ->get();

            // $oc_number = $this->generateCodeKO();

            // Simpan data ke dalam tabel order_confirmation dan order_confirmation_detail
            // foreach ($updatedData as $poCustomer) {
            //     // Simpan data ke dalam order_confirmation
            //     $orderConfirmation = OrderConfirmation::create([
            //         'oc_number' => $oc_number,
            //         'po_number' => $poCustomer->po_number,
            //         'date' => $poCustomer->date, //diambil dari po customer / buat ketika klik posted?
            //         'total_price' => $poCustomer->total_price,
            //         'id_master_customers' => $poCustomer->id_master_customers,
            //         'id_master_salesmen' => $poCustomer->id_master_salesmen,
            //         'id_master_term_payments' => $poCustomer->id_master_term_payments,
            //         'id_master_currencies' => $poCustomer->id_master_currencies,
            //         'ppn' => $poCustomer->ppn,
            //         'remark' => $poCustomer->remark, //diambil dari po customer / buat ketika klik posted?
            //         'status' => $poCustomer->status, //request / posted?
            //     ]);

            //     // Simpan detail ke dalam order_confirmation_detail
            //     foreach ($poCustomer->inputPOCustomerDetails as $detail) {
            //         orderConfirmationDetail::create([
            //             'oc_number' => $oc_number,
            //             'type_product' => $detail->type_product,
            //             'id_master_product' => $detail->id_master_product,
            //             'cust_product_code' => $detail->cust_product_code,
            //             'qty' => $detail->qty,
            //             'id_master_units' => $detail->id_master_units,
            //             'price' => $detail->price,
            //             'subtotal' => $detail->subtotal,
            //         ]);
            //     }
            // }

            // ...

            DB::commit();
            $this->saveLogs('Changed PO Customer ' . implode(', ', $poNumbers) . ' to posted');

            return response()->json(['message' => 'Change to posted successful', 'type' => 'success'], 200);
        } catch (\Exception $e) {
            // Tangani kesalahan jika diperlukan
            return response()->json(['error' => 'Error updating to posted', 'type' => 'error'], 500);
        }
    }

    public function bulkUnPosted(Request $request)
    {
        $poNumbers = $request->input('po_numbers');

        DB::beginTransaction();
        try {
            // Lakukan logika untuk melakukan bulk update status di sini

            // Contoh: Update status menjadi 'Posted'
            InputPOCust::whereIn('po_number', $poNumbers)
                ->update(['status' => 'Request', 'updated_at' => now()]);

            $this->saveLogs('Changed PO Customer ' . implode(', ', $poNumbers) . ' to unposted');

            DB::commit();
            return response()->json(['message' => 'Change to unposted successful', 'type' => 'success'], 200);
        } catch (\Exception $e) {
            // Tangani kesalahan jika diperlukan
            return response()->json(['error' => 'Error updating to unposted', 'type' => 'error'], 500);
        }
    }

    public function bulkDeleted(Request $request)
    {
        $poNumbers = $request->input('po_numbers');

        try {
            // Hapus data POCustomer sesuai po_number
            InputPOCust::whereIn('po_number', $poNumbers)->delete();

            // Hapus data POCustomerDetail sesuai po_number
            InputPOCustDetail::whereIn('po_number', $poNumbers)->delete();

            $this->saveLogs('Deleted PO Customer ' . implode(', ', $poNumbers));

            return response()->json(['message' => 'Successfully deleted data', 'type' => 'success'], 200);
        } catch (\Exception $e) {
            // Tangani kesalahan jika diperlukan
            return response()->json(['error' => 'Failed to delete data', 'type' => 'error'], 500);
        }
    }

    public function preview($encryptedPoNumber)
    {
        $poNumber = Crypt::decrypt($encryptedPoNumber);
        $inputPOCustomer = InputPOCust::with('inputPOCustomerDetails.masterUnit', 'masterSalesman')
            ->where('po_number', $poNumber)
            ->first();
        // dd($pu_customer);
        return view('marketing.input_po_customer.preview', compact('inputPOCustomer'));
    }

    public function print($encryptedPoNumber)
    {
        $poNumber = Crypt::decrypt($encryptedPoNumber);
        $inputPOCustomer = InputPOCust::with('inputPOCustomerDetails', 'inputPOCustomerDetails.masterUnit', 'masterSalesman')
            ->where('po_number', $poNumber)
            ->first();
        // dd($pu_customer);
        return view('marketing.input_po_customer.print', compact('inputPOCustomer'));
    }

    public  function getStatus()
    {
        $status = salesOrder::select('status')->groupBy('status')->orderBy('status', 'asc')->get();

        return response()->json($status);
    }

    public function exportData(Request $request)
    {
        $data = $this->fetchSalesOrderData(
            $request->start_date,
            $request->end_date,
            $request->status
        );

        return Excel::download(new ExportPOCustomer($data), 'po_customer_' . $request->start_date . ' s.d. ' . $request->end_date . '_' . $request->status . '.xlsx');
        // return response()->json($data);
    }

    private function fetchSalesOrderData($startDate, $endDate, $status)
    {
        // $query = salesOrder::whereBetween('date', [$startDate, $endDate]);
        $query = DB::table('sales_orders as a')
            ->leftJoin('master_customers as b', 'a.id_master_customers', '=', 'b.id')
            ->leftJoin('master_salesmen as c', 'a.id_master_salesmen', '=', 'c.id')
            ->leftJoin('master_customer_addresses as d', 'a.id_master_customer_addresses', '=', 'd.id')
            ->leftJoin('master_term_payments as g', 'a.id_master_term_payments', '=', 'g.id')
            ->join(
                \DB::raw(
                    '(SELECT id, product_code, description, id_master_units, \'FG\' as type_product, perforasi, weight FROM master_product_fgs WHERE status = \'Active\' UNION ALL SELECT id, wip_code as product_code, description, id_master_units, \'WIP\' as type_product, perforasi, weight FROM master_wips WHERE status = \'Active\' UNION ALL SELECT id, rm_code as product_code, description, id_master_units, \'RM\' as type_product, \'\' as perforasi, weight FROM master_raw_materials WHERE status = \'Active\' UNION ALL SELECT id, code as product_code, description, id_master_units, \'AUX\' as type_product, \'\' as perforasi, \'\' as weight FROM master_tool_auxiliaries) e'
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
            ->select('a.id', 'a.id_order_confirmations', 'a.so_number', 'a.date', 'a.so_type', 'a.so_category', 'b.name as customer', 'd.address', 'c.name as salesman', 'a.reference_number', 'a.price', 'a.total_price', 'a.due_date', 'a.color', 'a.non_invoiceable', 'a.remarks', 'a.status', 'a.type_product', 'a.qty', 'a.outstanding_delivery_qty', 'e.product_code', 'e.description', 'f.unit_code', 'e.perforasi', 'a.ppn', 'g.term_payment', 'e.weight');

        if ($status !== 'All Status') {
            $query->where('a.status', $status);
        }
        $query->whereBetween('a.date', [$startDate, $endDate]);
        $query->orderBy('a.date', 'desc');

        return $query->get();
    }
}
