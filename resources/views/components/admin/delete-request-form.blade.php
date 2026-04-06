@props([
    'accessRequest',
    'redirectTo' => null,
])
@php
    /** @var \App\Models\AccessRequest $accessRequest */
    $target = $redirectTo ?? url()->full();
    $base = rtrim((string) config('app.url'), '/');
    if (! is_string($target) || $target === '' || ($base !== '' && ! str_starts_with($target, $base))) {
        $target = route('admin.dashboard');
    }
@endphp
<form action="{{ route('admin.request.destroy', $accessRequest) }}" method="POST" class="d-inline" onsubmit="return confirm('Permanently delete this access request? This cannot be undone.');">
    @csrf
    @method('DELETE')
    <input type="hidden" name="redirect_to" value="{{ $target }}">
    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
</form>
