<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin' }}</title>
    <style>
        :root { --p:#00a7e1; --d:#2b4889; --m:#49619a; --bg:#eef4f8; --w:#fff; --t:#20304f; --line:#dbe5ee; }
        * { box-sizing:border-box; }
        body { margin:0; font-family:"Segoe UI",Arial,sans-serif; background:radial-gradient(circle at top right,rgba(0,167,225,.11),transparent 28rem), linear-gradient(145deg,#f8fbfd,var(--bg)); color:var(--t); }
        .shell { min-height:100vh; display:grid; grid-template-columns:260px 1fr; }
        .sidebar { position:sticky; top:0; align-self:start; min-height:100vh; background:linear-gradient(180deg,var(--d),var(--m)); color:#fff; padding:1rem .9rem; box-shadow:12px 0 32px rgba(32,48,79,.12); }
        .brand { display:flex; align-items:center; min-height:88px; padding:.35rem .35rem .95rem; margin-bottom:.65rem; border-bottom:1px solid rgba(255,255,255,.16); }
        .brand img { width:100%; max-width:210px; height:auto; object-fit:contain; }
        .label { color:rgba(255,255,255,.72); font-size:.7rem; text-transform:uppercase; letter-spacing:1px; margin:1.05rem .6rem .45rem; font-weight:700; }
        .link { position:relative; display:flex; align-items:center; gap:.65rem; color:#fff; text-decoration:none; padding:.68rem .72rem; border-radius:8px; margin-bottom:.28rem; font-size:.93rem; line-height:1.2; transition:transform .18s ease, background .18s ease, box-shadow .18s ease; }
        .link-icon { width:1.9rem; height:1.9rem; flex:0 0 1.9rem; display:grid; place-items:center; border-radius:8px; background:rgba(255,255,255,.11); color:#fff; }
        .link-icon svg { width:1.05rem; height:1.05rem; stroke:currentColor; stroke-width:2; fill:none; stroke-linecap:round; stroke-linejoin:round; }
        .link:hover, .link.active { background:rgba(255,255,255,.16); box-shadow:0 10px 24px rgba(0,0,0,.12); transform:translateY(-1px); }
        .link.active::before { content:""; position:absolute; left:-.9rem; top:.55rem; bottom:.55rem; width:4px; border-radius:0 999px 999px 0; background:var(--p); }
        .link:hover .link-icon, .link.active .link-icon { background:rgba(0,167,225,.32); }
        .content { padding:1.35rem; min-width:0; }
        .card { background:rgba(255,255,255,.96); border:1px solid var(--line); border-radius:8px; box-shadow:0 16px 40px rgba(32,48,79,.06); overflow:hidden; transition:transform .18s ease, box-shadow .18s ease, border-color .18s ease; }
        .card:hover { border-color:rgba(0,167,225,.35); box-shadow:0 22px 60px rgba(32,48,79,.10); transform:translateY(-2px); }
        .head { padding:.95rem 1rem; border-bottom:1px solid #e4ebf2; font-weight:700; background:linear-gradient(180deg,#fff,#f8fbfd); }
        .body { padding:1rem; }
        .top { display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; gap:.6rem; }
        .top h2, .top h1 { margin:0; }
        .btn { text-decoration:none; border:none; border-radius:8px; padding:.6rem .9rem; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:.4rem; transition:transform .15s ease, box-shadow .15s ease, filter .15s ease; }
        .btn:hover { transform:translateY(-1px); box-shadow:0 14px 30px rgba(32,48,79,.14); filter:saturate(1.05); }
        .btn:active { transform:translateY(0px); box-shadow:none; }
        .btn-p { background:var(--p); color:#fff; }
        .btn-g { background:#e9f5fb; color:#166286; }
        .btn-d { background:rgba(127, 143, 178, .18); color:#3d4d73; }
        .btn-sm { padding:.42rem .65rem; font-size:.85rem; border-radius:8px; }
        .alert { background:#e9f8ff; border:1px solid #b5e8fb; color:#0f5776; padding:.65rem .8rem; border-radius:8px; margin-bottom:1rem; }
        table { width:100%; border-collapse:collapse; }
        th,td { text-align:left; padding:.72rem .5rem; border-bottom:1px solid #edf2f7; font-size:.9rem; vertical-align:middle; }
        th { color:#607993; font-size:.76rem; letter-spacing:.45px; text-transform:uppercase; }
        tbody tr { transition:background .15s ease, transform .15s ease; }
        tbody tr:hover { background:rgba(0, 167, 225, .06); transform:translateX(2px); }
        input, textarea, select { width:100%; padding:.68rem .72rem; border:1px solid #d2dee9; border-radius:8px; margin-top:.28rem; background:#fbfdff; color:var(--t); }
        textarea { min-height:120px; resize:vertical; }
        .grid2 { display:grid; grid-template-columns:1fr 1fr; gap:.9rem; }
        input:focus, textarea:focus, select:focus { outline:none; border-color:rgba(0,167,225,.55); box-shadow:0 0 0 4px rgba(0,167,225,.12); }
        .toast-container { position:fixed; top:1.2rem; right:1.2rem; z-index:9999; display:flex; flex-direction:column; gap:.6rem; pointer-events:none; }
        .toast { padding:1rem 1.2rem; border-radius:10px; box-shadow:0 10px 30px rgba(32,48,79,.2); display:flex; align-items:center; gap:.6rem; animation:slideIn .3s ease, slideOut .3s ease 4.7s forwards; pointer-events:auto; font-weight:500; font-size:.95rem; }
        .toast-success { background:#e8f9f1; color:#0d6d42; border-left:4px solid #20c997; }
        .toast-error { background:#fef2f2; color:#7f1d1d; border-left:4px solid #ef4444; }
        .toast-icon { font-size:1.3rem; }
        @keyframes slideIn { from{transform:translateX(400px); opacity:0;} to{transform:translateX(0); opacity:1;} }
        @keyframes slideOut { from{transform:translateX(0); opacity:1;} to{transform:translateX(400px); opacity:0;} }
        @media (max-width:900px) { .shell{grid-template-columns:1fr;} .sidebar{position:relative; min-height:auto;} .brand{min-height:auto;} .brand img{max-width:190px;} .grid2{grid-template-columns:1fr;} .toast-container{top:.8rem; right:.8rem; left:.8rem;} .toast{justify-content:space-between;} }
    </style>
</head>
<body>
    <div class="shell">
        @include('pinoycoop_admin.partials.sidebar')
        <main class="content">
            @yield('content')
        </main>
    </div>

    <!-- Toast Notification Container -->
    <div class="toast-container" id="toastContainer"></div>

    <script>
        function showNotification(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            const isSuccess = type === 'success';
            
            toast.className = `toast ${isSuccess ? 'toast-success' : 'toast-error'}`;
            toast.innerHTML = `
                <span class="toast-icon">${isSuccess ? '✓' : '✕'}</span>
                <span>${message}</span>
            `;
            
            container.appendChild(toast);
            
            // Remove toast after animation completes
            setTimeout(() => toast.remove(), 5000);
        }

        // Show notification if there's a session message
        @if (session('status'))
            showNotification('{{ session('status') }}', 'success');
        @endif
        @if (session('error'))
            showNotification('{{ session('error') }}', 'error');
        @endif
        @if ($errors->any())
            @php
                $firstError = $errors->first();
            @endphp
            showNotification('{{ $firstError }}', 'error');
        @endif
    </script>
</body>
</html>
