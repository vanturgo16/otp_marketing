<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class salesOrder extends Model
{
    use HasFactory;
    protected $table = 'sales_orders';
    protected $guarded = [
        'id'
    ];
}
