// Rent Collection Chart
const rentCtx = document.getElementById('rentChart').getContext('2d');
new Chart(rentCtx, {
  type: 'line',
  data: {
    labels: rentData.map(r => r.month),
    datasets: [{
      label: 'Total Rent (₱)',
      data: rentData.map(r => r.total),
      borderColor: '#3498db',
      fill: false,
      tension: 0.3
    }]
  },
  options: { responsive: true, plugins: { legend: { display: false } } }
});

// Property Status Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
  type: 'doughnut',
  data: {
    labels: statusData.map(s => s.status),
    datasets: [{
      data: statusData.map(s => s.total),
      backgroundColor: ['#2ecc71', '#e74c3c', '#f1c40f']
    }]
  }
});

// Maintenance Summary Chart
const maintenanceCtx = document.getElementById('maintenanceChart').getContext('2d');
new Chart(maintenanceCtx, {
  type: 'bar',
  data: {
    labels: maintenanceData.map(m => m.status),
    datasets: [{
      label: 'Requests',
      data: maintenanceData.map(m => m.total),
      backgroundColor: ['#3498db', '#f1c40f', '#e74c3c']
    }]
  },
  options: { responsive: true, plugins: { legend: { display: false } } }
});
