<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin' }}</title>
    <style>
        :root { --p:#00a7e1; --d:#2b4889; --m:#49619a; --bg:#eef4f8; --w:#fff; --t:#20304f; }
        * { box-sizing:border-box; }
        body { margin:0; font-family:"Segoe UI",Arial,sans-serif; background:linear-gradient(145deg,#f5f9fc,var(--bg)); color:var(--t); }
        .shell { min-height:100vh; display:grid; grid-template-columns:240px 1fr; }
        .sidebar { background:linear-gradient(180deg,var(--d),var(--m)); color:#fff; padding:1.2rem 1rem; }
        .brand { font-weight:700; margin-bottom:1rem; }
        .label { color:rgba(255,255,255,.75); font-size:.75rem; text-transform:uppercase; letter-spacing:.8px; margin:1rem 0 .5rem; }
        .link { display:block; color:#fff; text-decoration:none; padding:.55rem .65rem; border-radius:10px; margin-bottom:.25rem; font-size:.92rem; transition:transform .18s ease, background .18s ease, box-shadow .18s ease; }
        .link:hover, .link.active { background:rgba(255,255,255,.16); box-shadow:0 10px 24px rgba(0,0,0,.12); transform:translateY(-1px); }
        .content { padding:1.2rem; }
        .card { background:var(--w); border:1px solid #dbe5ee; border-radius:14px; box-shadow:0 16px 40px rgba(32,48,79,.06); transition:transform .18s ease, box-shadow .18s ease, border-color .18s ease; }
        .card:hover { border-color:rgba(0,167,225,.35); box-shadow:0 22px 60px rgba(32,48,79,.10); transform:translateY(-2px); }
        .head { padding:.9rem 1rem; border-bottom:1px solid #e4ebf2; font-weight:600; }
        .body { padding:1rem; }
        .top { display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; gap:.6rem; }
        .btn { text-decoration:none; border:none; border-radius:10px; padding:.55rem .85rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:.35rem; transition:transform .15s ease, box-shadow .15s ease, filter .15s ease; }
        .btn:hover { transform:translateY(-1px); box-shadow:0 14px 30px rgba(32,48,79,.14); filter:saturate(1.05); }
        .btn:active { transform:translateY(0px); box-shadow:none; }
        .btn-p { background:var(--p); color:#fff; }
        .btn-g { background:#e9f5fb; color:#166286; }
        .btn-d { background:rgba(127, 143, 178, .18); color:#3d4d73; }
        .btn-sm { padding:.42rem .65rem; font-size:.85rem; border-radius:9px; }
        .alert { background:#e9f8ff; border:1px solid #b5e8fb; color:#0f5776; padding:.65rem .8rem; border-radius:8px; margin-bottom:1rem; }
        table { width:100%; border-collapse:collapse; }
        th,td { text-align:left; padding:.6rem .35rem; border-bottom:1px solid #edf2f7; font-size:.9rem; }
        th { color:#607993; }
        tbody tr { transition:background .15s ease, transform .15s ease; }
        tbody tr:hover { background:rgba(0, 167, 225, .06); transform:translateX(2px); }
        input, textarea, select { width:100%; padding:.58rem .62rem; border:1px solid #d2dee9; border-radius:8px; margin-top:.28rem; }
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
        @media (max-width:900px) { .shell{grid-template-columns:1fr;} .grid2{grid-template-columns:1fr;} .toast-container{top:.8rem; right:.8rem; left:.8rem;} .toast{justify-content:space-between;} }
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
