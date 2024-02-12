<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class orderConfirmation extends Model
{
    use HasFactory;
    protected $table = 'order_confirmations';
    protected $guarded = [
        'id'
    ];

    public function masterCustomerAddress()
    {
        return $this->hasMany(\App\Models\MstCustomersAddress::class, 'id_master_customers', 'id_master_customers');
    }

    // Definisikan relasi one-to-many ke tabel po_customer_details
    public function orderConfirmationDetails()
    {
        return $this->hasMany(OrderConfirmationDetail::class, 'oc_number', 'oc_number');
    }
}
