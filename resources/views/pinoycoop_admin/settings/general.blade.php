@extends('pinoycoop_admin.layouts.app', ['title' => 'General Settings'])

@section('content')
    <div class="top">
        <h2>General Settings</h2>
    </div>
    <div class="card">
        <div class="head">Site Configuration</div>
        <div class="body">
            <form method="POST" action="{{ route('pinoycoop.admin.settings.general.update') }}">
                @csrf
                @method('PUT')
                <div class="grid2">
                    <div>
                        <label>Site Name
                            <input type="text" name="site_name" value="{{ old('site_name', $settings['site_name'] ?? '') }}" required>
                        </label>
                    </div>
                    <div>
                        <label>Site Tagline
                            <input type="text" name="site_tagline" value="{{ old('site_tagline', $settings['site_tagline'] ?? '') }}">
                        </label>
                    </div>
                </div>
                <div style="margin-top:1rem;">
                    <button class="btn btn-p" type="submit">Save Settings</button>
                </div>
            </form>
        </div>
    </div>
@endsection
