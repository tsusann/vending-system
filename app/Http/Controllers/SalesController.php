<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Exception;

class SalesController extends Controller
{
    /**
     * 購入処理API
     */
    public function purchase(Request $request)
    {
        // 1. バリデーション
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
        ], [
            'product_id.required' => '商品IDは必須です。',
            'product_id.exists' => '指定された商品が存在しません。',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            // 2. トランザクション処理
            $sale = DB::transaction(function () use ($request) {
                // 排他ロックをかけて商品を取得
                $product = Product::lockForUpdate()->find($request->product_id);

                // 要件③ 在庫が0以下の場合はエラー
                if ($product->stock <= 0) {
                    throw new Exception('在庫が無いため購入できません。');
                }

                // 要件② productsテーブルの在庫数を減算
                $product->stock -= 1;
                $product->save();

                // 要件① salesテーブルにレコードを追加
                $sale = Sale::create([
                    'product_id' => $product->id,
                ]);

                return $sale;
            });

            // 購入成功レスポンス
            return response()->json([
                'message' => '購入処理が完了しました。',
                'sale' => $sale,
            ], 200);

        } catch (Exception $e) {
            // エラーレスポンス（在庫切れ等）
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
