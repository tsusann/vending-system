@extends('layouts.app')

@section('content')
<div class="container">
    <h1>商品一覧</h1>
    <form action="{{ route('products.index') }}" method="GET" class="mb-3">

        <div class="row">

            <div class="col-md-3">
                <input
                    type="text"
                    name="keyword"
                    class="form-control"
                    placeholder="商品名"
                    value="{{ request('keyword') }}">
            </div>

            <div class="col-md-3">
                <select name="company_id" class="form-control">
                    <option value="">メーカーを選択</option>

                    @foreach($companies as $company)
                    <option value="{{ $company->id }}"
                        {{ request('company_id') == $company->id ? 'selected' : '' }}>
                        {{ $company->company_name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">
                    検索
                </button>
            </div>

            <div class="col-md-2">
                <a href="{{ route('products.index') }}" class="btn btn-secondary">
                    リセット
                </a>
            </div>

        </div>

    </form>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <a href="{{ route('products.create') }}" class="btn btn-success mb-3">
        新規登録
    </a>

    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>商品画像</th>
                <th>商品名</th>
                <th>価格</th>
                <th>在庫数</th>
                <th>メーカー</th>
                <th>詳細</th>
                <th>削除</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
            <tr>
                <td>{{ $product->id }}</td>
                <td>
                    @if ($product->image)
                        {{-- 💡登録された画像がある場合は表示 --}}
                        <img src="{{ asset('storage/' . $product->image) }}" alt="商品画像" class="product-thumbnail img-fluid rounded" style="max-width: 60px; height: auto; object-fit: cover;">
                    @else
                        {{-- 💡画像がない場合はプレースホルダー（灰色の画像）を表示 --}}
                        <div class="no-image-placeholder text-center text-muted bg-light rounded" style="width: 60px; height: 60px; line-height: 60px; font-size: 10px; object-fit: cover; border: 1px solid #dee2e6;">
                            No Image
                        </div>
                    @endif
                </td>
                <td>{{ $product->product_name }}</td>
                <td>{{ $product->price }}</td>
                <td>{{ $product->stock }}</td>
                <td>{{ $product->company->company_name }}</td>
                <td>
                    <a href="{{ route('products.show', $product->id) }}"
                        class="btn btn-primary btn-sm">
                        詳細
                    </a>
                </td>
                <td>
                    <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                        onsubmit="return confirm('この商品を削除しますか？');">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-danger btn-sm">
                            削除
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5">商品が登録されていません</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection