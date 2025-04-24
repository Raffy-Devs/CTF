<?php
session_start();

// First check if user is logged in via session
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

// Then check cookie existence and valid role
if (!isset($_COOKIE['Role']) || !in_array($_COOKIE['Role'], ['user', 'admin'])) {
    // Destroy session and clear cookies if invalid role
    session_unset();
    session_destroy();
    setcookie('Role', '', time() - 3600, '/'); // Expire cookie
    header('Location: index.php');
    exit();
}

// Get both values for verification
$cookieRole = $_COOKIE['Role'];
$sessionRole = $_SESSION['role'];

// // Critical security check: Verify session and cookie match
// if ($cookieRole !== $sessionRole) {
//     // Potential tampering detected
//     session_unset();
//     session_destroy();
//     setcookie('Role', '', time() - 3600, '/');
//     header('Location: index.php');
//     exit();
// }

// Now handle authorization based on SESSION role (secure source)
$role = $_SESSION['role'];
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Burgundy Dashboard</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }
    
    :root {
      --primary: #7e0308;
      --primary-dark: #5a0205;
      --primary-light: #a30309;
      --text-light: #ffffff;
      --text-dark: #333333;
      --bg-light: #f8f9fa;
      --accent: #ff9800;
    }
    
    body {
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      min-height: 100vh;
      overflow-x: hidden;
    }
    
    .dashboard-container {
      display: grid;
      grid-template-columns: 250px 1fr;
      grid-template-rows: 70px 1fr;
      grid-template-areas: 
        "sidebar header"
        "sidebar main";
      height: 100vh;
    }
    
    /* HEADER */
    .header {
      grid-area: header;
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(10px);
      padding: 0 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .search-container {
      position: relative;
      width: 300px;
    }
    
    .search-container input {
      width: 100%;
      padding: 10px 15px 10px 40px;
      border-radius: 30px;
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: var(--text-light);
      outline: none;
      transition: all 0.3s ease;
    }
    
    .search-container input:focus {
      background: rgba(255, 255, 255, 0.15);
      border-color: rgba(255, 255, 255, 0.5);
      box-shadow: 0 0 10px rgba(255, 255, 255, 0.1);
      transform: translateY(-2px);
    }
    
    .search-container input::placeholder {
      color: rgba(255, 255, 255, 0.6);
    }
    
    .search-container i {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: rgba(255, 255, 255, 0.6);
    }
    
    .user-profile {
      display: flex;
      align-items: center;
      gap: 15px;
    }
    
    .profile-pic {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.2);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 700;
      border: 2px solid rgba(255, 255, 255, 0.5);
      overflow: hidden;
    }
    
    .notifications {
      position: relative;
      margin-right: 20px;
    }
    
    .notifications .badge {
      position: absolute;
      top: -5px;
      right: -5px;
      width: 18px;
      height: 18px;
      background: var(--accent);
      color: white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 10px;
      font-weight: bold;
      animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
      0% {
        transform: scale(1);
        opacity: 1;
      }
      50% {
        transform: scale(1.2);
        opacity: 0.8;
      }
      100% {
        transform: scale(1);
        opacity: 1;
      }
    }
    
    .header-icons {
      display: flex;
      align-items: center;
    }
    
    .header-icons i {
      color: white;
      font-size: 20px;
      cursor: pointer;
      margin-left: 15px;
      transition: all 0.2s ease;
    }
    
    .header-icons i:hover {
      color: rgba(255, 255, 255, 0.8);
      transform: translateY(-2px);
    }
    
    /* SIDEBAR */
    .sidebar {
      grid-area: sidebar;
      background: rgba(0, 0, 0, 0.2);
      backdrop-filter: blur(10px);
      padding: 20px 0;
      color: white;
      border-right: 1px solid rgba(255, 255, 255, 0.1);
      display: flex;
      flex-direction: column;
      transition: all 0.3s ease;
    }
    
    .logo {
      display: flex;
      align-items: center;
      padding: 10px 25px;
      margin-bottom: 30px;
    }
    
    .logo-icon {
      width: 40px;
      height: 40px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 10px;
      font-weight: bold;
      font-size: 20px;
      border: 1px solid rgba(255, 255, 255, 0.2);
      animation: float 4s ease-in-out infinite;
    }
    
    @keyframes float {
      0%, 100% {
        transform: translateY(0);
      }
      50% {
        transform: translateY(-10px);
      }
    }
    
    .logo-text {
      font-size: 18px;
      font-weight: 600;
      letter-spacing: 1px;
    }
    
    .menu {
      margin-top: 20px;
      flex: 1;
    }
    
    .menu-title {
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: rgba(255, 255, 255, 0.5);
      padding: 0 25px;
      margin: 15px 0 10px;
    }
    
    .menu-item {
      padding: 12px 25px;
      display: flex;
      align-items: center;
      cursor: pointer;
      transition: all 0.2s ease;
      position: relative;
    }
    
    .menu-item.active {
      background: rgba(255, 255, 255, 0.1);
    }
    
    .menu-item.active::before {
      content: '';
      position: absolute;
      left: 0;
      top: 0;
      height: 100%;
      width: 4px;
      background: var(--accent);
    }
    
    .menu-item:hover {
      background: rgba(255, 255, 255, 0.05);
      transform: translateX(5px);
    }
    
    .menu-item i {
      margin-right: 15px;
      font-size: 18px;
      width: 20px;
      text-align: center;
    }
    
    .sidebar-bottom {
      padding: 15px 25px;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      display: flex;
      align-items: center;
    }
    
    .sidebar-bottom .info {
      margin-left: 10px;
    }
    
    .sidebar-bottom .name {
      font-size: 14px;
      font-weight: 600;
    }
    
    .sidebar-bottom .role {
      font-size: 12px;
      color: rgba(255, 255, 255, 0.7);
    }
    
    /* MAIN CONTENT */
    .main-content {
      grid-area: main;
      padding: 25px;
      overflow-y: auto;
      color: white;
    }
    
    .page-title {
      margin-bottom: 25px;
      font-size: 24px;
      font-weight: 600;
    }
    
    .card-container {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 25px;
      margin-bottom: 25px;
    }
    
    .card {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(10px);
      border-radius: 15px;
      padding: 25px;
      border: 1px solid rgba(255, 255, 255, 0.1);
      transition: all 0.3s ease;
      overflow: hidden;
      position: relative;
    }
    
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
      border-color: rgba(255, 255, 255, 0.3);
    }
    
    .card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
    }
    
    .card-title {
      font-size: 18px;
      font-weight: 600;
    }
    
    .card-icon {
      width: 40px;
      height: 40px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
    }
    
    .card-value {
      font-size: 28px;
      font-weight: 700;
      margin: 10px 0;
    }
    
    .card-label {
      color: rgba(255, 255, 255, 0.7);
      font-size: 14px;
    }
    
    .stat-change {
      display: flex;
      align-items: center;
      margin-top: 10px;
      font-size: 14px;
    }
    
    .stat-change.positive {
      color: #4caf50;
    }
    
    .stat-change.negative {
      color: #f44336;
    }
    
    .chart-container {
      height: 100px;
      margin-top: 15px;
      position: relative;
    }
    
    .chart-line {
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 50px;
      background: linear-gradient(to top right, transparent, rgba(255, 255, 255, 0.1));
      clip-path: polygon(0 100%, 15% 70%, 30% 90%, 45% 60%, 60% 80%, 75% 40%, 100% 10%, 100% 100%);
    }
    
    /* Table */
    .table-container {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(10px);
      border-radius: 15px;
      border: 1px solid rgba(255, 255, 255, 0.1);
      overflow: hidden;
      margin-top: 25px;
    }
    
    .table-header {
      padding: 20px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .table {
      width: 100%;
      border-collapse: collapse;
    }
    
    .table th {
      text-align: left;
      padding: 15px 20px;
      font-weight: 600;
      color: rgba(255, 255, 255, 0.8);
      background: rgba(0, 0, 0, 0.1);
    }
    
    .table td {
      padding: 15px 20px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    .table tr:last-child td {
      border-bottom: none;
    }
    
    .table tr {
      transition: all 0.2s ease;
    }
    
    .table tr:hover {
      background: rgba(255, 255, 255, 0.05);
    }
    
    .status {
      display: inline-block;
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
    }
    
    .status.active {
      background: rgba(76, 175, 80, 0.2);
      color: #4caf50;
    }
    
    .status.pending {
      background: rgba(255, 152, 0, 0.2);
      color: #ff9800;
    }
    
    .status.inactive {
      background: rgba(244, 67, 54, 0.2);
      color: #f44336;
    }
    
    /* Progress bar */
    .progress {
      height: 6px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 3px;
      overflow: hidden;
      margin-top: 5px;
    }
    
    .progress-bar {
      height: 100%;
      background: var(--accent);
      border-radius: 3px;
      transition: width 0.5s ease;
    }
    
    /* Background Effects */
    .bg-shapes {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      overflow: hidden;
      z-index: -1;
    }
    
    .bg-shape {
      position: absolute;
      background: rgba(126, 3, 8, 0.4);
      border-radius: 50%;
      filter: blur(80px);
      z-index: -1;
      animation: moveShape ease-in-out infinite alternate;
    }
    
    @keyframes moveShape {
      0% {
        transform: translate(0, 0) scale(1);
      }
      100% {
        transform: translate(30px, 30px) scale(1.1);
      }
    }
    
    /* Responsiveness */
    @media (max-width: 992px) {
      .dashboard-container {
        grid-template-columns: 70px 1fr;
      }
      
      .sidebar {
        width: 70px;
        overflow: hidden;
      }
      
      .logo-text,
      .menu-text,
      .menu-title,
      .sidebar-bottom .info {
        display: none;
      }
      
      .menu-item {
        justify-content: center;
        padding: 15px 0;
      }
      
      .menu-item i {
        margin-right: 0;
      }
      
      .sidebar-bottom {
        justify-content: center;
        padding: 15px 0;
      }
    }
    
    @media (max-width: 768px) {
      .card-container {
        grid-template-columns: 1fr;
      }
    }
  </style>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- FLAG  :  CTF{1nsP3c7_M3}  -->



  <div class="bg-shapes">
    <!-- Background shapes will be added by JS -->
  </div>
  
  <div class="dashboard-container">
    <!-- Sidebar -->
    <div class="sidebar">
      <div class="logo">
        <div class="logo-icon">B</div>
        <div class="logo-text">Burgundy</div>
      </div>
      
      <div class="menu">
        <div class="menu-title">Main</div>
        <div class="menu-item active">
          <i class="fas fa-home"></i>
          <span class="menu-text">Dashboard</span>
        </div>
        <div class="menu-item">
          <i class="fas fa-chart-line"></i>
          <span class="menu-text">Analytics</span>
        </div>
        <div class="menu-item">
          <i class="fas fa-clipboard-list"></i>
          <span class="menu-text">Projects</span>
        </div>
        
        <div class="menu-title">Management</div>
        <div class="menu-item">
          <i class="fas fa-users"></i>
          <span class="menu-text">Team</span>
        </div>
        <div class="menu-item">
          <i class="fas fa-calendar"></i>
          <span class="menu-text">Calendar</span>
        </div>
        <div class="menu-item">
          <i class="fas fa-cog"></i>
          <span class="menu-text">Settings</span>
        </div>
      </div>
      
      <div class="sidebar-bottom">
        <div class="profile-pic">J</div>
        <div class="info">
          <div class="name">John Doe</div>
          <div class="role">Administrator</div>
        </div>
      </div>
    </div>
    
    <!-- Header -->
    <div class="header">
      <div class="search-container">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Search...">
      </div>
      
      <div class="header-icons">
        <div class="notifications">
          <i class="far fa-bell"></i>
          <span class="badge">3</span>
        </div>
        <i class="far fa-envelope"></i>
        <div class="user-profile">
          <div class="profile-pic">J</div>
        </div>
      </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
      <h1 class="page-title">Dashboard Overview</h1>
      
      <!-- Statistics Cards -->
      <div class="card-container">
        <div class="card">
          <div class="card-header">
            <div class="card-title">Revenue</div>
            <div class="card-icon"><i class="fas fa-dollar-sign"></i></div>
          </div>
          <div class="card-value">$24,568</div>
          <div class="card-label">Total Revenue</div>
          <div class="stat-change positive">
            <i class="fas fa-arrow-up"></i>
            <span>12.5% since last month</span>
          </div>
          <div class="chart-container">
            <div class="chart-line"></div>
          </div>
        </div>
        
        <div class="card">
          <div class="card-header">
            <div class="card-title">Users</div>
            <div class="card-icon"><i class="fas fa-users"></i></div>
          </div>
          <div class="card-value">7,842</div>
          <div class="card-label">Total Users</div>
          <div class="stat-change positive">
            <i class="fas fa-arrow-up"></i>
            <span>8.2% since last month</span>
          </div>
          <div class="chart-container">
            <div class="chart-line"></div>
          </div>
        </div>
        
        <div class="card">
          <div class="card-header">
            <div class="card-title">Orders</div>
            <div class="card-icon"><i class="fas fa-shopping-cart"></i></div>
          </div>
          <div class="card-value">1,352</div>
          <div class="card-label">Total Orders</div>
          <div class="stat-change negative">
            <i class="fas fa-arrow-down"></i>
            <span>3.4% since last month</span>
          </div>
          <div class="chart-container">
            <div class="chart-line"></div>
          </div>
        </div>
        
        <div class="card">
          <div class="card-header">
            <div class="card-title">Engagement</div>
            <div class="card-icon"><i class="fas fa-chart-pie"></i></div>
          </div>
          <div class="card-value">68%</div>
          <div class="card-label">Average Engagement</div>
          <div class="stat-change positive">
            <i class="fas fa-arrow-up"></i>
            <span>5.7% since last month</span>
          </div>
          <div class="progress">
            <div class="progress-bar" style="width: 68%"></div>
          </div>
        </div>
      </div>
      
      <!-- Recent Activities Table -->
      <div class="table-container">
        <div class="table-header">
          <h2>Recent Activities</h2>
          <div>
            <i class="fas fa-ellipsis-h"></i>
          </div>
        </div>
        <table class="table">
          <thead>
            <tr>
              <th>User</th>
              <th>Project</th>
              <th>Progress</th>
              <th>Status</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>John Smith</td>
              <td>Website Redesign</td>
              <td>
                <div>75%</div>
                <div class="progress">
                  <div class="progress-bar" style="width: 75%"></div>
                </div>
              </td>
              <td><span class="status active">Active</span></td>
              <td>Apr 18, 2025</td>
            </tr>
            <tr>
              <td>Emma Wilson</td>
              <td>Mobile App Development</td>
              <td>
                <div>45%</div>
                <div class="progress">
                  <div class="progress-bar" style="width: 45%"></div>
                </div>
              </td>
              <td><span class="status active">Active</span></td>
              <td>Apr 17, 2025</td>
            </tr>
            <tr>
              <td>Michael Brown</td>
              <td>SEO Optimization</td>
              <td>
                <div>90%</div>
                <div class="progress">
                  <div class="progress-bar" style="width: 90%"></div>
                </div>
              </td>
              <td><span class="status pending">Pending</span></td>
              <td>Apr 15, 2025</td>
            </tr>
            <tr>
              <td>Sarah Davis</td>
              <td>Content Strategy</td>
              <td>
                <div>30%</div>
                <div class="progress">
                  <div class="progress-bar" style="width: 30%"></div>
                </div>
              </td>
              <td><span class="status inactive">Inactive</span></td>
              <td>Apr 10, 2025</td>
            </tr>
            <tr>
              <td>Robert Johnson</td>
              <td>Data Migration</td>
              <td>
                <div>60%</div>
                <div class="progress">
                  <div class="progress-bar" style="width: 60%"></div>
                </div>
              </td>
              <td><span class="status active">Active</span></td>
              <td>Apr 5, 2025</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Create animated background shapes
      const shapesContainer = document.querySelector('.bg-shapes');
      const numShapes = 4;
      
      for (let i = 0; i < numShapes; i++) {
        const shape = document.createElement('div');
        shape.className = 'bg-shape';
        
        // Random position and size
        const size = Math.random() * 500 + 300;
        const left = Math.random() * 100;
        const top = Math.random() * 100;
        const duration = Math.random() * 15 + 20;
        const delay = Math.random() * 5;
        
        shape.style.width = `${size}px`;
        shape.style.height = `${size}px`;
        shape.style.left = `${left}%`;
        shape.style.top = `${top}%`;
        shape.style.animationDuration = `${duration}s`;
        shape.style.animationDelay = `${delay}s`;
        
        shapesContainer.appendChild(shape);
      }
      
      // Menu item click handler
      const menuItems = document.querySelectorAll('.menu-item');
      menuItems.forEach(item => {
        item.addEventListener('click', function() {
          // Remove active class from all items
          menuItems.forEach(i => i.classList.remove('active'));
          // Add active class to clicked item
          this.classList.add('active');
        });
      });
    });
  </script>
</body>
</html>