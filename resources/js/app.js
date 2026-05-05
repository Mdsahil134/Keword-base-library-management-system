import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

function recordSearchEvent() {
    const key = 'library_search_log';
    const today = new Date().toISOString().slice(0, 10);
    try {
        const raw = localStorage.getItem(key);
        const log = raw ? JSON.parse(raw) : {};
        log[today] = (log[today] || 0) + 1;
        localStorage.setItem(key, JSON.stringify(log));
    } catch {
        /* ignore */
    }
}

function getSearchLogByDay() {
    const key = 'library_search_log';
    try {
        const raw = localStorage.getItem(key);
        return raw ? JSON.parse(raw) : {};
    } catch {
        return {};
    }
}

window.librarySearchChartData = function librarySearchChartData() {
    const log = getSearchLogByDay();
    const labels = [];
    const data = [];
    for (let i = 6; i >= 0; i--) {
        const d = new Date();
        d.setDate(d.getDate() - i);
        const day = d.toISOString().slice(0, 10);
        labels.push(
            d.toLocaleDateString(undefined, {
                weekday: 'short',
                month: 'short',
                day: 'numeric',
            }),
        );
        data.push(log[day] || 0);
    }
    return { labels, data };
};

function wireSearchFormTracking(form) {
    if (!form || form.dataset.tracked === '1') return;
    form.dataset.tracked = '1';
    form.addEventListener('submit', () => {
        const q = form.querySelector('input[name="q"]');
        if (q && q.value.trim() !== '') {
            recordSearchEvent();
        }
    });
}

function initSidebar() {
    const sidebar = document.getElementById('app-sidebar');
    const toggle = document.getElementById('sidebar-toggle');
    const overlay = document.getElementById('sidebar-overlay');
    const shell = document.getElementById('app-shell');
    if (!sidebar || !toggle) return;

    const close = () => {
        sidebar.classList.add('-translate-x-full');
        if (window.innerWidth >= 768) {
            sidebar.classList.add('md:w-0', 'md:overflow-hidden', 'md:border-r-0');
            shell?.classList.remove('md:ml-64');
            shell?.classList.add('md:ml-0');
        }
        overlay?.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    };
    const open = () => {
        sidebar.classList.remove('-translate-x-full');
        if (window.innerWidth >= 768) {
            sidebar.classList.remove('md:w-0', 'md:overflow-hidden', 'md:border-r-0');
            shell?.classList.add('md:ml-64');
            shell?.classList.remove('md:ml-0');
            overlay?.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            return;
        }
        overlay?.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    };

    toggle.addEventListener('click', () => {
        if (sidebar.classList.contains('-translate-x-full')) {
            open();
        } else {
            close();
        }
    });
    overlay?.addEventListener('click', close);

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 768) {
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.remove('md:w-0', 'md:overflow-hidden', 'md:border-r-0');
            shell?.classList.add('md:ml-64');
            shell?.classList.remove('md:ml-0');
            overlay?.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('md:w-0', 'md:overflow-hidden', 'md:border-r-0');
        }
    });
}

function initNavSearchLoading() {
    const form = document.getElementById('nav-search-form');
    const spinner = document.getElementById('nav-search-spinner');
    if (!form || !spinner) return;
    form.addEventListener('submit', () => {
        spinner.classList.remove('hidden');
    });
}

function updateStatSearchesToday() {
    const el = document.getElementById('stat-searches-today');
    if (!el) return;
    const log = getSearchLogByDay();
    const today = new Date().toISOString().slice(0, 10);
    el.textContent = String(log[today] || 0);
}

function initPageTimeTracking() {
    const url = document.querySelector('meta[name="activity-time-url"]')?.getAttribute('content');
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!url || !token) return;

    let start = Date.now();
    let accumulated = 0;

    function sendSeconds(sec) {
        if (sec < 3) return;
        const capped = Math.min(sec, 600);
        const body = new URLSearchParams();
        body.append('_token', token);
        body.append('seconds', String(capped));
        if (navigator.sendBeacon) {
            const blob = new Blob([body.toString()], { type: 'application/x-www-form-urlencoded' });
            navigator.sendBeacon(url, blob);
        } else {
            fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token, Accept: 'application/json' },
                body,
                keepalive: true,
            }).catch(() => {});
        }
    }

    function flush() {
        const sec = Math.floor((Date.now() - start) / 1000) + accumulated;
        accumulated = 0;
        start = Date.now();
        sendSeconds(sec);
    }

    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'hidden') {
            flush();
        } else {
            start = Date.now();
        }
    });
    window.addEventListener('pagehide', flush);
}

document.addEventListener('DOMContentLoaded', () => {
    initSidebar();
    initNavSearchLoading();
    updateStatSearchesToday();
    initPageTimeTracking();
    document.querySelectorAll('form[action*="search"], form[action*="/search"]').forEach(wireSearchFormTracking);
    const homeForm = document.getElementById('search-form');
    if (homeForm) wireSearchFormTracking(homeForm);
});

window.initBooksCategoryChart = function initBooksCategoryChart(canvasId, labels, values) {
    const canvas = document.getElementById(canvasId);
    if (!canvas || typeof Chart === 'undefined') return;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels,
            datasets: [
                {
                    data: values,
                    backgroundColor: [
                        'rgba(56, 189, 248, 0.75)',
                        'rgba(99, 102, 241, 0.75)',
                        'rgba(167, 139, 250, 0.75)',
                        'rgba(244, 114, 182, 0.75)',
                        'rgba(52, 211, 153, 0.75)',
                        'rgba(251, 191, 36, 0.75)',
                        'rgba(148, 163, 184, 0.75)',
                    ],
                    borderWidth: 0,
                },
            ],
        },
        options: {
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: '#64748b', padding: 16 },
                },
            },
            cutout: '58%',
        },
    });
};

window.initSearchesLineChart = function initSearchesLineChart(canvasId) {
    const canvas = document.getElementById(canvasId);
    if (!canvas || typeof Chart === 'undefined') return;
    const { labels, data } = window.librarySearchChartData();
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Searches (this device)',
                    data,
                    borderColor: 'rgba(56, 189, 248, 0.9)',
                    backgroundColor: 'rgba(56, 189, 248, 0.12)',
                    fill: true,
                    tension: 0.35,
                    pointRadius: 4,
                    pointBackgroundColor: '#38bdf8',
                },
            ],
        },
        options: {
            plugins: {
                legend: {
                    labels: { color: '#64748b' },
                },
            },
            scales: {
                x: {
                    ticks: { color: '#64748b', maxRotation: 45 },
                    grid: { color: 'rgba(148,163,184,0.25)' },
                },
                y: {
                    beginAtZero: true,
                    ticks: { color: '#64748b', stepSize: 1 },
                    grid: { color: 'rgba(148,163,184,0.25)' },
                },
            },
        },
    });
};
