import Chart from 'chart.js/auto';

Chart.defaults.font.family = "Inter, ui-sans-serif, system-ui, sans-serif";
Chart.defaults.font.size = 13;
Chart.defaults.color = '#737373';
Chart.defaults.animation.duration = 750;
Chart.defaults.animation.easing = 'easeInOutQuart';
Chart.defaults.plugins.legend.labels.boxWidth = 8;
Chart.defaults.plugins.legend.labels.boxHeight = 8;
Chart.defaults.plugins.legend.labels.padding = 10;
Chart.defaults.elements.point.radius = 0;
Chart.defaults.elements.point.hoverRadius = 3;
Chart.defaults.elements.line.tension = 0.38;
Chart.defaults.elements.line.borderWidth = 2;

const brand = '#3ecf8e';

function gradient(ctx, from, to) {
    const gradientFill = ctx.createLinearGradient(0, 0, 0, ctx.canvas.height || 180);
    gradientFill.addColorStop(0, from);
    gradientFill.addColorStop(1, to);

    return gradientFill;
}

function baseOptions() {
    return {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#171717',
                titleColor: '#fafafa',
                bodyColor: '#d4d4d4',
                padding: 8,
                cornerRadius: 6,
                displayColors: false,
            },
        },
        scales: {
            x: {
                grid: { display: false },
                ticks: { maxTicksLimit: 6, maxRotation: 0 },
                border: { display: false },
            },
            y: {
                grid: { color: 'rgba(23, 23, 23, 0.06)' },
                ticks: { maxTicksLimit: 4 },
                border: { display: false },
            },
        },
    };
}

function lineChart(canvas, labels, data, color) {
    const ctx = canvas.getContext('2d');

    return new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                data,
                borderColor: color,
                backgroundColor: gradient(ctx, `${color}33`, `${color}00`),
                fill: true,
                pointBackgroundColor: color,
            }],
        },
        options: baseOptions(),
    });
}

function barChart(canvas, labels, data) {
    return new Chart(canvas.getContext('2d'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                data,
                backgroundColor: data.map((_, index) => (
                    index === data.length - 1 ? brand : 'rgba(62, 207, 142, 0.28)'
                )),
                borderRadius: 3,
                borderSkipped: false,
                maxBarThickness: 12,
            }],
        },
        options: {
            ...baseOptions(),
            animation: { duration: 650, easing: 'easeOutCubic' },
        },
    });
}

function doughnutChart(canvas, traffic) {
    const labels = Object.keys(traffic);
    const values = Object.values(traffic);

    return new Chart(canvas.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{
                data: values,
                backgroundColor: [brand, '#737373', '#3b82f6', '#f5a524'],
                borderWidth: 0,
                hoverOffset: 4,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '74%',
            animation: { animateRotate: true, duration: 800 },
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: { color: '#737373', font: { size: 10 } },
                },
                tooltip: {
                    backgroundColor: '#171717',
                    padding: 8,
                    cornerRadius: 6,
                },
            },
        },
    });
}

function sparkChart(canvas, data, color) {
    return new Chart(canvas.getContext('2d'), {
        type: 'line',
        data: {
            labels: data.map((_, index) => index),
            datasets: [{
                data,
                borderColor: color,
                backgroundColor: `${color}22`,
                fill: true,
                borderWidth: 1.6,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 500 },
            plugins: { legend: { display: false }, tooltip: { enabled: false } },
            scales: { x: { display: false }, y: { display: false } },
        },
    });
}

function patch(chart, labels, data, extra = null) {
    if (! chart) {
        return;
    }

    chart.data.labels = labels;
    chart.data.datasets[0].data = data;

    if (extra) {
        extra(chart);
    }

    chart.update();
}

document.addEventListener('alpine:init', () => {
    window.Alpine.data('dashboardCharts', (initial) => ({
        charts: {},
        init() {
            this.charts.revenue = lineChart(this.$refs.revenue, initial.labels, initial.revenue, brand);
            this.charts.orders = barChart(this.$refs.orders, initial.labels, initial.orders);
            this.charts.traffic = doughnutChart(this.$refs.traffic, initial.traffic);
            this.charts.sparkRevenue = sparkChart(this.$refs.sparkRevenue, initial.sparks.revenue, brand);
            this.charts.sparkOrders = sparkChart(this.$refs.sparkOrders, initial.sparks.orders, brand);
            this.charts.sparkCustomers = sparkChart(this.$refs.sparkCustomers, initial.sparks.customers, '#3b82f6');
            this.charts.sparkConversion = sparkChart(this.$refs.sparkConversion, initial.sparks.conversion, '#f5a524');

            this.$wire.on('dashboard-tick', (payload) => {
                const data = Array.isArray(payload) ? payload[0] : payload;
                this.sync(data);
            });
        },
        sync(data) {
            if (! data) {
                return;
            }

            patch(this.charts.revenue, data.labels, data.revenue);
            patch(this.charts.orders, data.labels, data.orders, (chart) => {
                chart.data.datasets[0].backgroundColor = data.orders.map((_, index) => (
                    index === data.orders.length - 1 ? brand : 'rgba(62, 207, 142, 0.28)'
                ));
            });
            patch(this.charts.traffic, Object.keys(data.traffic), Object.values(data.traffic));
            patch(this.charts.sparkRevenue, data.sparks.revenue.map((_, index) => index), data.sparks.revenue);
            patch(this.charts.sparkOrders, data.sparks.orders.map((_, index) => index), data.sparks.orders);
            patch(this.charts.sparkCustomers, data.sparks.customers.map((_, index) => index), data.sparks.customers);
            patch(this.charts.sparkConversion, data.sparks.conversion.map((_, index) => index), data.sparks.conversion);
        },
        destroy() {
            Object.values(this.charts).forEach((chart) => chart?.destroy());
        },
    }));
});
