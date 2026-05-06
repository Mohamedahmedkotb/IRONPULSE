document.addEventListener("DOMContentLoaded", function () {

  const ctx1 = document.getElementById('lineChart');
  const ctx2 = document.getElementById('barChart');

  // LINE CHART
  new Chart(ctx1, {
    type: 'line',
    data: {
      labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
      datasets: [{
        data: [3000, 6000, 8000, 9500, 14000],
        borderColor: '#3b82f6',
        backgroundColor: 'rgba(59,130,246,0.2)',
        tension: 0.4,
        fill: true,
        pointRadius: 5
      }]
    },
    options: {
      plugins: { legend: { display: false } },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: v => v >= 1000 ? v/1000 + 'k' : v
          }
        }
      }
    }
  });

  // BAR CHART
  new Chart(ctx2, {
    type: 'bar',
    data: {
      labels: ['S','M','T','W','T','F','S'],
      datasets: [{
        data: [4,7,3,9,5,2,1],
        backgroundColor: '#3b82f6',
        borderRadius: 6
      }]
    },
    options: {
      plugins: { legend: { display: false } },
      scales: {
        y: { display: false },
        x: { grid: { display: false } }
      }
    }
  });

});

 
