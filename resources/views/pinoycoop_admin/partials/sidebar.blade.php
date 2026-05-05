<aside class="sidebar">
    <div class="brand">MASS-SPECC CMS</div>
    <div class="label">Overview</div>
    <a class="link {{ request()->routeIs('pinoycoop.admin.dashboard') ? 'active' : '' }}" href="{{ route('pinoycoop.admin.dashboard') }}">Dashboard</a>
    <a class="link" href="{{ route('landing') }}" target="_blank">View Website</a>

    <div class="label">Content</div>
    <a class="link {{ request()->routeIs('pinoycoop.admin.pages.*') ? 'active' : '' }}" href="{{ route('pinoycoop.admin.pages.index') }}">Pages</a>
    <a class="link {{ request()->routeIs('pinoycoop.admin.menus.*') ? 'active' : '' }}" href="{{ route('pinoycoop.admin.menus.index') }}">Menus</a>
    <a class="link {{ request()->routeIs('pinoycoop.admin.media.*') ? 'active' : '' }}" href="{{ route('pinoycoop.admin.media.index') }}">Media Library</a>

    <div class="label">Settings</div>
    <a class="link {{ request()->routeIs('pinoycoop.admin.settings.*') ? 'active' : '' }}" href="{{ route('pinoycoop.admin.settings.general') }}">General</a>
    <a class="link {{ request()->routeIs('pinoycoop.admin.users.*') ? 'active' : '' }}" href="{{ route('pinoycoop.admin.users.index') }}">Users</a>
</aside>

