<?php

namespace App\Http\Controllers\Marketing;

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
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Crypt;
use App\Exports\ExportOrderConfirmation;
use App\Models\Marketing\orderConfirmation;
use App\Models\Marketing\OrderConfirmationDetail;

class orderConfirmationController extends Controller
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
            $columns = ['', '', 'oc_number', 'date', 'customer', 'salesman', 'total_price', 'ppn', 'status', ''];

            // Query dasar
            $query = DB::table('order_confirmations as a')
                ->join('master_customers as b', 'a.id_master_customers', '=', 'b.id')
                ->join('master_salesmen as c', 'a.id_master_salesmen', '=', 'c.id')
                ->select('a.id', 'a.oc_number', 'a.date', 'b.name as customer', 'c.name as salesman', 'a.total_price', 'a.ppn', 'a.status')
                ->orderBy($columns[$orderColumn], $orderDirection);

            // Handle pencarian
            if ($request->has('search') && $request->input('search')) {
                $searchValue = $request->input('search');
                $query->where(function ($query) use ($searchValue) {
                    $query->where('a.oc_number', 'like', '%' . $searchValue . '%')
                        ->orWhere('a.date', 'like', '%' . $searchValue . '%')
                        ->orWhere('b.name', 'like', '%' . $searchValue . '%')
                        ->orWhere('c.name', 'like', '%' . $searchValue . '%')
                        ->orWhere('a.total_price', 'like', '%' . $searchValue . '%')
                        ->orWhere('a.ppn', 'like', '%' . $searchValue . '%')
                        ->orWhere('a.status', 'like', '%' . $searchValue . '%');
                });
            }

            return DataTables::of($query)
                ->addColumn('action', function ($data) {
                    return view('marketing.order_confirmation.action', compact('data'));
                })
                ->addColumn('bulk-action', function ($data) {
                    $checkBox = ($data->status == 'Request' || $data->status == 'Un Posted') ? '<input class="rowCheckbox" type="checkbox" name="checkbox" data-oc-number="' . $data->oc_number . '" />' : '';
                    return $checkBox;
                })
                ->addColumn('status', function ($data) {
                    $badgeColor = $data->status == 'Request' ? 'secondary' : ($data->status == 'Un Posted' ? 'warning' : ($data->status == 'Closed' ? 'info' : ($data->status == 'Finish' ? 'primary' : 'success')));
                    return '<span class="badge bg-' . $badgeColor . '" style="font-size: smaller;width: 100%">' . $data->status . '</span>';
                })
                ->addColumn('statusLabel', function ($data) {
                    return $data->status;
                })
                ->rawColumns(['bulk-action', 'status', 'statusLabel'])
                ->make(true);
        }
        return view('marketing.order_confirmation.index');
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

        return view('marketing.order_confirmation.create', compact('customers', 'salesmans', 'termPayments', 'currencies', 'units', 'kodeOtomatis'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());

        $data = request()->validate([
            'oc_number' => 'required',
            'date' => 'required',
            'id_master_customers' => 'required',
            'id_master_salesmen' => 'required',
            'id_master_term_payments' => 'required',
            'id_master_currencies' => 'required',
            'ppn' => 'required',
            // 'remark' => 'required',
            'status' => 'required',
            'type_product' => 'required',
            'id_master_products' => 'required',
            // 'cust_product_code' => 'required',
            'qty' => 'required',
            'id_master_units' => 'required',
            'price' => 'required',
            'subtotal' => 'required',
            'total_price' => 'required',
        ]);

        DB::beginTransaction();
        try {
            //Audit Log
            // $username = auth()->user()->email;
            // $ipAddress = $_SERVER['REMOTE_ADDR'];
            // $location = '0';
            // $access_from = Browser::browserName();
            // $activity = 'Create New Order Confirmation (' . $request->oc_number . ')';
            // $this->auditLogs($username, $ipAddress, $location, $access_from, $activity);

            // Simpan data ke dalam tabel order_confirmation
            $orderConfirmation = OrderConfirmation::create([
                'oc_number' => $request->oc_number,
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
                OrderConfirmationDetail::create([
                    'oc_number' => $request->oc_number,
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

            $this->saveLogs('Adding New Order Confirmation : ' . $request->oc_number);

            DB::commit();

            if ($request->has('save_add_more')) {
                return redirect()->back()->with(['success' => 'Success Create New Order Confirmation ' . $request->oc_number]);
            } else {
                return redirect()->route('marketing.orderConfirmation.index')->with(['success' => 'Success Create New Order Confirmation ' . $request->oc_number]);
            }
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with(['fail' => 'Failed to Create New Order Confirmation! ' . $request->oc_number]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(orderConfirmation $orderConfirmation, $encryptedOCNumber)
    {
        // Dekripsi data
        $oc_number = Crypt::decrypt($encryptedOCNumber);

        $customers = $this->getAllCustomers();
        $salesmans = $this->getAllSalesman();
        $termPayments = $this->getAllTermPayment();
        $currencies = $this->getAllCurrency();
        $units = $this->getAllUnit();

        $orderConfirmation = orderConfirmation::with('orderConfirmationDetails', 'masterCustomer')
            ->where('oc_number', $oc_number)
            ->first();

        return view('marketing.order_confirmation.show', compact('customers', 'salesmans', 'termPayments', 'currencies', 'units', 'orderConfirmation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(orderConfirmation $orderConfirmation, $encryptedOCNumber)
    {
        // Dekripsi data
        $oc_number = Crypt::decrypt($encryptedOCNumber);

        $customers = $this->getAllCustomers();
        $salesmans = $this->getAllSalesman();
        $termPayments = $this->getAllTermPayment();
        $currencies = $this->getAllCurrency();
        $units = $this->getAllUnit();

        $orderConfirmation = orderConfirmation::with('orderConfirmationDetails')
            ->where('oc_number', $oc_number)
            ->first();

        return view('marketing.order_confirmation.edit', compact('customers', 'salesmans', 'termPayments', 'currencies', 'units', 'orderConfirmation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, orderConfirmation $orderConfirmation)
    {
        // dd($request->oc_number);

        $oc_number = $request->oc_number;
        // Mulai transaksi database
        DB::beginTransaction();

        try {
            // Hapus detail orderConfirmationDetails sesuai oc_number
            // OrderConfirmationDetail::where('oc_number', $oc_number)->delete();

            // Update data orderConfirmation
            $orderConfirmation = orderConfirmation::where('oc_number', $oc_number)->first();
            $orderConfirmation->update([
                'oc_number' => $request->oc_number,
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

            // Simpan detail baru ke dalam orderConfirmationDetails
            foreach ($request->type_product as $index => $typeProduct) {
                $idMasterProduct = $request->id_master_products[$index];

                // Cek apakah data dengan id_master_product sudah ada
                $existingDetail = OrderConfirmationDetail::where('oc_number', $oc_number)
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
                    OrderConfirmationDetail::create([
                        'oc_number' => $oc_number,
                        'type_product' => $typeProduct,
                        'id_master_product' => $request->id_master_products[$index],
                        'cust_product_code' => $request->cust_product_code[$index],
                        'qty' => $request->qty[$index],
                        'id_master_units' => $request->id_master_units[$index],
                        'price' => $request->price[$index],
                        'subtotal' => $request->subtotal[$index],
                    ]);
                }

                // OrderConfirmationDetail::create([
                //     'oc_number' => $oc_number,
                //     'type_product' => $typeProduct,
                //     'id_master_product' => $request->id_master_products[$index],
                //     'cust_product_code' => $request->cust_product_code[$index],
                //     'qty' => $request->qty[$index],
                //     'id_master_units' => $request->id_master_units[$index],
                //     'price' => $request->price[$index],
                //     'subtotal' => $request->subtotal[$index],
                // ]);
            }

            // Hapus data orderConfirmationDetails yang tidak ada dalam data baru
            $existingProductIds = collect($request->id_master_products);
            OrderConfirmationDetail::where('oc_number', $oc_number)
                ->whereNotIn('id_master_product', $existingProductIds)
                ->delete();


            $this->saveLogs('Edit Order Confirmation : ' . $oc_number);
            // Commit transaksi jika berhasil
            DB::commit();

            if ($request->has('update_add_more')) {
                return redirect()->route('marketing.orderConfirmation.create')->with(['success' => 'Success Update Order Confirmation ' . $oc_number]);
            } else {
                return redirect()->route('marketing.orderConfirmation.index')->with(['success' => 'Success Update Order Confirmation ' . $oc_number]);
            }
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollback();
            return redirect()->back()->with(['fail' => 'Failed to Update Order Confirmation! ' . $oc_number]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(orderConfirmation $orderConfirmation)
    {
        //
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
        $units = MstUnits::select('*')
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
                ->select('a.id', 'a.wip_code', 'a.description', 'a.perforasi')
                ->get();
        } else if ($typeProduct == 'FG') {
            $products = DB::table('master_product_fgs as a')
                ->where('a.status', 'Active')
                ->select('a.id', 'a.product_code', 'a.description', 'a.perforasi', 'a.group_sub_code')
                ->get();
        } else if ($typeProduct == 'RM') {
            $products = DB::table('master_raw_materials as a')
                ->where('a.status', 'Active')
                ->select('a.id', 'a.rm_code', 'a.description')
                ->get();
        } else if ($typeProduct == 'AUX') {
            $products = DB::table('master_tool_auxiliaries as a')
                // ->where('a.status', 'Active')
                ->select('a.id', 'a.code', 'a.description')
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
        } else if ($typeProduct == 'RM') {
            $product = DB::table('master_raw_materials as a')
                ->select('a.id', 'a.description', 'a.id_master_units')
                // ->join('master_units as b', 'a.id_master_units', '=', 'b.id')
                ->where('a.id', $idProduct)
                ->first();
        } else if ($typeProduct == 'AUX') {
            $product = DB::table('master_tool_auxiliaries as a')
                ->select('a.id', 'a.description', 'a.id_master_units')
                // ->join('master_units as b', 'a.id_master_units', '=', 'b.id')
                ->where('a.id', $idProduct)
                ->first();
        }
        return response()->json(['product' => $product]);
    }

    public function getDataOrderConfirmation()
    {
        $oc_number = request()->get('oc_number');
        $customerId = request()->get('customer_select');
        $customer = $customerId ? MstCustomers::find($customerId) : null;
        // $customer = MstCustomers::find(request()->get('customer_select'));
        $salesmans = $this->getAllSalesman();
        $termPayments = $this->getAllTermPayment();
        $currencies = $this->getAllCurrency();
        $units = $this->getAllUnit();
        $orderConfirmation = orderConfirmation::with('orderConfirmationDetails', 'orderConfirmationDetails.masterUnit')
            ->where('oc_number', $oc_number)
            ->first();

        $combinedDataProducts = DB::table('master_product_fgs')
            ->select('id', 'product_code', 'description', 'id_master_units', DB::raw("'FG' as type_product"), 'perforasi', 'group_sub_code')
            ->where('status', 'Active')
            ->unionAll(
                DB::table('master_wips')
                    ->select('id', 'wip_code as product_code', 'description', 'id_master_units', DB::raw("'WIP' as type_product"), 'perforasi', DB::raw('"" as group_sub_code'))
                    ->where('status', 'Active')
            )
            ->unionAll(
                DB::table('master_raw_materials')
                    ->select('id', 'rm_code as product_code', 'description', 'id_master_units', DB::raw("'RM' as type_product"), DB::raw('"" as perforasi'), DB::raw('"" as group_sub_code'))
                    ->where('status', 'Active')
            )
            ->unionAll(
                DB::table('master_tool_auxiliaries')
                    ->select('id', 'code as product_code', 'description', 'id_master_units', DB::raw("'AUX' as type_product"), DB::raw('"" as perforasi'), DB::raw('"" as group_sub_code'))
            )
            ->get();

        // You can then use $combinedData as needed.

        return response()->json(['orderConfirmation' => $orderConfirmation, 'salesmans' => $salesmans, 'termPayments' => $termPayments, 'currencies' => $currencies, 'units' => $units, 'products' => $combinedDataProducts, 'customer' => $customer]);
    }

    public function bulkPosted(Request $request)
    {
        $oc_numbers = $request->input('oc_numbers');

        DB::beginTransaction();
        try {
            // Lakukan logika untuk melakukan bulk update status di sini

            // Contoh: Update status menjadi 'Posted'
            orderConfirmation::whereIn('oc_number', $oc_numbers)
                ->update(['status' => 'Posted', 'updated_at' => now()]);

            // Ambil data yang diupdate beserta detailnya
            // $updatedData = orderConfirmation::with('inputPOCustomerDetails')
            //     ->whereIn('oc_number', $oc_numbers)
            //     ->get();

            // $oc_number = $this->generateCodeKO();

            // Simpan data ke dalam tabel order_confirmation dan order_confirmation_detail
            // foreach ($updatedData as $poCustomer) {
            //     // Simpan data ke dalam order_confirmation
            //     $orderConfirmation = OrderConfirmation::create([
            //         'oc_number' => $oc_number,
            //         'oc_number' => $poCustomer->oc_number,
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
            $this->saveLogs('Changed Order Confirmation ' . implode(', ', $oc_numbers) . ' to posted');

            return response()->json(['message' => 'Change to posted successful', 'type' => 'success'], 200);
        } catch (\Exception $e) {
            // Tangani kesalahan jika diperlukan
            return response()->json(['error' => 'Error updating to posted', 'type' => 'error'], 500);
        }
    }

    public function bulkUnPosted(Request $request)
    {
        $oc_numbers = $request->input('oc_numbers');

        DB::beginTransaction();
        try {
            // Lakukan logika untuk melakukan bulk update status di sini

            // Contoh: Update status menjadi 'Posted'
            orderConfirmation::whereIn('oc_number', $oc_numbers)
                ->update(['status' => 'Un Posted', 'updated_at' => now()]);

            $this->saveLogs('Changed Order Confirmation ' . implode(', ', $oc_numbers) . ' to unposted');

            DB::commit();
            return response()->json(['message' => 'Change to unposted successful', 'type' => 'success'], 200);
        } catch (\Exception $e) {
            // Tangani kesalahan jika diperlukan
            return response()->json(['error' => 'Error updating to unposted', 'type' => 'error'], 500);
        }
    }

    public function bulkDeleted(Request $request)
    {
        $oc_numbers = $request->input('oc_numbers');

        try {
            // Hapus data POCustomer sesuai oc_number
            orderConfirmation::whereIn('oc_number', $oc_numbers)->delete();

            // Hapus data POCustomerDetail sesuai oc_number
            OrderConfirmationDetail::whereIn('oc_number', $oc_numbers)->delete();

            $this->saveLogs('Deleted Order Confirmation ' . implode(', ', $oc_numbers));

            return response()->json(['message' => 'Successfully deleted data', 'type' => 'success'], 200);
        } catch (\Exception $e) {
            // Tangani kesalahan jika diperlukan
            return response()->json(['error' => 'Failed to delete data', 'type' => 'error'], 500);
        }
    }

    public function preview($encryptedOCNumber)
    {
        $oc_number = Crypt::decrypt($encryptedOCNumber);
        $orderConfirmation = orderConfirmation::with('orderConfirmationDetails.masterUnit', 'masterSalesman')
            ->where('oc_number', $oc_number)
            ->first();
        // dd($pu_customer);
        return view('marketing.order_confirmation.preview', compact('orderConfirmation'));
    }

    public function print($encryptedOCNumber)
    {
        $oc_number = Crypt::decrypt($encryptedOCNumber);
        $orderConfirmation = orderConfirmation::with('orderConfirmationDetails', 'orderConfirmationDetails.masterUnit', 'masterSalesman', 'masterCustomer', 'masterCustomer.currency', 'masterCustomer.termPayment', 'masterCustomer.customerAddress')
            ->where('oc_number', $oc_number)
            ->first();
        // return json_encode($orderConfirmation);
        // dd($orderConfirmation);
        return view('marketing.order_confirmation.print', compact('orderConfirmation'));
    }

    public  function getStatus()
    {
        $status = orderConfirmation::select('status')->groupBy('status')->orderBy('status', 'asc')->get();

        return response()->json($status);
    }

    public function exportData(Request $request)
    {
        $data = $this->fetchSalesOrderData(
            $request->start_date,
            $request->end_date,
            $request->status
        );

        return Excel::download(new ExportOrderConfirmation($data), 'order_confirmation_' . $request->start_date . ' s.d. ' . $request->end_date . '_' . $request->status . '.xlsx');
        // return response()->json($data);
    }

    private function fetchSalesOrderData($startDate, $endDate, $status)
    {
        // $query = salesOrder::whereBetween('date', [$startDate, $endDate]);
        $query = DB::table('order_confirmations as a')
            ->join('master_customers as b', 'a.id_master_customers', '=', 'b.id')
            ->join('master_salesmen as c', 'a.id_master_salesmen', '=', 'c.id')
            ->select('a.id', 'a.oc_number', 'a.date', 'b.name as customer', 'c.name as salesman', 'a.total_price', 'a.ppn', 'a.status');

        if ($status !== 'All Status') {
            $query->where('a.status', $status);
        }
        $query->whereBetween('a.date', [$startDate, $endDate]);
        $query->orderBy('a.date', 'desc');

        return $query->get();
    }
}
