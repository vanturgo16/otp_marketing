<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Marketing\orderConfirmation;
use App\Models\Marketing\OrderConfirmationDetail;
use App\Models\MstCurrencies;
use App\Models\MstCustomers;
use App\Models\MstSalesmans;
use App\Models\MstTermPayments;
use App\Models\MstUnits;
use App\Traits\AuditLogsTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use  Browser;

class orderConfirmationController extends Controller
{
    use AuditLogsTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orderConfirmations = DB::table('order_confirmations as a')
            ->join('master_customers as b', 'a.id_master_customers', '=', 'b.id')
            ->join('master_salesmen as c', 'a.id_master_salesmen', '=', 'c.id')
            ->select('a.id', 'a.oc_number', 'a.date', 'b.name as customer', 'c.name as salesman', 'a.total_price', 'a.ppn', 'a.status')
            ->orderBy('a.oc_number', 'desc')
            ->get();

        return view('marketing.order_confirmation.index', compact('orderConfirmations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = MstCustomers::get();
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
            'remark' => 'required',
            'status' => 'required',
            'type_product' => 'required',
            'id_master_products' => 'required',
            'cust_product_code' => 'required',
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

            DB::commit();

            if ($request->has('save_add_more')) {
                return redirect()->back()->with(['success' => 'Success Create New Order Confirmation']);
            } else {
                return redirect()->route('marketing.orderConfirmation.index')->with(['success' => 'Success Create New Order Confirmation']);
            }
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with(['fail' => 'Failed to Create New Order Confirmation!']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(orderConfirmation $orderConfirmation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(orderConfirmation $orderConfirmation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, orderConfirmation $orderConfirmation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(orderConfirmation $orderConfirmation)
    {
        //
    }

    public function getCustomers()
    {
        $customers = MstCustomers::select('id', 'customer_code', 'name')
            ->where('status', 'active')
            ->orderBy('customer_code', 'asc')
            ->get();
        return response()->json(['customers' => $customers]);
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
}
