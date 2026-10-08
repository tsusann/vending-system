<table class="table table-striped table-hover">
    <thead>
        <tr>
            {{-- IDヘッダー --}}
            <th class="sortable" data-column="id" style="cursor: pointer;">
                ID
                @if(request('sort_column') == 'id')
                {{ request('sort_order') == 'asc' ? '▲' : '▼' }}
                @elseif(!request('sort_column'))
                {{-- 初期表示（id降順）時の矢印 --}}
                ▼
                @endif
            </th>

            <th>商品画像</th>

            {{-- 商品名ヘッダー --}}
            <th class="sortable" data-column="product_name" style="cursor: pointer;">
                商品名
                @if(request('sort_column') == 'product_name')
                {{ request('sort_order') == 'asc' ? '▲' : '▼' }}
                @endif
            </th>

            {{-- 価格ヘッダー --}}
            <th class="sortable" data-column="price" style="cursor: pointer;">
                価格
                @if(request('sort_column') == 'price')
                {{ request('sort_order') == 'asc' ? '▲' : '▼' }}
                @endif
            </th>

            {{-- 在庫数ヘッダー --}}
            <th class="sortable" data-column="stock" style="cursor: pointer;">
                在庫数
                @if(request('sort_column') == 'stock')
                {{ request('sort_order') == 'asc' ? '▲' : '▼' }}
                @endif
            </th>

            {{-- メーカーヘッダー --}}
            <th class="sortable" data-column="company_id" style="cursor: pointer;">
                メーカー
                @if(request('sort_column') == 'company_id')
                {{ request('sort_order') == 'asc' ? '▲' : '▼' }}
                @endif
            </th>

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

                    <button class="btn btn-danger btn-sm btn-delete" data-id="{{ $product->id }}">
                        削除
                    </button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8">商品が登録されていません</td>
        </tr>
        @endforelse
    </tbody>
</table>

<!-- 現在のソート状態を保持する隠しフィールド -->
<input type="hidden" id="current-sort-column" value="{{ request('sort_column', 'id') }}">
<input type="hidden" id="current-sort-order" value="{{ request('sort_order', 'desc') }}">