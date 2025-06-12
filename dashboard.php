<?php session_start(); if (!isset($_SESSION['user'])) header("Location: login.php"); ?>
<?php include 'includes/header.php'; include 'includes/sidebar.php'; ?>
<h2 class="mb-4">Dashboard</h2>

<div class="row g-4">
  <div class="col-md-6">
    <div class="card shadow rounded-4 text-white bg-primary">
      <div class="card-body d-flex align-items-center">
        <i class="fas fa-users fa-2x me-3"></i>
        <div>
          <h5>Total Users</h5>
          <strong>123</strong>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card shadow rounded-4 text-white bg-success">
      <div class="card-body d-flex align-items-center">
        <i class="fas fa-robot fa-2x me-3"></i>
        <div>
          <h5>Active Bots</h5>
          <strong>45</strong>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card shadow rounded-4 text-white bg-info">
      <div class="card-body d-flex align-items-center">
        <i class="fas fa-user-plus fa-2x me-3"></i>
        <div>
          <h5>New Signups</h5>
          <strong>10</strong>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card shadow rounded-4 text-white bg-danger">
      <div class="card-body d-flex align-items-center">
        <i class="fas fa-comments fa-2x me-3"></i>
        <div>
          <h5>Messages Sent</h5>
          <strong>12,000</strong>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card p-4 mb-4">
  <h5 class="mb-3">Bot Activity Chart</h5>
  <div style="position: relative; height:400px; width:100%;">
    <canvas id="myChart"></canvas>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('myChart');
    if (canvas) {
      const ctx = canvas.getContext('2d');
      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
          datasets: [{
            label: 'Active Bots',
            data: [5, 10, 8, 6, 12, 15, 7],
            backgroundColor: '#0d6efd'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });
    }
  });
</script>


<?php include 'includes/footer.php'; ?>
