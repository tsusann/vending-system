<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with('company');

        // 商品名検索
        if ($request->filled('keyword')) {
            $query->where('product_name', 'like', '%' . $request->keyword . '%');
        }

        // メーカー名検索
        if ($request->filled('company_id')) {
            $query->where('company_id', 'like', $request->company_id);
        }

        // 3. 価格（下限〜上限）検索
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        // 4. 在庫数（下限〜上限）検索
        if ($request->filled('stock_min')) {
            $query->where('stock', '>=', $request->stock_min);
        }
        if ($request->filled('stock_max')) {
            $query->where('stock', '<=', $request->stock_max);
        }

        // 5. ソート処理（初期表示時は id 降順）
        $sortColumn = $request->input('sort_column', 'id');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortColumn, $sortOrder);

        $products = $query->get();

        // セレクトボックス用
        $companies = Company::all();

        // ★ Ajaxリクエストの場合はテーブルの部分HTMLだけを返す
        if ($request->ajax()) {
            return view('products.table', compact('products'))->render();
        }

        // 通常のリクエストの場合は全体画面を返す
        return view('products.index', compact('products', 'companies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $companies = Company::all();

        return view('products.create', compact('companies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {

        // 1. 登録用のデータを配列として準備する
        $data = [
            'product_name' => $request->product_name,
            'company_id' => $request->company_id,
            'price' => $request->price,
            'stock' => $request->stock,
            'comment' => $request->comment,
        ];

        // 2. もし画像ファイルがアップロードされていたら保存処理を行う
        if ($request->hasFile('image')) {
            // storage/app/public/products ディレクトリにランダムなファイル名で保存
            $path = $request->file('image')->store('products', 'public');
            // 保存したパス（例: products/xxxx.png）をデータベース用のデータに追加
            $data['image'] = $path;
        }

        // 3. データベースに一括保存する
        Product::create($data);

        // 4. 一覧画面にリダイレクトする
        return redirect()->route('products.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $companies = Company::all();
        return view('products.edit', compact('product', 'companies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'product_name' => 'required|max:255',
            'company_id' => 'required|max:255',
            'price' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
            'comment' => 'nullable|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // 2. 更新用データの準備
        $data = [
            'product_name' => $request->product_name,
            'company_id' => $request->company_id, // 💡 修正
            'price' => $request->price,
            'stock' => $request->stock,
            'comment' => $request->comment,
        ];

        // 3. 画像がアップロードされた場合の処理
        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            // 新しい画像を public/products ディレクトリに保存
            $path = $request->file('image')->store('products', 'public');
            $data['image'] = $path; // 保存パスを更新データに追加
        }

        $product->update($data);

        $product->update($request->only([
            'product_name',
            'company_name',
            'price',
            'stock',
            'comment',
        ]));

        return redirect()->route('products.show', $product->id)
            ->with('success', '商品情報を更新しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Product $product)
    {
        // 画像ファイルが存在する場合はストレージから削除
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        // DBから商品を削除
        $product->delete();

        // ★ Ajax（非同期）リクエストの場合
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => '商品を削除しました。'
            ]);
        }

        // 通常のリクエストの場合（従来の処理）
        return redirect()->route('products.index')
            ->with('success', '商品を削除しました。');
    }
}
