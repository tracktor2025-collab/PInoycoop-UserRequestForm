@props([
    'column',
    'label',
    'sort',
    'direction',
])
@php
    /** @var string $column */
    /** @var string $label */
    /** @var string $sort */
    /** @var string $direction */
    $routeName = \Illuminate\Support\Facades\Route::currentRouteName();
    $baseQuery = request()->except('page');
    $isActive = $sort === $column;
    $defaultForColumn = $column === 'created_at' ? 'desc' : 'asc';
    $newDirection = $isActive ? ($direction === 'asc' ? 'desc' : 'asc') : $defaultForColumn;
    $href = route($routeName, array_merge($baseQuery, ['sort' => $column, 'direction' => $newDirection]));
@endphp
<th scope="col">
    <a href="{{ $href }}" class="table-sort-link">{{ $label }}@if($isActive)<span class="ms-1 text-primary" aria-hidden="true">{{ $direction === 'asc' ? '↑' : '↓' }}</span>@endif</a>
</th>
