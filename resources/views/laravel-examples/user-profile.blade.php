@extends('layouts.user_type.auth')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            @php
                $perPageOptions = [10, 25, 50, 100];
                // Define CSV files and their identifiers (from public/storage folder)
                $datasets = [
                    'dana' => [
                        'path' => base_path('resources/views/data/data_labeled.csv'),
                        'title' => 'Dana',
                        'anchor' => 'dana',
                    ],
                    'gopay' => [
                        'path' => base_path('resources/views/data/data_labeledgopay.csv'),
                        'title' => 'GoPay',
                        'anchor' => 'gopay',
                    ],
                    'shopee' => [
                        'path' => base_path('resources/views/data/data_labeledshopee.csv'),
                        'title' => 'ShopeePay',
                        'anchor' => 'shopeepay',
                    ],
                ];
                // Which columns to display
                $displayCols = ['clean_text', 'label_auto'];

                // Build paginators and headers
                foreach ($datasets as $key => $info) {
                    $allRows = file_exists($info['path']) ? array_map('str_getcsv', file($info['path'])) : [];
                    ${"header_{$key}"} = count($allRows) > 1 ? array_shift($allRows) : [];

                    // Determine display column indices
                    ${"displayIdxs_{$key}"} = [];
                    foreach (${"header_{$key}"} as $i => $col) {
                        if (in_array($col, $displayCols)) {
                            ${"displayIdxs_{$key}"}[] = $i;
                        }
                    }

                    $perPageKey = 'per_page_' . $key;
                    $pageKey = 'page_' . $key;
                    ${"perPage_{$key}"} = (int) request()->get($perPageKey, 10);
                    ${"currentPage_{$key}"} = (int) request()->get($pageKey, 1);

                    ${"paginator_{$key}"} = new \Illuminate\Pagination\LengthAwarePaginator(
                        array_slice($allRows, (${"currentPage_{$key}"} - 1) * ${"perPage_{$key}"}, ${"perPage_{$key}"}),
                        count($allRows),
                        ${"perPage_{$key}"},
                        ${"currentPage_{$key}"},
                        [
                            'path' => url()->current(),
                            'pageName' => $pageKey,
                            'query' => request()->except($pageKey),
                        ],
                    );
                }
            @endphp

            @foreach ($datasets as $key => $info)
                @php
                    $header = ${"header_{$key}"};
                    $idxs = ${"displayIdxs_{$key}"};
                    $paginator = ${"paginator_{$key}"};
                    $perPage = ${"perPage_{$key}"};
                    $anchor = $info['anchor'];
                    $title = $info['title'];
                @endphp
                <div class="col-12 mb-4" id="{{ $anchor }}">
                    <div class="card shadow-sm border-0 rounded-2">
                        <div class="card-header bg-white border-bottom-0 pb-0">
                            <h6 class="mb-0">Data Sentimen {{ $title }} ({{ basename($info['path']) }})</h6>
                        </div>
                        <div class="card-body px-0 pt-2 pb-3">
                            <div class="table-responsive">
                                @if (count($idxs))
                                    <table class="table table-striped table-hover align-middle mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                @foreach ($idxs as $i)
                                                    <th class="text-uppercase text-secondary text-sm fw-bold">
                                                        {{ $header[$i] }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($paginator as $row)
                                                <tr>
                                                    @foreach ($idxs as $i)
                                                        <td class="text-sm text-dark">{{ $row[$i] }}</td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <p class="text-center text-muted py-4">
                                        Kolom untuk clean_text dan label_auto tidak ditemukan.
                                    </p>
                                @endif
                            </div>

                            @if (count($idxs))
                                <div class="d-flex justify-content-between align-items-center px-4 mt-3">
                                    <div class="d-flex align-items-center text-sm">
                                        <label class="me-2 mb-0">Items per page</label>
                                        <form method="get" class="d-flex">
                                            <select name="per_page_{{ $key }}" onchange="this.form.submit()"
                                                class="form-select form-select-sm me-3" style="width:auto">
                                                @foreach ($perPageOptions as $n)
                                                    <option value="{{ $n }}"
                                                        @if ($perPage == $n) selected @endif>
                                                        {{ $n }}</option>
                                                @endforeach
                                            </select>
                                            @foreach (request()->except(['per_page_' . $key, 'page_' . $key]) as $k => $v)
                                                <input type="hidden" name="{{ $k }}"
                                                    value="{{ $v }}">
                                            @endforeach
                                        </form>
                                        <span class="text-secondary">
                                            {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} of
                                            {{ $paginator->total() }}
                                        </span>
                                    </div>
                                    <nav aria-label="Page navigation" class="mt-2">
                                        <ul
                                            class="pagination pagination-sm justify-content-center shadow-sm bg-white rounded p-2">
                                            <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                                                <a class="page-link" href="{{ $paginator->url(1) }}#{{ $anchor }}"
                                                    aria-label="First"><span aria-hidden="true">&laquo;&laquo;</span></a>
                                            </li>
                                            <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                                                <a class="page-link"
                                                    href="{{ $paginator->previousPageUrl() }}#{{ $anchor }}"
                                                    aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>
                                            </li>
                                            @for ($i = max(1, $paginator->currentPage() - 2); $i <= min($paginator->lastPage(), $paginator->currentPage() + 2); $i++)
                                                <li
                                                    class="page-item {{ $paginator->currentPage() == $i ? 'active' : '' }}">
                                                    <a class="page-link"
                                                        href="{{ $paginator->url($i) }}#{{ $anchor }}">{{ $i }}</a>
                                                </li>
                                            @endfor
                                            <li class="page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
                                                <a class="page-link"
                                                    href="{{ $paginator->nextPageUrl() }}#{{ $anchor }}"
                                                    aria-label="Next"><span aria-hidden="true">&raquo;</span></a>
                                            </li>
                                            <li class="page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
                                                <a class="page-link"
                                                    href="{{ $paginator->url($paginator->lastPage()) }}#{{ $anchor }}"
                                                    aria-label="Last"><span aria-hidden="true">&raquo;&raquo;</span></a>
                                            </li>
                                        </ul>
                                    </nav>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
@endsection
