import Chart from 'chart.js/auto';

window.createSimpananChart = function (data) {
    const labels = data.map(item => `Bulan ${item.bulan}`);
    const datasets = data.map(item => item.total);

    new Chart(document.getElementById('simpanan-chart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Simpanan per Bulan',
                data: datasets,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
};