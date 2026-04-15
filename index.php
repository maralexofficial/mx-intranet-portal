<!doctype html>
<html lang="de">
  <head>
    <meta charset="UTF-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, maximum-scale=1.0"
    />
    <title>MX INTRANET</title>
    <link rel="stylesheet" href="/assets/css/tailwind.build.css">
    <script>
      async function updateStatus() {
        try {
          const res = await fetch(
            "/api/prometheus/api/v1/query?query=probe_success",
          );
          const data = await res.json();

          data.data.result.forEach((item) => {
            const service = item.metric.service;
            const value = item.value[1];

            let el;

            if (service === "grafana") {
              el = document.getElementById("status-grafana");
            } else if (service === "prometheus") {
              el = document.getElementById("status-prometheus");
            }

            if (el) {
              el.classList.remove(
                "text-gray-500",
                "text-green-400",
                "text-red-400",
              );
              el.classList.add(
                value === "1" ? "text-green-400" : "text-red-400",
              );
            }
          });
        } catch (err) {
          console.error("Status Update Fehler:", err);
        }
      }

      updateStatus();
      setInterval(updateStatus, 5000);
    </script>
    <style>
      body {
        background: radial-gradient(circle at top, #1a1a1a, #0a0a0a);
      }
    </style>
  </head>
  <body class="text-white min-h-screen p-10">
    <div class="mb-10">
      <h1 class="text-4xl font-bold text-orange-500">MX Intranet</h1>
      <p class="text-gray-400 mt-2">Maralex Control Center</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <a
        href="https://grafana.local"
        class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 hover:border-orange-500 transition"
      >
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <span class="text-2xl">📊</span>
            <div>
              <h2 class="text-xl font-bold">Grafana</h2>
              <p class="text-gray-400 text-sm">Monitoring & Dashboards</p>
            </div>
          </div>
          <span id="status-grafana" class="text-gray-500 text-lg">●</span>
        </div>
        <div class="mt-4 border-t border-zinc-800 pt-4 text-sm text-gray-300">
          <p>Haupt Monitoring Plattform</p>
          <p>Systemmetriken, Container, Logs</p>
          <p class="text-orange-400 mt-2">
            Connection only: 10.66.66.66/24, 10.0.0.0/16
          </p>
        </div>
      </a>
      <a
        href="https://prometheus.local"
        class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 hover:border-orange-500 transition"
      >
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <span class="text-2xl">📈</span>
            <div>
              <h2 class="text-xl font-bold">Prometheus</h2>
              <p class="text-gray-400 text-sm">Metrics & Time Series</p>
            </div>
          </div>
          <span id="status-prometheus" class="text-gray-500 text-lg">●</span>
        </div>
        <div class="mt-4 border-t border-zinc-800 pt-4 text-sm text-gray-300">
          <p>Time Series Datenbank</p>
          <p>Scraping & Metrics Storage</p>
          <p class="text-orange-400 mt-2">
            Connection only: 10.66.66.66/24, 10.0.0.0/16
          </p>
        </div>
      </a>
    </div>
  </body>
</html>
