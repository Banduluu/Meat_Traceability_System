<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not authenticated
    header('Location: Login.php');
    exit();
}

// Include dashboard calculations
require_once 'dashboard_calculations.php';

// Initialize calculations class
$dashboard = new DashboardCalculations();
$metrics = $dashboard->getAllMetrics();
$user_info = $dashboard->getUserInfo($_SESSION['user_id']);
$system_updates = $dashboard->getRecentSystemUpdates(5);
$user_activities = $dashboard->getUserActivities(4);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="logo">
                <h2><i class="fas fa-chart-line"></i> adminity</h2>
            </div>
            <nav>
                <ul>
                    <li><a href="#" class="nav-link active"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
                    <li><a href="user.php" class="nav-link"><i class="fas fa-users"></i> <span>Users</span></a></li>
                    <li><a href="approvals.php" class="nav-link"><i class="fas fa-check-square"></i> <span>User Approvals</span></a></li>
                    <li><a href="reports.php" class="nav-link"><i class="fas fa-chart-bar"></i> <span>Reports</span></a></li>
                    <li><a href="MIC viewer.php" class="nav-link"><i class="fas fa-microscope"></i> <span>MIC Viewer</span></a></li>
                    <li><a href="Login.php" class="nav-link"><i class="fas fa-sign-in-alt"></i> <span>Login</span></a></li>
                    <li><a href="#" class="nav-link"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
                    <li style="margin-top: 50px; border-top: 1px solid rgba(255, 255, 255, 0.1); padding-top: 20px;">
                        <a href="#" class="nav-link"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
                    </li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <header class="header">
                <h1>Dashboard</h1>
                <div class="user-info">
                    <i class="fas fa-search"></i>
                    <i class="fas fa-bell" style="color: #e74c3c;"></i>
                    <i class="fas fa-cog"></i>
                    <span><?php echo htmlspecialchars($user_info['full_name'] ?? 'User'); ?></span>
                </div>
            </header>

            <!-- Dashboard Cards Top Row -->
            <div class="dashboard-cards">
                <div class="card">
                    <div class="card-title"><i class="fas fa-certificate"></i> Total encoded certificates today</div>
                    <div class="card-value"><?php echo $metrics['certificates_today']; ?></div>
                    <div class="card-subtitle"><i class="fas fa-arrow-up"></i> Update: <?php echo date('h:i a'); ?></div>
                </div>
                <div class="card green">
                    <div class="card-title"><i class="fas fa-users"></i> Total Meat Heads Inspected</div>
                    <div class="card-value"><?php echo $metrics['total_heads']; ?></div>
                    <div class="card-subtitle"><i class="fas fa-arrow-up"></i> Update: <?php echo date('h:i a'); ?></div>
                </div>
                <div class="card pink">
                    <div class="card-title"><i class="fas fa-check-circle"></i> Approved inspections</div>
                    <div class="card-value"><?php echo $metrics['approved_inspections']; ?></div>
                    <div class="card-subtitle"><i class="fas fa-arrow-up"></i> Update: <?php echo date('h:i a'); ?></div>
                </div>
            </div>

            <!-- Dashboard Cards Bottom Row -->
            <div class="dashboard-cards">
                <div class="card cyan">
                    <div class="card-title"><i class="fas fa-exclamation-circle"></i> Needs review or rejected</div>
                    <div class="card-value"><?php echo $metrics['needs_review']; ?></div>
                    <div class="card-subtitle"><i class="fas fa-arrow-up"></i> Update: <?php echo date('h:i a'); ?></div>
                </div>
                <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-title"><i class="fas fa-users"></i> Active Inspectors</div>
                    <div style="font-size: 24px; font-weight: 700; margin-bottom: 12px;"><?php echo $metrics['active_inspectors']; ?> Working / <?php echo $metrics['idle_inspectors']; ?> Idle</div>
                    <div class="card-subtitle"><i class="fas fa-arrow-up"></i> Real-time status</div>
                </div>
                <div class="card" style="background: linear-gradient(135deg, #d0d0d0 0%, #e0e0e0 100%);">
                    <div class="card-title" style="color: #333;"><i class="fas fa-layer-group"></i> Today's Meat Categories</div>
                    <div style="font-size: 14px; color: #333; line-height: 1.8; margin-top: 10px;">
                        <?php 
                        if (!empty($metrics['meat_categories'])) {
                            foreach ($metrics['meat_categories'] as $category) {
                                echo "<div><strong>" . htmlspecialchars($category['product_name']) . "</strong> - " . $category['count'] . "</div>";
                            }
                        } else {
                            echo "<div>No data available</div>";
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Number of Heads Section -->
            <div class="section">
                <div class="section-header">
                    <h2>Number of Heads Per Month</h2>
                    <div class="actions">
                        <a href="#" style="color: #3498db; text-decoration: none; font-size: 14px; font-weight: 600;">
                            <i class="fas fa-download"></i> Download Report
                        </a>
                    </div>
                </div>
                <div class="section-content">
                    <div style="position: relative; height: 400px; margin-bottom: 20px;">
                        <canvas id="salesChart"></canvas>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 20px; padding-top: 20px; border-top: 1px solid #ecf0f1;">
                        <?php 
                        $total_heads = 0;
                        $monthly_count = count($metrics['monthly_data']);
                        if (!empty($metrics['monthly_data'])) {
                            foreach ($metrics['monthly_data'] as $data) {
                                $total_heads += $data['count'];
                            }
                        }
                        ?>
                        <div style="text-align: center;">
                            <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px;">TOTAL HEADS</div>
                            <div style="font-size: 32px; font-weight: 700; color: #ff9a56;"><?php echo $total_heads; ?></div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px;">MONTHS TRACKED</div>
                            <div style="font-size: 32px; font-weight: 700; color: #3498db;"><?php echo $monthly_count; ?></div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 12px; color: #7f8c8d; margin-bottom: 5px;">AVERAGE PER MONTH</div>
                            <div style="font-size: 32px; font-weight: 700; color: #27ae60;"><?php echo ($monthly_count > 0 ? round($total_heads / $monthly_count) : 0); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Activity & Latest Updates -->
            <div class="grid-2">
                <!-- User Activity -->
                <div class="section">
                    <div class="section-header">
                        <h2>User Activity</h2>
                    </div>
                    <div class="section-content">
                        <?php 
                        if (!empty($user_activities)) {
                            foreach ($user_activities as $activity) {
                                echo '<div class="activity-item">';
                                echo '  <div class="activity-avatar">' . htmlspecialchars($activity['avatar_initials']) . '</div>';
                                echo '  <div class="activity-content">';
                                echo '    <h4>' . htmlspecialchars($activity['full_name']) . '</h4>';
                                echo '    <p>' . htmlspecialchars($activity['description']) . '</p>';
                                echo '    <div class="activity-time">' . $dashboard->getTimeAgo($activity['created_at']) . '</div>';
                                echo '  </div>';
                                echo '</div>';
                            }
                        } else {
                            echo '<div style="text-align: center; color: #7f8c8d; padding: 20px;">No recent activities</div>';
                        }
                        ?>
                        <p style="color: #3498db; text-align: center; margin-top: 15px; font-size: 14px; cursor: pointer;">
                            <i class="fas fa-eye"></i> View all Activities
                        </p>
                    </div>
                </div>

                <!-- Latest Updates -->
                <div class="section">
                    <div class="section-header">
                        <h2>Latest Updates</h2>
                    </div>
                    <div class="section-content">
                        <?php 
                        if (!empty($system_updates)) {
                            foreach ($system_updates as $update) {
                                echo '<div style="display: flex; gap: 15px; padding: 15px 0; border-bottom: 1px solid #ecf0f1;">';
                                echo '  <div style="width: 8px; background: ' . htmlspecialchars($update['color']) . '; border-radius: 4px;"></div>';
                                echo '  <div>';
                                echo '    <div style="color: #2c3e50; font-weight: 600; margin-bottom: 5px;">' . $dashboard->getTimeAgo($update['created_at']) . '</div>';
                                echo '    <div style="color: #7f8c8d; font-size: 14px;"><i class="' . htmlspecialchars($update['icon']) . '"></i> ' . htmlspecialchars($update['message']) . '</div>';
                                echo '  </div>';
                                echo '</div>';
                            }
                        } else {
                            echo '<div style="color: #7f8c8d; text-align: center; padding: 20px;">No recent updates</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Information Section -->
            <div class="section">
                <div class="section-header">
                    <h2>Information</h2>
                </div>
                <div class="section-content">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 20px;">
                        <div style="padding: 15px; background: #f8f9fa; border-radius: 8px;">
                            <div style="color: #7f8c8d; font-size: 13px; margin-bottom: 5px;">Username</div>
                            <div style="color: #2c3e50; font-weight: 600;"><?php echo htmlspecialchars($user_info['username']); ?></div>
                        </div>
                        <div style="padding: 15px; background: #f8f9fa; border-radius: 8px;">
                            <div style="color: #7f8c8d; font-size: 13px; margin-bottom: 5px;">Role</div>
                            <div style="color: #2c3e50; font-weight: 600;"><?php echo htmlspecialchars($user_info['role']); ?></div>
                        </div>
                        <div style="padding: 15px; background: #f8f9fa; border-radius: 8px;">
                            <div style="color: #7f8c8d; font-size: 13px; margin-bottom: 5px;">Branch/Slaughterhouse</div>
                            <div style="color: #2c3e50; font-weight: 600;"><?php echo htmlspecialchars($user_info['branch_or_slaughterhouse']); ?></div>
                        </div>
                    </div>
                    <button style="width: 100%; padding: 12px; background: linear-gradient(135deg, #ff9a56 0%, #ff7e5f 100%); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                        Download Overall Report
                    </button>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Handle active nav link
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Sales Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        
        // Prepare monthly data
        const monthlyData = <?php echo json_encode($metrics['monthly_data']); ?>;
        const monthlyLabels = monthlyData.map(item => {
            const date = new Date(item.month + '-01');
            return date.toLocaleString('default', { month: 'long', year: 'numeric' });
        });
        const monthlyValues = monthlyData.map(item => parseInt(item.count));
        
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'Number of Heads',
                    data: monthlyValues,
                    borderColor: '#ff9a56',
                    backgroundColor: 'rgba(255, 154, 86, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#ff9a56'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#ecf0f1'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
