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