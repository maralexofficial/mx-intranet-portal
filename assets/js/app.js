function statusApp(sites) {
    return {
        sites: sites,
        interval: null,

        getServiceId(site) {
            return site.id;
        },

        formatDescription(text) {
            return text.replace(/\n/g, "<br>");
        },

        setStatus(el, online) {
            if (!el) return;

            el.classList.remove("text-gray-500", "text-green-400", "text-red-400");

            if (online === "1") {
                el.classList.add("text-green-400");
            } else {
                el.classList.add("text-red-400");
            }

            el.textContent = "●";
        },

        async updateStatus() {
            try {
                const res = await fetch("/api/prometheus/api/v1/query?query=probe_success");
                const data = await res.json();

                const results = data?.data?.result || [];

                results.forEach((item) => {
                    const service = item?.metric?.service;
                    const value = item?.value?.[1];

                    const el = document.getElementById("status-" + service);
                    this.setStatus(el, value);
                });

            } catch (err) {
                console.error("Status Update Fehler:", err);
                this.sites.forEach(site => {
                    const el = document.getElementById("status-" + this.getServiceId(site));
                    this.setStatus(el, "0");
                });
            }
        },

        init() {
            this.updateStatus();
            this.interval = setInterval(() => this.updateStatus(), 5000);
        }
    }
}