<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class orderConfirmation extends Model
{
    use HasFactory;
    protected $table = 'order_confirmations';
    protected $guarded=[
        'id'
    ];
}
