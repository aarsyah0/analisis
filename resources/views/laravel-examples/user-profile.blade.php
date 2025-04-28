{{-- resources/views/sentimen/index.blade.php --}}
@extends('layouts.user_type.auth')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            @php
                $perPageOptions = [10, 25, 50, 100];
            @endphp

            {{-- === Table for Dana === --}}
            @php
                $pathDana = resource_path('views/danalabel.csv');
                $allRowsDana = file_exists($pathDana) ? array_map('str_getcsv', file($pathDana)) : [];
                $headerDana = count($allRowsDana) > 1 ? array_shift($allRowsDana) : [];
                $perPageDana = (int) request()->get('per_page_dana', 10);
                $currentPageDana = (int) request()->get('page_dana', 1);
                $paginatorDana = new \Illuminate\Pagination\LengthAwarePaginator(
                    array_slice($allRowsDana, ($currentPageDana - 1) * $perPageDana, $perPageDana),
                    count($allRowsDana),
                    $perPageDana,
                    $currentPageDana,
                    [
                        'path' => url()->current(),
                        'pageName' => 'page_dana',
                        'query' => request()->except('page_dana'),
                    ],
                );
            @endphp
            <div class="col-12 mb-4" id="dana">
                <div class="card shadow-sm border-0 rounded-2">
                    <div class="card-header bg-white border-bottom-0 pb-0">
                        <h6 class="mb-0">Data Sentimen Dana (danalabel.csv)</h6>
                    </div>
                    <div class="card-body px-0 pt-2 pb-3">
                        <div class="table-responsive">
                            @if (count($headerDana))
                                <table class="table table-striped table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            @foreach ($headerDana as $col)
                                                <th class="text-uppercase text-secondary text-sm fw-bold">
                                                    {{ $col }}
                                                </th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($paginatorDana as $row)
                                            <tr>
                                                @foreach ($row as $cell)
                                                    <td class="text-sm text-dark">{{ $cell }}</td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-center text-muted py-4">
                                    File danalabel.csv tidak ditemukan atau kosong.
                                </p>
                            @endif
                        </div>

                        @if (count($headerDana))
                            <div class="d-flex justify-content-between align-items-center px-4 mt-3">
                                {{-- Left: items per page + summary --}}
                                <div class="d-flex align-items-center text-sm">
                                    <label class="me-2 mb-0">Items per page</label>
                                    <form method="get" class="d-flex">
                                        <select name="per_page_dana" onchange="this.form.submit()"
                                            class="form-select form-select-sm me-3" style="width:auto">
                                            @foreach ($perPageOptions as $n)
                                                <option value="{{ $n }}"
                                                    @if ($perPageDana == $n) selected @endif>
                                                    {{ $n }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @foreach (request()->except(['per_page_dana', 'page_dana']) as $k => $v)
                                            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                                        @endforeach
                                    </form>
                                    <span class="text-secondary">
                                        {{ $paginatorDana->firstItem() }}–{{ $paginatorDana->lastItem() }} of
                                        {{ $paginatorDana->total() }}
                                    </span>
                                </div>
                                {{-- Right: pagination controls --}}
                                <nav aria-label="Page navigation" class="mt-2">
                                    <ul
                                        class="pagination pagination-sm justify-content-center shadow-sm bg-white rounded p-2">
                                        {{-- First --}}
                                        <li class="page-item {{ $paginatorDana->onFirstPage() ? 'disabled' : '' }}">
                                            <a class="page-link" href="{{ $paginatorDana->url(1) }}#dana"
                                                aria-label="First">
                                                <span aria-hidden="true">&laquo;&laquo;</span>
                                            </a>
                                        </li>

                                        {{-- Previous --}}
                                        <li class="page-item {{ $paginatorDana->onFirstPage() ? 'disabled' : '' }}">
                                            <a class="page-link" href="{{ $paginatorDana->previousPageUrl() }}#dana"
                                                aria-label="Previous">
                                                <span aria-hidden="true">&laquo;</span>
                                            </a>
                                        </li>

                                        {{-- Page Numbers (2 before & 2 after current) --}}
                                        @for ($i = max(1, $paginatorDana->currentPage() - 2); $i <= min($paginatorDana->lastPage(), $paginatorDana->currentPage() + 2); $i++)
                                            <li
                                                class="page-item {{ $paginatorDana->currentPage() == $i ? 'active' : '' }}">
                                                <a class="page-link"
                                                    href="{{ $paginatorDana->url($i) }}#dana">{{ $i }}</a>
                                            </li>
                                        @endfor

                                        {{-- Next --}}
                                        <li class="page-item {{ $paginatorDana->hasMorePages() ? '' : 'disabled' }}">
                                            <a class="page-link" href="{{ $paginatorDana->nextPageUrl() }}#dana"
                                                aria-label="Next">
                                                <span aria-hidden="true">&raquo;</span>
                                            </a>
                                        </li>

                                        {{-- Last --}}
                                        <li class="page-item {{ $paginatorDana->hasMorePages() ? '' : 'disabled' }}">
                                            <a class="page-link"
                                                href="{{ $paginatorDana->url($paginatorDana->lastPage()) }}#dana"
                                                aria-label="Last">
                                                <span aria-hidden="true">&raquo;&raquo;</span>
                                            </a>
                                        </li>
                                    </ul>
                                </nav>

                            </div>
                        @endif

                    </div>
                </div>
            </div>

            {{-- === Table for GoPay === --}}
            @php
                $pathGo = resource_path('views/gopaylabel.csv');
                $allRowsGo = file_exists($pathGo) ? array_map('str_getcsv', file($pathGo)) : [];
                $headerGo = count($allRowsGo) > 1 ? array_shift($allRowsGo) : [];
                $perPageGo = (int) request()->get('per_page_go', 10);
                $currentPageGo = (int) request()->get('page_go', 1);
                $paginatorGo = new \Illuminate\Pagination\LengthAwarePaginator(
                    array_slice($allRowsGo, ($currentPageGo - 1) * $perPageGo, $perPageGo),
                    count($allRowsGo),
                    $perPageGo,
                    $currentPageGo,
                    [
                        'path' => url()->current(),
                        'pageName' => 'page_go',
                        'query' => request()->except('page_go'),
                    ],
                );
            @endphp
            <div class="col-12 mb-4" id="gopay">
                <div class="card shadow-sm border-0 rounded-2">
                    <div class="card-header bg-white border-bottom-0 pb-0">
                        <h6 class="mb-0">Data Sentimen GoPay (gopaylabel.csv)</h6>
                    </div>
                    <div class="card-body px-0 pt-2 pb-3">
                        <div class="table-responsive">
                            @if (count($headerGo))
                                <table class="table table-striped table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            @foreach ($headerGo as $col)
                                                <th class="text-uppercase text-secondary text-sm fw-bold">
                                                    {{ $col }}
                                                </th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($paginatorGo as $row)
                                            <tr>
                                                @foreach ($row as $cell)
                                                    <td class="text-sm text-dark">{{ $cell }}</td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-center text-muted py-4">
                                    File terlabelgopay.csv tidak ditemukan atau kosong.
                                </p>
                            @endif
                        </div>

                        @if (count($headerGo))
                            <div class="d-flex justify-content-between align-items-center px-4 mt-3">
                                <div class="d-flex align-items-center text-sm">
                                    <label class="me-2 mb-0">Items per page</label>
                                    <form method="get" class="d-flex">
                                        <select name="per_page_go" onchange="this.form.submit()"
                                            class="form-select form-select-sm me-3" style="width:auto">
                                            @foreach ($perPageOptions as $n)
                                                <option value="{{ $n }}"
                                                    @if ($perPageGo == $n) selected @endif>
                                                    {{ $n }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @foreach (request()->except(['per_page_go', 'page_go']) as $k => $v)
                                            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                                        @endforeach
                                    </form>
                                    <span class="text-secondary">
                                        {{ $paginatorGo->firstItem() }}–{{ $paginatorGo->lastItem() }} of
                                        {{ $paginatorGo->total() }}
                                    </span>
                                </div>
                                <nav aria-label="Page navigation" class="mt-2">
                                    <ul
                                        class="pagination pagination-sm justify-content-center shadow-sm bg-white rounded p-2">
                                        {{-- First --}}
                                        <li class="page-item {{ $paginatorGo->onFirstPage() ? 'disabled' : '' }}">
                                            <a class="page-link" href="{{ $paginatorGo->url(1) }}#gopay"
                                                aria-label="First">
                                                <span aria-hidden="true">&laquo;&laquo;</span>
                                            </a>
                                        </li>

                                        {{-- Prev --}}
                                        <li class="page-item {{ $paginatorGo->onFirstPage() ? 'disabled' : '' }}">
                                            <a class="page-link" href="{{ $paginatorGo->previousPageUrl() }}#gopay"
                                                aria-label="Previous">
                                                <span aria-hidden="true">&laquo;</span>
                                            </a>
                                        </li>

                                        {{-- Page numbers --}}
                                        @for ($i = max(1, $paginatorGo->currentPage() - 2); $i <= min($paginatorGo->lastPage(), $paginatorGo->currentPage() + 2); $i++)
                                            <li class="page-item {{ $paginatorGo->currentPage() == $i ? 'active' : '' }}">
                                                <a class="page-link"
                                                    href="{{ $paginatorGo->url($i) }}#gopay">{{ $i }}</a>
                                            </li>
                                        @endfor

                                        {{-- Next --}}
                                        <li class="page-item {{ $paginatorGo->hasMorePages() ? '' : 'disabled' }}">
                                            <a class="page-link" href="{{ $paginatorGo->nextPageUrl() }}#gopay"
                                                aria-label="Next">
                                                <span aria-hidden="true">&raquo;</span>
                                            </a>
                                        </li>

                                        {{-- Last --}}
                                        <li class="page-item {{ $paginatorGo->hasMorePages() ? '' : 'disabled' }}">
                                            <a class="page-link"
                                                href="{{ $paginatorGo->url($paginatorGo->lastPage()) }}#gopay"
                                                aria-label="Last">
                                                <span aria-hidden="true">&raquo;&raquo;</span>
                                            </a>
                                        </li>
                                    </ul>
                                </nav>

                            </div>
                        @endif

                    </div>
                </div>
            </div>

            {{-- === Table for ShopeePay === --}}
            @php
                $pathShopee = resource_path('views/shopeepaylabel.csv');
                $allRowsShopee = file_exists($pathShopee) ? array_map('str_getcsv', file($pathShopee)) : [];
                $headerShopee = count($allRowsShopee) > 1 ? array_shift($allRowsShopee) : [];
                $perPageShopee = (int) request()->get('per_page_shopee', 10);
                $currentPageShopee = (int) request()->get('page_shopee', 1);
                $paginatorShopee = new \Illuminate\Pagination\LengthAwarePaginator(
                    array_slice($allRowsShopee, ($currentPageShopee - 1) * $perPageShopee, $perPageShopee),
                    count($allRowsShopee),
                    $perPageShopee,
                    $currentPageShopee,
                    [
                        'path' => url()->current(),
                        'pageName' => 'page_shopee',
                        'query' => request()->except('page_shopee'),
                    ],
                );
            @endphp
            <div class="col-12 mb-4" id="shopeepay">
                <div class="card shadow-sm border-0 rounded-2">
                    <div class="card-header bg-white border-bottom-0 pb-0">
                        <h6 class="mb-0">Data Sentimen ShopeePay (shopepaylabel.csv)</h6>
                    </div>
                    <div class="card-body px-0 pt-2 pb-3">
                        <div class="table-responsive">
                            @if (count($headerShopee))
                                <table class="table table-striped table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            @foreach ($headerShopee as $col)
                                                <th class="text-uppercase text-secondary text-sm fw-bold">
                                                    {{ $col }}
                                                </th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($paginatorShopee as $row)
                                            <tr>
                                                @foreach ($row as $cell)
                                                    <td class="text-sm text-dark">{{ $cell }}</td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-center text-muted py-4">
                                    File shopepaylabel.csv tidak ditemukan atau kosong.
                                </p>
                            @endif
                        </div>

                        @if (count($headerShopee))
                            <div class="d-flex justify-content-between align-items-center px-4 mt-3">
                                <div class="d-flex align-items-center text-sm">
                                    <label class="me-2 mb-0">Items per page</label>
                                    <form method="get" class="d-flex">
                                        <select name="per_page_shopee" onchange="this.form.submit()"
                                            class="form-select form-select-sm me-3" style="width:auto">
                                            @foreach ($perPageOptions as $n)
                                                <option value="{{ $n }}"
                                                    @if ($perPageShopee == $n) selected @endif>
                                                    {{ $n }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @foreach (request()->except(['per_page_shopee', 'page_shopee']) as $k => $v)
                                            <input type="hidden" name="{{ $k }}"
                                                value="{{ $v }}">
                                        @endforeach
                                    </form>
                                    <span class="text-secondary">
                                        {{ $paginatorShopee->firstItem() }}–{{ $paginatorShopee->lastItem() }} of
                                        {{ $paginatorShopee->total() }}
                                    </span>
                                </div>
                                <nav aria-label="Page navigation" class="mt-2">
                                    <ul
                                        class="pagination pagination-sm justify-content-center shadow-sm bg-white rounded p-2">
                                        {{-- First --}}
                                        <li class="page-item {{ $paginatorShopee->onFirstPage() ? 'disabled' : '' }}">
                                            <a class="page-link" href="{{ $paginatorShopee->url(1) }}#shopeepay"
                                                aria-label="First">
                                                <span aria-hidden="true">&laquo;&laquo;</span>
                                            </a>
                                        </li>

                                        {{-- Prev --}}
                                        <li class="page-item {{ $paginatorShopee->onFirstPage() ? 'disabled' : '' }}">
                                            <a class="page-link"
                                                href="{{ $paginatorShopee->previousPageUrl() }}#shopeepay"
                                                aria-label="Previous">
                                                <span aria-hidden="true">&laquo;</span>
                                            </a>
                                        </li>

                                        {{-- Page numbers (2 before and after current) --}}
                                        @for ($i = max(1, $paginatorShopee->currentPage() - 2); $i <= min($paginatorShopee->lastPage(), $paginatorShopee->currentPage() + 2); $i++)
                                            <li
                                                class="page-item {{ $paginatorShopee->currentPage() == $i ? 'active' : '' }}">
                                                <a class="page-link"
                                                    href="{{ $paginatorShopee->url($i) }}#shopeepay">{{ $i }}</a>
                                            </li>
                                        @endfor

                                        {{-- Next --}}
                                        <li class="page-item {{ $paginatorShopee->hasMorePages() ? '' : 'disabled' }}">
                                            <a class="page-link" href="{{ $paginatorShopee->nextPageUrl() }}#shopeepay"
                                                aria-label="Next">
                                                <span aria-hidden="true">&raquo;</span>
                                            </a>
                                        </li>

                                        {{-- Last --}}
                                        <li class="page-item {{ $paginatorShopee->hasMorePages() ? '' : 'disabled' }}">
                                            <a class="page-link"
                                                href="{{ $paginatorShopee->url($paginatorShopee->lastPage()) }}#shopeepay"
                                                aria-label="Last">
                                                <span aria-hidden="true">&raquo;&raquo;</span>
                                            </a>
                                        </li>
                                    </ul>
                                </nav>

                            </div>
                        @endif

                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
