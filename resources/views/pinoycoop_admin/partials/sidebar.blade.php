<aside class="sidebar">
    <a class="brand" href="{{ route('pinoycoop.admin.dashboard') }}" aria-label="MASS-SPECC CMS dashboard">
        <img src="{{ asset('pinooycoop/images/CMS-logo.png') }}" alt="MASS-SPECC Cooperative Development Center">
    </a>
    <div class="label">Overview</div>
    <a class="link {{ request()->routeIs('pinoycoop.admin.dashboard') ? 'active' : '' }}" href="{{ route('pinoycoop.admin.dashboard') }}">
        <span class="link-icon">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 13h8V3H3z"></path><path d="M13 21h8V11h-8z"></path><path d="M13 3h8v6h-8z"></path><path d="M3 21h8v-6H3z"></path></svg>
        </span>
        <span>Dashboard</span>
    </a>
    <a class="link" href="{{ route('landing') }}" target="_blank">
        <span class="link-icon">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><path d="M15 3h6v6"></path><path d="M10 14 21 3"></path></svg>
        </span>
        <span>View Website</span>
    </a>

    <div class="label">Content</div>
    <a class="link {{ request()->routeIs('pinoycoop.admin.pages.*') ? 'active' : '' }}" href="{{ route('pinoycoop.admin.pages.index') }}">
        <span class="link-icon">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><path d="M14 2v6h6"></path><path d="M16 13H8"></path><path d="M16 17H8"></path><path d="M10 9H8"></path></svg>
        </span>
        <span>Pages</span>
    </a>
    <a class="link {{ request()->routeIs('pinoycoop.admin.page-builder.*') ? 'active' : '' }}" href="{{ route('pinoycoop.admin.page-builder.index') }}">
        <span class="link-icon">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20h9"></path><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"></path><path d="M3 7h8"></path><path d="M3 11h6"></path></svg>
        </span>
        <span>Page Builder</span>
    </a>
    <a class="link {{ request()->routeIs('pinoycoop.admin.media.*') ? 'active' : '' }}" href="{{ route('pinoycoop.admin.media.index') }}">
        <span class="link-icon">
            <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><path d="M21 15 16 10 5 21"></path></svg>
        </span>
        <span>Media Library</span>
    </a>
    <a class="link {{ request()->routeIs('pinoycoop.admin.messages.*') ? 'active' : '' }}" href="{{ route('pinoycoop.admin.messages.index') }}">
        <span class="link-icon">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 15a4 4 0 0 1-4 4H7l-4 4V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"></path></svg>
        </span>
        <span>Messages</span>
    </a>

    <div class="label">Settings</div>
    <a class="link {{ request()->routeIs('pinoycoop.admin.settings.general') ? 'active' : '' }}" href="{{ route('pinoycoop.admin.settings.general') }}">
        <span class="link-icon">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 15.5A3.5 3.5 0 1 0 12 8a3.5 3.5 0 0 0 0 7.5z"></path><path d="M19.4 15a1.8 1.8 0 0 0 .36 1.98l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06A1.8 1.8 0 0 0 15 19.4a1.8 1.8 0 0 0-1 .6 1.8 1.8 0 0 0-.4 1.1V21a2 2 0 1 1-4 0v-.09A1.8 1.8 0 0 0 8.5 19.3a1.8 1.8 0 0 0-1.98.36l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.8 1.8 0 0 0 4.6 15a1.8 1.8 0 0 0-.6-1 1.8 1.8 0 0 0-1.1-.4H3a2 2 0 1 1 0-4h.09A1.8 1.8 0 0 0 4.7 8.5a1.8 1.8 0 0 0-.36-1.98l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.8 1.8 0 0 0 9 4.6a1.8 1.8 0 0 0 1-.6 1.8 1.8 0 0 0 .4-1.1V3a2 2 0 1 1 4 0v.09A1.8 1.8 0 0 0 15.5 4.7a1.8 1.8 0 0 0 1.98-.36l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.8 1.8 0 0 0 19.4 9c.2.35.55.6 1 .6h.6a2 2 0 1 1 0 4h-.6a1.8 1.8 0 0 0-1 .4z"></path></svg>
        </span>
        <span>General</span>
    </a>
    <a class="link {{ request()->routeIs('pinoycoop.admin.settings.home-counter') ? 'active' : '' }}" href="{{ route('pinoycoop.admin.settings.home-counter') }}">
        <span class="link-icon">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 3v18h18"></path><path d="M18 17V9"></path><path d="M13 17V5"></path><path d="M8 17v-3"></path></svg>
        </span>
        <span>Home Counter</span>
    </a>
    <a class="link {{ request()->routeIs('pinoycoop.admin.users.*') ? 'active' : '' }}" href="{{ route('pinoycoop.admin.users.index') }}">
        <span class="link-icon">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
        </span>
        <span>Users</span>
    </a>
</aside>
