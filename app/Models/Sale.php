<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory; 

    // 一括割り当て（Sale::create）を許可するカラムを指定
    protected $fillable = [
        'product_id',
    ];

    /**
     * リレーション：商品（Product）モデルとの参照関係
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
