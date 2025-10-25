<?php
session_start();
include('../db_connect.php');

// protect
if(!isset($_SESSION['admin_id'])){
    header("Location: admin_login.php");
    exit;
}

// Fetch summary stats
$totalOrdersRes = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM orders");
$totalOrders = mysqli_fetch_assoc($totalOrdersRes)['cnt'] ?? 0;

$totalSalesRes = mysqli_query($conn, "SELECT IFNULL(SUM(total),0) AS s FROM orders");
$totalSales = mysqli_fetch_assoc($totalSalesRes)['s'] ?? 0;

$totalCustomersRes = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM users");
$totalCustomers = mysqli_fetch_assoc($totalCustomersRes)['cnt'] ?? 0;

$pendingRes = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM orders WHERE status='Pending'");
$pendingOrders = mysqli_fetch_assoc($pendingRes)['cnt'] ?? 0;

// Recent orders
$recentOrders = mysqli_query($conn, "
  SELECT o.*, u.first_name, u.last_name
  FROM orders o
  LEFT JOIN users u ON o.user_id=u.id
  ORDER BY o.created_at DESC
  LIMIT 8
");

// Sales by month (last 12 months)
$sales_month = [];
$sales_labels = [];
$sales_data = [];
$monthsRes = mysqli_query($conn, "
  SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym, IFNULL(SUM(total),0) AS sum_total
  FROM orders
  WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 11 MONTH)
  GROUP BY ym
  ORDER BY ym ASC
");
while($r = mysqli_fetch_assoc($monthsRes)){
  $sales_labels[] = $r['ym'];
  $sales_data[] = (float)$r['sum_total'];
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Dashboard - Caraang Aluminum</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
:root{
  --bg:#0f1724; --card:#0b1220; --muted:#98a0ac; --accent:#7dd3fc;
}
body{ background:var(--bg); color:#e6eef6; font-family:Poppins, sans-serif; }
.app { display:flex; min-height:100vh; }
.sidebar {
  width:260px; background:linear-gradient(180deg,#071022,#0b1220); padding:30px 18px; box-shadow:2px 0 12px rgba(0,0,0,0.6);
}
.brand { font-size:1.1rem; font-weight:700; margin-bottom:18px; color:#fff; }
.nav-link { color:var(--muted); padding:12px 14px; border-radius:8px; display:flex; gap:12px; align-items:center; text-decoration:none; }
.nav-link i { width:22px; text-align:center; color:#98a0ac; }
.nav-link:hover, .nav-link.active { background:#07111a; color:#fff; }
.content { flex:1; padding:28px; }
.topbar { display:flex; justify-content:space-between; gap:12px; align-items:center; margin-bottom:18px; }
.card { background:var(--card); border-radius:12px; padding:18px; box-shadow:0 8px 30px rgba(0,0,0,0.5); color:#dbe7f0; }
.stat-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:18px; margin-bottom:18px; }
.stat { padding:18px; border-radius:10px; background:linear-gradient(180deg,rgba(255,255,255,0.02),transparent); }
.stat h5{color:#9fb4c4; font-weight:600; font-size:0.9rem;}
.stat p{font-size:1.4rem; font-weight:700; margin:6px 0;}
.table-dark-custom thead{background:#07111a;color:#fff;}
.recent-orders .table td, .recent-orders .table th { vertical-align:middle; color:#dbe7f0; }
.small-muted{ color:var(--muted); font-size:0.9rem; }
.footer-note{ color:var(--muted); margin-top:24px; text-align:center; }
@media(max-width:900px){ .stat-grid{ grid-template-columns:repeat(2,1fr);} .sidebar{display:none;} .content{padding:18px;} }
</style>
</head>
<body>
<div class="app">

  <!-- sidebar -->
  <aside class="sidebar">
    <div class="brand">Caraang Aluminum Shop</div>
    <nav class="mb-4">
      <a class="nav-link active" href="admin_dashboard.php"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
      <a class="nav-link" href="manage_products.php"><i class="fa-solid fa-box"></i> Products</a>
      <a class="nav-link" href="manage_orders.php"><i class="fa-solid fa-list"></i> Orders</a>
      <a class="nav-link" href="manage_users.php"><i class="fa-solid fa-users"></i> Customers</a>
      <a class="nav-link" href="admin_logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </nav>

    <div style="margin-top:20px;">
      <div class="small-muted">Quick stats</div>
      <div class="small-muted mt-2">Orders: <?= number_format($totalOrders) ?></div>
      <div class="small-muted">Customers: <?= number_format($totalCustomers) ?></div>
      <div class="small-muted">Pending: <?= number_format($pendingOrders) ?></div>
    </div>
  </aside>

  <!-- content -->
  <main class="content">
    <div class="topbar">
      <h3 style="margin:0;">Dashboard</h3>
      <div style="display:flex; gap:12px; align-items:center;">
        <div class="small-muted">Hello, <?= htmlspecialchars($_SESSION['admin_name']) ?></div>
        <div style="width:44px; height:44px; border-radius:50%; background:#0c1b24; display:flex; align-items:center; justify-content:center;">
          <i class="fa-solid fa-user" style="color:#89b1c9;"></i>
        </div>
      </div>
    </div>

    <div class="stat-grid">
      <div class="stat card">
        <h5>Total Sales</h5>
        <p>₱ <?= number_format($totalSales,2) ?></p>
        <div class="small-muted">Since launch</div>
      </div>

      <div class="stat card">
        <h5>Total Orders</h5>
        <p><?= number_format($totalOrders) ?></p>
        <div class="small-muted">All orders</div>
      </div>

      <div class="stat card">
        <h5>Total Customers</h5>
        <p><?= number_format($totalCustomers) ?></p>
        <div class="small-muted">Registered users</div>
      </div>

      <div class="stat card">
        <h5>Pending Orders</h5>
        <p><?= number_format($pendingOrders) ?></p>
        <div class="small-muted">Need processing</div>
      </div>
    </div>

    <div style="display:grid; grid-template-columns:2fr 1fr; gap:18px; align-items:flex-start;">
      <div class="card recent-orders">
        <h5>Recent Orders</h5>
        <div class="table-responsive mt-3">
          <table class="table table-borderless table-sm">
            <thead class="table-dark-custom">
              <tr>
                <th>Order #</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php while($r = mysqli_fetch_assoc($recentOrders)): 
                $cls = '';
                $st = $r['status'];
                if(strtolower($st) == 'pending') $cls='badge bg-warning text-dark';
                elseif(strtolower($st) == 'processing') $cls='badge bg-info text-dark';
                elseif(strtolower($st) == 'delivered') $cls='badge bg-success';
                elseif(strtolower($st) == 'cancelled') $cls='badge bg-danger';
              ?>
              <tr>
                <td>#<?= $r['id'] ?></td>
                <td><?= htmlspecialchars($r['first_name'].' '.$r['last_name']) ?></td>
                <td>₱ <?= number_format($r['total'],2) ?></td>
                <td><span class="<?= $cls ?>"><?= htmlspecialchars($st) ?></span></td>
                <td><?= date('M d, Y', strtotime($r['created_at'])) ?></td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="card">
        <h5>Sales (last 12 months)</h5>
        <canvas id="salesChart" style="height:200px;"></canvas>
      </div>
    </div>

    <div class="footer-note">© <?= date('Y') ?> Caraang Aluminum Shop. Admin panel.</div>
  </main>
</div>

<script>
const ctx = document.getElementById('salesChart').getContext('2d');
const salesChart = new Chart(ctx, {
  type: 'line',
  data: {
    labels: <?= json_encode($sales_labels) ?: '[]' ?>,
    datasets: [{
      label: 'Sales',
      data: <?= json_encode($sales_data) ?: '[]' ?>,
      borderColor: '#7dd3fc',
      backgroundColor: 'rgba(125,211,252,0.06)',
      fill: true,
      tension: 0.3,
      pointRadius: 3
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { display: false }
    },
    scales: {
      y: { ticks: { color:'#b9d7e6' }, grid: { color: 'rgba(255,255,255,0.03)' } },
      x: { ticks: { color:'#b9d7e6' }, grid: { color: 'rgba(255,255,255,0.03)' } }
    }
  }
});
</script>
</body>
</html>
