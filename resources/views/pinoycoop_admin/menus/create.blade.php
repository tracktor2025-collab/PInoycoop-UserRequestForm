@extends('pinoycoop_admin.layouts.app', ['title' => 'Create Menu'])

@section('content')
    <div class="top">
        <h2>Create Menu</h2>
        <a class="btn btn-g" href="{{ route('pinoycoop.admin.menus.index') }}">Back to Menus</a>
    </div>
    <div class="card">
        <div class="head">New Menu</div>
        <div class="body">
            <form method="POST" action="{{ route('pinoycoop.admin.menus.store') }}">
                @csrf
                <div class="grid2">
                    <div>
                        <label>Name
                            <input type="text" name="name" value="{{ old('name') }}" required>
                        </label>
                    </div>
                    <div>
                        <label>Slug (optional)
                            <input type="text" name="slug" value="{{ old('slug') }}" placeholder="auto-generated from name">
                        </label>
                    </div>
                    <div>
                        <label>Location
                            <select name="location" required>
                                <option value="primary" {{ old('location', 'primary') === 'primary' ? 'selected' : '' }}>Primary</option>
                                <option value="footer" {{ old('location') === 'footer' ? 'selected' : '' }}>Footer</option>
                            </select>
                        </label>
                    </div>
                    <div>
                        <label>Active</label>
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                    </div>
                </div>
                <div style="margin-top:1rem;">
                    <button class="btn btn-p" type="submit">Create Menu</button>
                </div>
            </form>
        </div>
    </div>
@endsection

