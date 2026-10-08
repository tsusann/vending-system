@extends('layouts.app')

@section('content')
<div class="container">
    <h1>商品一覧</h1>
    <form action="{{ route('products.index') }}" method="GET" class="mb-3" id="search-form">

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

            <!-- 価格（下限〜上限） -->
            <div class="row g-2 mb-2">
                <div class="col-md-3">
                    <input
                        type="number"
                        name="price_min"
                        class="form-control"
                        placeholder="価格(下限)"
                        value="{{ request('price_min') }}">
                </div>
                <div class="col-md-3">
                    <input
                        type="number"
                        name="price_max"
                        class="form-control"
                        placeholder="価格(上限)"
                        value="{{ request('price_max') }}">
                </div>
            </div>

            <!-- 在庫数（下限〜上限） -->
            <div class="row g-2 mb-3">
                <div class="col-md-3">
                    <input
                        type="number"
                        name="stock_min"
                        class="form-control"
                        placeholder="在庫数(下限)"
                        value="{{ request('stock_min') }}">
                </div>
                <div class="col-md-3">
                    <input
                        type="number"
                        name="stock_max"
                        class="form-control"
                        placeholder="在庫数(上限)"
                        value="{{ request('stock_max') }}">
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

    <!-- テーブル部分（非同期差し替え領域） -->
    <div id="product-table-wrapper">
        @include('products.table')
    </div>


</div>
@endsection

<!-- JavaScriptの処理 -->
@section('script')
<script>
    $(document).ready(function() {
        // データ取得用共通関数
        function fetchProducts(sortColumn, sortOrder) {
            // フォーム内の入力を全取得 (keyword, company_id, price_*, stock_*)
            let formData = $('#search-form').serializeArray();

            // ソート条件を追加
            if (sortColumn) {
                formData.push({
                    name: 'sort_column',
                    value: sortColumn
                });
                formData.push({
                    name: 'sort_order',
                    value: sortOrder
                });
            }

            $.ajax({
                    url: $('#search-form').attr('action'),
                    type: 'GET',
                    data: formData,
                    dataType: 'html'
                })
                .done(function(data) {
                    // テーブル領域を差し替え
                    $('#product-table-wrapper').html(data);
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    alert('データの取得に失敗しました。');
                    console.error(textStatus, errorThrown);
                });
        }

        // 1. 検索フォーム送信時（ボタン押下／Enterキー押下）
        $('#search-form').on('submit', function(e) {
            e.preventDefault(); // 通常のページ遷移をキャンセル

            // 現在適用されているソート条件（なければ初期値 id 降順）を維持して検索
            const currentColumn = $('#current-sort-column').val() || 'id';
            const currentOrder = $('#current-sort-order').val() || 'desc';

            fetchProducts(currentColumn, currentOrder);
        });

        // 2. テーブルヘッダークリック時のソート処理
        // ※Ajaxによる要素更新に対応するため $(document).on でイベント委譲
        $(document).on('click', '.sortable', function() {
            const clickedColumn = $(this).data('column');
            const currentColumn = $('#current-sort-column').val();
            const currentOrder = $('#current-sort-order').val();

            let newOrder = 'asc';

            // 同じカラムをクリックした場合は 昇順/降順 を反転
            if (clickedColumn === currentColumn) {
                newOrder = (currentOrder === 'asc') ? 'desc' : 'asc';
            }

            fetchProducts(clickedColumn, newOrder);
        });

        // 3. 【追加】削除ボタン押下時の非同期（Ajax）処理
        $(document).on('click', '.btn-delete', function() {
            if (!confirm('この商品を削除しますか？')) {
                return;
            }

            const button = $(this);
            const productId = button.data('id');
            const row = button.closest('tr'); // 削除対象の行を取得

            $.ajax({
                url: '/products/' + productId, // ルーティングの定義に合わせて調整してください
                type: 'POST',
                data: {
                    '_method': 'DELETE',
                    '_token': $('meta[name="csrf-token"]').attr('content') // CSRFトークン
                },
                dataType: 'json'
            })
            .done(function(response) {
                // 成功時、該当の行（tr）をフェードアウトして削除
                row.fadeOut(300, function() {
                    $(this).remove();
                });
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                alert('削除処理に失敗しました。');
                console.error(textStatus, errorThrown);
            });
        });
    });
</script>

@endsection