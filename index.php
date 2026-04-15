<!doctype html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
  <title>MX INTRANET | PORTAL</title>

  <link rel="stylesheet" href="/assets/css/tailwind.build.css">

  <style>
    body {
      background: radial-gradient(circle at top, #1a1a1a, #0a0a0a);
    }
  </style>

  <script>
    const SERVICES = {
      grafana: "status-grafana",
      prometheus: "status-prometheus"
    };

    function setStatus(el, online) {
      if (!el) return;

      el.classList.remove("text-gray-500", "text-green-400", "text-red-400");

      if (online === "1") {
        el.classList.add("text-green-400");
        el.textContent = "●";
      } else {
        el.classList.add("text-red-400");
        el.textContent = "●";
      }
    }

    async function updateStatus() {
      try {
        const res = await fetch("/api/prometheus/api/v1/query?query=probe_success");
        const data = await res.json();

        const results = data?.data?.result || [];

        results.forEach((item) => {
          const service = item?.metric?.service;
          const value = item?.value?.[1];

          const elId = SERVICES[service];
          if (!elId) return;

          const el = document.getElementById(elId);
          setStatus(el, value);
        });

      } catch (err) {
        console.error("Status Update Fehler:", err);
      }
    }

    updateStatus();
    setInterval(updateStatus, 5000);
  </script>
</head>

<body class="text-white min-h-screen p-10">

  <div class="mb-10">
    <h1 class="text-4xl font-bold text-orange-500 tracking-tight">
      MX Intranet
    </h1>
    <p class="text-gray-400 mt-2">Maralex Control Center</p>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <a href="https://grafana.local"
       class="group bg-zinc-900 border border-zinc-800 rounded-2xl p-6
              hover:border-orange-500 hover:shadow-lg hover:shadow-orange-500/10
              transition-all duration-200">

      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <span class="text-2xl">📊</span>
          <div>
            <h2 class="text-xl font-bold group-hover:text-orange-400 transition">
              Grafana
            </h2>
            <p class="text-gray-400 text-sm">Monitoring & Dashboards</p>
          </div>
        </div>

        <span id="status-grafana" class="text-gray-500 text-xl">●</span>
      </div>

      <div class="mt-4 border-t border-zinc-800 pt-4 text-sm text-gray-300 space-y-1">
        <p>Haupt Monitoring Plattform</p>
        <p>Systemmetriken, Container, Logs</p>
        <p class="text-orange-400 mt-2">
          Connection: 10.66.66.66/24 · 10.0.0.0/16
        </p>
      </div>

    </a>

    <a href="https://prometheus.local"
       class="group bg-zinc-900 border border-zinc-800 rounded-2xl p-6
              hover:border-orange-500 hover:shadow-lg hover:shadow-orange-500/10
              transition-all duration-200">

      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <span class="text-2xl">📈</span>
          <div>
            <h2 class="text-xl font-bold group-hover:text-orange-400 transition">
              Prometheus
            </h2>
            <p class="text-gray-400 text-sm">Metrics & Time Series</p>
          </div>
        </div>

        <span id="status-prometheus" class="text-gray-500 text-xl">●</span>
      </div>

      <div class="mt-4 border-t border-zinc-800 pt-4 text-sm text-gray-300 space-y-1">
        <p>Time Series Datenbank</p>
        <p>Scraping & Metrics Storage</p>
        <p class="text-orange-400 mt-2">
          Connection: 10.66.66.66/24 · 10.0.0.0/16
        </p>
      </div>

    </a>

  </div>

</body>
</html>