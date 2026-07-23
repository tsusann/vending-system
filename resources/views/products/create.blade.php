@extends('layouts.app')

@section('content')
<div class="container">
    <h1>商品登録</h1>

    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="product_name">商品名 <span style="color: red;">*</span></label>
            <input type="text" name="product_name" id="product_name" class="form-control" required>
        </div>

        <div class="mb-3">
            <<label for="company_id">メーカー名 <span style="color: red;">*</span></label>
                <select name="company_id" id="company_id" class="form-control" required>
                    <option value="">メーカーを選択</option>
                    @foreach($companies as $company)
                    <option value="{{ $company->id }}">
                        {{ $company->company_name }}
                    </option>
                    @endforeach
                </select>
        </div>

        <div class="mb-3">
            <label for="price">価格 <span style="color: red;">*</span></label>
            <input type="number" name="price" id="price" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="stock">在庫数 <span style="color: red;">*</span></label>
            <input type="number" name="stock" id="stock" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="comment">コメント</label>
            <textarea name="comment" id="comment" class="form-control"></textarea>
        </div>

        <div class="mb-3">
            <label for="image">商品画像</label>
            <input type="file" name="image" id="image" class="form-control-file" accept="image/*">
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <button type="submit" class="btn btn-primary">
                登録
            </button>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">戻る</a>
        </div>
    </form>
</div>
@endsection