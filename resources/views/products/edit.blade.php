@extends('layouts.app')

@section('content')
<div class="container">
    <h1>商品編集</h1>

    <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="product_name">商品名 <span style="color: red;">*</span></label>
            <input type="text" name="product_name" id="product_name" class="form-control" value="{{ $product->product_name }}" required>
        </div>

        <div class="mb-3">
            <label for="company_id">メーカー名 <span style="color: red;">*</span></label>
            <select name="company_id" id="company_id" class="form-control" required>
                <option value="">メーカーを選択</option>
                @foreach($companies as $company)
                <!-- 現在登録されているメーカーを選択状態（selected）にする -->
                <option value="{{ $company->id }}" {{ $product->company_id == $company->id ? 'selected' : '' }}>
                    {{ $company->company_name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="price">価格 <span style="color: red;">*</span></label>
            <input type="number" name="price" id="price" class="form-control" value="{{ $product->price }}" required>
        </div>

        <div class="mb-3">
            <<label for="stock">在庫数 <span style="color: red;">*</span></label>
                <input type="number" name="stock" id="stock" class="form-control" value="{{ $product->stock }}" required>

                <div class="mb-3">
                    <label for="comment">コメント</label>
                    <textarea name="comment" id="comment" class="form-control">{{ $product->comment }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="image">商品画像</label>
                    <input type="file" name="image" id="image" class="form-control-file" accept="image/*">
                    @if ($product->image)
                    <div class="mt-2">
                        <p class="text-muted">現在の画像：</p>
                        <img src="{{ asset('storage/' . $product->image) }}" alt="現在の商品画像" class="product-thumbnail img-fluid rounded" style="max-width: 150px; height: auto;">
                    </div>
                    @endif
                </div>

                <button type="submit" class="btn btn-primary">
                    更新
                </button>

                <a href="{{ route('products.show', $product->id) }}" class="btn btn-secondary">
                    戻る
                </a>
    </form>
</div>
@endsection