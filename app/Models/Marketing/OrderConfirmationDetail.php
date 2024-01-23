<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderConfirmationDetail extends Model
{
    use HasFactory;
    protected $table = 'order_confirmation_details';
    protected $guarded=[
        'id'
    ];
}
