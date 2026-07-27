@extends('layouts.app')

@section('content')
<div class="container">

    <h1>商品詳細</h1>

    <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <td>{{ $product->id }}</td>
        </tr>

        <tr>
            <th>商品名</th>
            <td>{{ $product->product_name }}</td>
        </tr>

        <tr>
            <th>メーカー名</th>
            <td>{{ $product->company?->company_name }}</td>
        </tr>

        <tr>
            <th>価格</th>
            <td>{{ $product->price }} 円</td>
        </tr>

        <tr>
            <th>在庫数</th>
            <td>{{ $product->stock }}</td>
        </tr>

        <tr>
            <th>コメント</th>
            <td>{{ $product->comment }}</td>
        </tr>

        <tr>
        <th>商品画像</th>
        <td>
            @if ($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->product_name }}" style="max-width: 200px;">
            @else
                <span>画像なし</span>
            @endif
        </td>
    </tr>
    </table>

    <div class="mt-3">
        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning">
            編集
        </a>

        <a href="{{ route('products.index') }}" class="btn btn-secondary">
            一覧へ戻る
        </a>
    </div>


</div>
@endsection