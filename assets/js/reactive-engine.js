class ReactiveEngine {
    constructor(endpoint, interval = 15000) {
        this.endpoint = endpoint;
        this.interval = interval;
        this.components = {}; 
        this.timer = null;
    }

    // UPGRADED: Now supports multiple listeners for the same data key!
    register(dataKey, renderFunction) {
        if (!this.components[dataKey]) {
            this.components[dataKey] = [];
        }
        this.components[dataKey].push(renderFunction);
    }

    async fetchNow() {
        try {
            const response = await fetch(this.endpoint);
            const data = await response.json();

            if (data.success) {
                for (const [key, renderFns] of Object.entries(this.components)) {
                    if (data[key] !== undefined) {
                        // Trigger ALL functions listening to this key
                        renderFns.forEach(fn => fn(data[key])); 
                    }
                }
            }
        } catch (err) {
            console.error("[ReactiveEngine] Polling Error:", err);
        }
    }

    start() {
        this.timer = setInterval(() => this.fetchNow(), this.interval);
    }

    stop() {
        if (this.timer) clearInterval(this.timer);
    }
}

window.ReactiveEngine = ReactiveEngine;