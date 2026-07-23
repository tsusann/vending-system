<?php

namespace App\Http\Controllers;

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

        $products = $query->get();

        // セレクトボックス用
        $companies = Company::all();

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
    public function store(Request $request)
    {
        // 1. バリデーション（入力値のチェック）を行う
        $request->validate([
            'product_name' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'price' => 'required|integer',
            'stock' => 'required|integer',
            // 画像は「任意（必須ではない）」「最大2MB」「jpeg, png, jpg, gif 形式のみ」
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // 2. 登録用のデータを配列として準備する
        $data = [
            'product_name' => $request->product_name,
            'company_id' => $request->company_id,
            'price' => $request->price,
            'stock' => $request->stock,
            'comment' => $request->comment,
        ];

        // 3. もし画像ファイルがアップロードされていたら保存処理を行う
        if ($request->hasFile('image')) {
            // storage/app/public/products ディレクトリにランダムなファイル名で保存
            $path = $request->file('image')->store('products', 'public');
            // 保存したパス（例: products/xxxx.png）をデータベース用のデータに追加
            $data['image'] = $path;
        }

        // 4. データベースに一括保存する
        Product::create($data);

        // 5. 一覧画面にリダイレクトする
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
    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', '商品を削除しました。');
    }
}
