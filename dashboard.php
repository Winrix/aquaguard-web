<?php
session_start();
if (!isset($_SESSION["usuario"])) { header("Location: login.html"); exit; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>AquaGuard - Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    @keyframes fade-in { 0% { opacity: 0; transform: translateY(8px);} 100% { opacity: 1; transform: translateY(0);} }
    .animate-fade-in { animation: fade-in 0.6s ease-out forwards; }
    body.dark-mode { background-color: #0f172a; color: #e5e7eb; }
    body.dark-mode .bg-white { background-color: #0b1220 !important; }
    body.dark-mode .text-gray-800 { color: #f3f4f6 !important; }
    body.dark-mode .text-gray-700 { color: #e5e7eb !important; }
    body.dark-mode .text-gray-600 { color: #cbd5e1 !important; }
    body.dark-mode .border-gray-200, body.dark-mode .border-gray-300 { border-color: #334155 !important; }
    body.dark-mode .bg-blue-50, body.dark-mode .bg-blue-100 { background-color: #0b1220 !important; }
    body.dark-mode .bg-blue-600 { background-color: #2563eb !important; }
    body.dark-mode .hover\:bg-blue-700:hover { background-color: #1d4ed8 !important; }
    body.dark-mode .status-ok{ border-color:#e5e7eb !important; }
    .status-warn{ border-color:#fbbf24 !important; }
    .status-alert{ border-color:#f87171 !important; }
    .status-warn-bg{ background-color: rgba(251,191,36,0.06) !important; }
    .status-alert-bg{ background-color: rgba(248,113,113,0.07) !important; }
    .dark-mode .status-warn-bg{ background-color: rgba(251,191,36,0.12) !important; }
    .dark-mode .status-alert-bg{ background-color: rgba(248,113,113,0.14) !important; }
  </style>
</head>
<body class="bg-blue-50 text-gray-800 min-h-screen">
  <header class="bg-white shadow-md">
    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
      <a href="index.html" class="flex items-center space-x-3 group">
        <img src="logo aquaguard.png" alt="AquaGuard" class="h-12 w-auto" />
        <h1 class="text-xl font-bold text-blue-600 group-hover:underline">AquaGuard</h1>
      </a>
      <nav class="flex items-center space-x-4">
        <a href="caracteristicas.html" class="text-gray-600 hover:text-blue-500">Características</a>
        <a href="nosotros.html" class="text-gray-600 hover:text-blue-500">¿Quiénes somos?</a>
        <span class="text-blue-600 font-semibold">Dashboard</span>
        <button id="btn-toggle-tema" class="p-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-700" aria-label="Cambiar tema" title="Cambiar tema">
          <svg id="icon-sun" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="4"/>
            <path d="M12 2v2m0 16v2m10-10h-2M4 12H2m15.364 6.364l-1.414-1.414M8.05 8.05 6.636 6.636m10.728 0-1.414 1.414M8.05 15.95l-1.414 1.414"/>
          </svg>
          <svg id="icon-moon" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 12.79A9 9 0 1 1 11.21 3a7 7 0 0 0 9.79 9.79z"/>
          </svg>
        </button>
        <a href="logout.php" class="text-gray-600 hover:text-blue-500">Salir</a>
      </nav>
    </div>
  </header>

  <main class="max-w-7xl mx-auto px-4 py-6 space-y-6">
    <div class="flex items-center justify-between">
      <h2 class="text-2xl font-bold">Hola, <?php echo htmlspecialchars($_SESSION["usuario"]); ?> 👋</h2>
      <div class="text-sm text-gray-600">Desde última anomalía: <span id="desde-anomalia">—</span></div>
    </div>

    <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
      <div id="card-cloro" class="bg-white rounded-xl shadow p-5 animate-fade-in border border-gray-200">
        <div class="text-sm text-gray-600">Nivel de cloro (ppm)</div>
        <div class="text-3xl font-semibold" id="val-cloro">—</div>
      </div>
      <div id="card-ph" class="bg-white rounded-xl shadow p-5 animate-fade-in border border-gray-200">
        <div class="text-sm text-gray-600">pH</div>
        <div class="text-3xl font-semibold" id="val-ph">—</div>
      </div>
      <div id="card-humedad" class="bg-white rounded-xl shadow p-5 animate-fade-in border border-gray-200">
        <div class="text-sm text-gray-600">Humedad (%)</div>
        <div class="text-3xl font-semibold" id="val-humedad">—</div>
      </div>
      <div id="card-temperatura" class="bg-white rounded-xl shadow p-5 animate-fade-in border border-gray-200">
        <div class="text-sm text-gray-600">Temperatura (°C)</div>
        <div class="text-3xl font-semibold" id="val-temperatura">—</div>
      </div>
    </section>

    <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="bg-white rounded-xl shadow p-5">
        <div class="flex items-center justify-between mb-3">
          <h3 class="font-semibold">Cloro y pH</h3>
          <div class="text-xs text-gray-500" id="rango-1">—</div>
        </div>
        <div class="h-64"><canvas id="chart-quimica"></canvas></div>
      </div>
      <div class="bg-white rounded-xl shadow p-5">
        <div class="flex items-center justify-between mb-3">
          <h3 class="font-semibold">Humedad y Temperatura</h3>
          <div class="text-xs text-gray-500" id="rango-2">—</div>
        </div>
        <div class="h-64"><canvas id="chart-ambiente"></canvas></div>
      </div>
    </section>
    
    <!-- Tabla de rangos ideales y alertas -->
    <section class="bg-white rounded-xl shadow p-5">
      <div class="flex items-center gap-2 mb-4">
        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        <h3 class="text-lg font-semibold">Resumen general para tu sistema AquaGuard</h3>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="text-left text-sm font-semibold text-gray-700">
            <tr>
              <th class="py-3 pr-6">Parámetro</th>
              <th class="py-3 pr-6">Rango ideal</th>
              <th class="py-3 pr-6">Alerta baja</th>
              <th class="py-3 pr-6">Alerta alta</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 text-sm">
            <tr>
              <td class="py-3 pr-6">Temperatura</td>
              <td class="py-3 pr-6"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-300 text-green-900 dark:bg-green-900/60 dark:text-green-200">26 – 28 °C</span></td>
              <td class="py-3 pr-6"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-300 text-red-900 dark:bg-red-900/60 dark:text-red-200">&lt; 25 °C</span></td>
              <td class="py-3 pr-6"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-300 text-red-900 dark:bg-red-900/60 dark:text-red-200">&gt; 30 °C</span></td>
            </tr>
            <tr>
              <td class="py-3 pr-6">pH</td>
              <td class="py-3 pr-6"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-300 text-green-900 dark:bg-green-900/60 dark:text-green-200">7.2 – 7.6</span></td>
              <td class="py-3 pr-6"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-300 text-red-900 dark:bg-red-900/60 dark:text-red-200">&lt; 7.0</span></td>
              <td class="py-3 pr-6"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-300 text-red-900 dark:bg-red-900/60 dark:text-red-200">&gt; 8.0</span></td>
            </tr>
            <tr>
              <td class="py-3 pr-6">Cloro</td>
              <td class="py-3 pr-6"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-300 text-green-900 dark:bg-green-900/60 dark:text-green-200">1.0 – 3.0 ppm</span></td>
              <td class="py-3 pr-6"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-300 text-red-900 dark:bg-red-900/60 dark:text-red-200">&lt; 1.0 ppm</span></td>
              <td class="py-3 pr-6"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-300 text-red-900 dark:bg-red-900/60 dark:text-red-200">&gt; 3.5 ppm</span></td>
            </tr>
            <tr>
              <td class="py-3 pr-6">Humedad</td>
              <td class="py-3 pr-6"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-300 text-green-900 dark:bg-green-900/60 dark:text-green-200">50 – 60 %</span></td>
              <td class="py-3 pr-6"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-300 text-red-900 dark:bg-red-900/60 dark:text-red-200">&lt; 40 %</span></td>
              <td class="py-3 pr-6"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-300 text-red-900 dark:bg-red-900/60 dark:text-red-200">&gt; 70 %</span></td>
            </tr>
          </tbody>
        </table>
      </div>

      <p class="mt-4 text-sm text-gray-600">
        Si alguna medición no está en el rango ideal, por favor comuníquese con nosotros a la brevedad.
        <a href="index.html#contacto" class="text-blue-600 hover:text-blue-700 font-medium">Contacto</a>
      </p>
    </section>

  </main>

  <script>
    function setThemeIcon(isDark) {
      const sun = document.getElementById('icon-sun');
      const moon = document.getElementById('icon-moon');
      if (!sun || !moon) return;
      sun.classList.toggle('hidden', isDark);
      moon.classList.toggle('hidden', !isDark);
    }
    (function initTheme() {
      try {
        const saved = localStorage.getItem('theme');
        const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        if (saved === 'dark' || (!saved && prefersDark)) {
          document.body.classList.add('dark-mode');
        }
      } catch (_) {}
      setThemeIcon(document.body.classList.contains('dark-mode'));
    })();
    const btnToggleTema = document.getElementById('btn-toggle-tema');
    if (btnToggleTema) {
      btnToggleTema.addEventListener('click', () => {
        const isDark = document.body.classList.toggle('dark-mode');
        setThemeIcon(isDark);
        try { localStorage.setItem('theme', isDark ? 'dark' : 'light'); } catch (_) {}
      });
    }
  </script>

  <script>
    const desdeAnomaliaEl = document.getElementById('desde-anomalia');
    const valCloro = document.getElementById('val-cloro');
    const valPh = document.getElementById('val-ph');
    const valHumedad = document.getElementById('val-humedad');
    const valTemp = document.getElementById('val-temperatura');
    const rango1 = document.getElementById('rango-1');
    const rango2 = document.getElementById('rango-2');

    let ultimaDesdeISO = null;
    let chartQuimica = null;
    let chartAmbiente = null;

    function setStatus(cardId, valueElId, value, bounds) {
      const card = document.getElementById(cardId);
      const valueEl = document.getElementById(valueElId);
      if (!card || !valueEl || typeof value !== 'number' || isNaN(value)) return;

      const isAlert = value < bounds.alertLow || value > bounds.alertHigh;
      const isWarn = !isAlert && (value < bounds.warnLow || value > bounds.warnHigh);

      // Reset classes
      card.classList.remove('status-ok','status-warn','status-alert','status-warn-bg','status-alert-bg','ring-2','ring-red-300','ring-amber-300');
      valueEl.classList.remove('text-red-600','text-amber-600');

      if (isAlert) {
        card.classList.add('status-alert','status-alert-bg','ring-2','ring-red-300');
        valueEl.classList.add('text-red-600');
      } else if (isWarn) {
        card.classList.add('status-warn','status-warn-bg','ring-2','ring-amber-300');
        valueEl.classList.add('text-amber-600');
      } else {
        card.classList.add('status-ok');
      }
    }

    async function cargarDesdeAnomalia() {
      const r = await fetch('api/ultima_anomalia.php');
      if (!r.ok) return;
      const j = await r.json();
      ultimaDesdeISO = j.timestamp;
      const d = new Date(ultimaDesdeISO);
      desdeAnomaliaEl.textContent = isNaN(d) ? '—' : d.toLocaleString();
    }

    async function cargarDatos() {
      if (!ultimaDesdeISO) return;
      const r = await fetch('api/datos_sensores.php?since=' + encodeURIComponent(ultimaDesdeISO));
      if (!r.ok) return;
      const j = await r.json();
      const labels = j.map(p => new Date(p.t).toLocaleTimeString());
      const serieCloro = j.map(p => p.cloro);
      const seriePh = j.map(p => p.ph);
      const serieHum = j.map(p => p.humedad);
      const serieTmp = j.map(p => p.temperatura);

      if (serieCloro.length && typeof serieCloro[serieCloro.length-1] === 'number') valCloro.textContent = serieCloro[serieCloro.length-1].toFixed(2);
      if (seriePh.length && typeof seriePh[seriePh.length-1] === 'number') valPh.textContent = seriePh[seriePh.length-1].toFixed(2);
      if (serieHum.length && typeof serieHum[serieHum.length-1] === 'number') valHumedad.textContent = Math.round(serieHum[serieHum.length-1]);
      if (serieTmp.length && typeof serieTmp[serieTmp.length-1] === 'number') valTemp.textContent = serieTmp[serieTmp.length-1].toFixed(1);

      // Marcar estado según rangos (ideal y alerta)
      setStatus('card-cloro','val-cloro',
        (serieCloro.length && typeof serieCloro[serieCloro.length-1] === 'number') ? serieCloro[serieCloro.length-1] : NaN,
        { warnLow: 1.0, warnHigh: 3.0, alertLow: 1.0, alertHigh: 3.5 }
      );
      setStatus('card-ph','val-ph',
        (seriePh.length && typeof seriePh[seriePh.length-1] === 'number') ? seriePh[seriePh.length-1] : NaN,
        { warnLow: 7.2, warnHigh: 7.6, alertLow: 7.0, alertHigh: 8.0 }
      );
      setStatus('card-humedad','val-humedad',
        (serieHum.length && typeof serieHum[serieHum.length-1] === 'number') ? serieHum[serieHum.length-1] : NaN,
        { warnLow: 50, warnHigh: 60, alertLow: 40, alertHigh: 70 }
      );
      setStatus('card-temperatura','val-temperatura',
        (serieTmp.length && typeof serieTmp[serieTmp.length-1] === 'number') ? serieTmp[serieTmp.length-1] : NaN,
        { warnLow: 26, warnHigh: 28, alertLow: 25, alertHigh: 30 }
      );

      if (!chartQuimica) {
        const ctx1 = document.getElementById('chart-quimica');
        chartQuimica = new Chart(ctx1, {
          type: 'line',
          data: { labels, datasets: [
            { label: 'Cloro', data: serieCloro, borderColor: '#2563eb', backgroundColor: 'rgba(37,99,235,0.2)', tension: 0.3 },
            { label: 'pH', data: seriePh, borderColor: '#f59e0b', backgroundColor: 'rgba(245,158,11,0.2)', tension: 0.3 }
          ] },
          options: { responsive: true, maintainAspectRatio: false, scales: { x: { ticks: { maxTicksLimit: 6 } } } }
        });
      } else {
        chartQuimica.data.labels = labels;
        chartQuimica.data.datasets[0].data = serieCloro;
        chartQuimica.data.datasets[1].data = seriePh;
        chartQuimica.update();
      }

      if (!chartAmbiente) {
        const ctx2 = document.getElementById('chart-ambiente');
        chartAmbiente = new Chart(ctx2, {
          type: 'line',
          data: { labels, datasets: [
            { label: 'Humedad', data: serieHum, borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,0.2)', tension: 0.3 },
            { label: 'Temperatura', data: serieTmp, borderColor: '#ef4444', backgroundColor: 'rgba(239,68,68,0.2)', tension: 0.3 }
          ] },
          options: { responsive: true, maintainAspectRatio: false, scales: { x: { ticks: { maxTicksLimit: 6 } } } }
        });
      } else {
        chartAmbiente.data.labels = labels;
        chartAmbiente.data.datasets[0].data = serieHum;
        chartAmbiente.data.datasets[1].data = serieTmp;
        chartAmbiente.update();
      }

      if (j.length) {
        const first = new Date(j[0].t).toLocaleString();
        const last = new Date(j[j.length-1].t).toLocaleString();
        rango1.textContent = first + ' — ' + last;
        rango2.textContent = first + ' — ' + last;
      }
    }

    async function init() {
      await cargarDesdeAnomalia();
      await cargarDatos();
      setInterval(cargarDatos, 10000);
    }
    init();
  </script>
</body>
</html>