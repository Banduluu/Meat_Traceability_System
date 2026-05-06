<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: Login.php');
    exit();
}

require_once 'databaseconnection.php';

$database = new Database();
$conn = $database->getConnection();

$error_message = '';
$reports_data = [];
$price_per_meat = 500; // Default price per meat in currency units

if ($conn) {
    // Today's metrics
    $today_query = "SELECT COUNT(*) as total FROM meat_products WHERE DATE(created_at) = CURDATE()";
    $today_result = $conn->query($today_query);
    $today_count = $today_result ? $today_result->fetch_assoc()['total'] : 0;

    // This month metrics
    $month_query = "SELECT COUNT(*) as total FROM meat_products WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
    $month_result = $conn->query($month_query);
    $month_count = $month_result ? $month_result->fetch_assoc()['total'] : 0;

    // Total all time
    $total_query = "SELECT COUNT(*) as total FROM meat_products";
    $total_result = $conn->query($total_query);
    $total_count = $total_result ? $total_result->fetch_assoc()['total'] : 0;

    // Daily income calculation
    $daily_income = $today_count * $price_per_meat;

    // Monthly income calculation
    $monthly_income = $month_count * $price_per_meat;

    // Approved inspections today
    $approved_today_query = "SELECT COUNT(*) as total FROM meat_inspection_certificates WHERE DATE(created_at) = CURDATE() AND verification_remarks IS NOT NULL AND verification_remarks != ''";
    $approved_today_result = $conn->query($approved_today_query);
    $approved_today = $approved_today_result ? $approved_today_result->fetch_assoc()['total'] : 0;

    // Get daily breakdown for chart (last 30 days)
    $daily_breakdown_query = "SELECT DATE(created_at) as date, COUNT(*) as count FROM meat_products WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) GROUP BY DATE(created_at) ORDER BY date ASC";
    $daily_breakdown_result = $conn->query($daily_breakdown_query);
    $daily_breakdown = [];
    if ($daily_breakdown_result) {
        while ($row = $daily_breakdown_result->fetch_assoc()) {
            $daily_breakdown[] = $row;
        }
    }

    // Get category breakdown for today
    $category_query = "SELECT product_name, COUNT(*) as count FROM meat_products WHERE DATE(created_at) = CURDATE() GROUP BY product_name";
    $category_result = $conn->query($category_query);
    $category_breakdown = [];
    if ($category_result) {
        while ($row = $category_result->fetch_assoc()) {
            $category_breakdown[] = $row;
        }
    }

    // Get top performing days
    $top_days_query = "SELECT DATE(created_at) as date, COUNT(*) as count FROM meat_products GROUP BY DATE(created_at) ORDER BY count DESC LIMIT 7";
    $top_days_result = $conn->query($top_days_query);
    $top_days = [];
    if ($top_days_result) {
        while ($row = $top_days_result->fetch_assoc()) {
            $top_days[] = $row;
        }
    }

    $database->closeConnection();
} else {
    $error_message = 'Unable to connect to database. Please try again later.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Meat Traceability System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="reports.css">
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
                    <li><a href="dashboard.php" class="nav-link"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
                    <li><a href="users.php" class="nav-link"><i class="fas fa-users"></i> <span>Users</span></a></li>
                    <li><a href="approvals.php" class="nav-link"><i class="fas fa-check-square"></i> <span>User Approvals</span></a></li>
                    <li><a href="reports.php" class="nav-link active"><i class="fas fa-chart-bar"></i> <span>Reports</span></a></li>
                    <li><a href="MIC viewer.php" class="nav-link"><i class="fas fa-microscope"></i> <span>MIC Viewer</span></a></li>
                    <li><a href="daily-anti-mortem-report.php" class="nav-link"><i class="fas fa-file-alt"></i> <span>Daily Anti-Mortem Report</span></a></li>
                    <li><a href="#" class="nav-link"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
                    <li style="margin-top: 50px; border-top: 1px solid rgba(255, 255, 255, 0.1); padding-top: 20px;">
                        <a href="logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
                    </li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <header class="header">
                <h1>Reports</h1>
                <div class="user-info">
                    <span><?php echo htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?></span>
                </div>
            </header>

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <!-- Key Metrics Cards -->
            <div class="dashboard-cards">
                <button class="card metric-card" onclick="showModal('todayModal')" style="background: linear-gradient(135deg, #ff9a56 0%, #ff7e5f 100%);">
                    <div class="card-title"><i class="fas fa-cut"></i> Meats Processed Today</div>
                    <div class="card-value"><?php echo $today_count; ?></div>
                    <div class="card-subtitle"><i class="fas fa-info-circle"></i> Click to view details</div>
                </button>
                <button class="card green metric-card" onclick="showModal('monthModal')">
                    <div class="card-title"><i class="fas fa-calendar-alt"></i> Meats This Month</div>
                    <div class="card-value"><?php echo $month_count; ?></div>
                    <div class="card-subtitle"><i class="fas fa-info-circle"></i> Click to view details</div>
                </button>
                <button class="card pink metric-card" onclick="showModal('totalModal')">
                    <div class="card-title"><i class="fas fa-box"></i> Total Meats All Time</div>
                    <div class="card-value"><?php echo $total_count; ?></div>
                    <div class="card-subtitle"><i class="fas fa-info-circle"></i> Click to view details</div>
                </button>
            </div>

            <!-- Income Metrics -->
            <div class="dashboard-cards">
                <button class="card cyan metric-card" onclick="showModal('dailyIncomeModal')">
                    <div class="card-title"><i class="fas fa-money-bill-wave"></i> Daily Estimated Income</div>
                    <div class="card-value" style="color: #27ae60; font-size: 28px;">₱<?php echo number_format($daily_income, 2); ?></div>
                    <div class="card-subtitle"><i class="fas fa-info-circle"></i> Click to view breakdown</div>
                </button>
                <button class="card metric-card" onclick="showModal('monthlyIncomeModal')" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <div class="card-title"><i class="fas fa-chart-line"></i> Monthly Estimated Income</div>
                    <div class="card-value" style="color: #ffffff;">₱<?php echo number_format($monthly_income, 2); ?></div>
                    <div class="card-subtitle"><i class="fas fa-info-circle"></i> Click to view breakdown</div>
                </button>
                <button class="card metric-card" onclick="showModal('approvedModal')" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <div class="card-title"><i class="fas fa-check-circle"></i> Approved Inspections Today</div>
                    <div class="card-value"><?php echo $approved_today; ?></div>
                    <div class="card-subtitle"><i class="fas fa-info-circle"></i> Click to view details</div>
                </button>
            </div>

            <!-- Charts Section -->
            <div class="grid-2">
                <div class="section">
                    <div class="section-header">
                        <h2>Daily Processing Trend (Last 30 Days)</h2>
                    </div>
                    <div class="section-content">
                        <div style="position: relative; height: 300px;">
                            <canvas id="dailyTrendChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="section">
                    <div class="section-header">
                        <h2>Today's Meat Categories</h2>
                    </div>
                    <div class="section-content">
                        <div style="position: relative; height: 300px;">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Performing Days -->
            <div class="section">
                <div class="section-header">
                    <h2>Top Processing Days</h2>
                </div>
                <div class="section-content">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8f9fa; border-bottom: 2px solid #ecf0f1;">
                                <th style="padding: 12px; text-align: left; font-weight: 600;">Date</th>
                                <th style="padding: 12px; text-align: left; font-weight: 600;">Meats Processed</th>
                                <th style="padding: 12px; text-align: left; font-weight: 600;">Estimated Income</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($top_days) > 0): ?>
                                <?php foreach ($top_days as $day): ?>
                                    <tr style="border-bottom: 1px solid #ecf0f1;">
                                        <td style="padding: 12px;"><?php echo date('M d, Y', strtotime($day['date'])); ?></td>
                                        <td style="padding: 12px;"><strong><?php echo $day['count']; ?></strong></td>
                                        <td style="padding: 12px; color: #27ae60; font-weight: 600;">₱<?php echo number_format($day['count'] * $price_per_meat, 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" style="text-align: center; padding: 20px; color: #7f8c8d;">No data available</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Category Breakdown Table -->
            <div class="section">
                <div class="section-header">
                    <h2>Today's Category Breakdown</h2>
                </div>
                <div class="section-content">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8f9fa; border-bottom: 2px solid #ecf0f1;">
                                <th style="padding: 12px; text-align: left; font-weight: 600;">Product Name</th>
                                <th style="padding: 12px; text-align: left; font-weight: 600;">Count</th>
                                <th style="padding: 12px; text-align: left; font-weight: 600;">Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($category_breakdown) > 0): ?>
                                <?php $category_total = array_sum(array_column($category_breakdown, 'count')); ?>
                                <?php foreach ($category_breakdown as $category): ?>
                                    <tr style="border-bottom: 1px solid #ecf0f1;">
                                        <td style="padding: 12px;"><?php echo htmlspecialchars($category['product_name']); ?></td>
                                        <td style="padding: 12px;"><strong><?php echo $category['count']; ?></strong></td>
                                        <td style="padding: 12px;">
                                            <div style="display: flex; align-items: center; gap: 8px;">
                                                <div style="width: 100px; height: 8px; background: #ecf0f1; border-radius: 4px; position: relative;">
                                                    <div style="width: <?php echo ($category_total > 0 ? ($category['count'] / $category_total) * 100 : 0); ?>%; height: 100%; background: #3498db; border-radius: 4px;"></div>
                                                </div>
                                                <?php echo number_format(($category_total > 0 ? ($category['count'] / $category_total) * 100 : 0), 1); ?>%
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" style="text-align: center; padding: 20px; color: #7f8c8d;">No data available for today</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal: Today's Meats -->
    <div id="todayModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Meats Processed Today</h2>
                <button class="modal-close" onclick="closeModal('todayModal')">&times;</button>
            </div>
            <div class="filter-section" style="margin-bottom: 16px; padding: 12px; background: #f8f9fa; border-radius: 8px;">
                <label for="meatFilter" style="font-weight: 600; margin-right: 8px;">Filter by Meat Type:</label>
                <select id="meatFilter" onchange="filterTodayMeats()" style="padding: 6px 12px; border: 1px solid #ddd; border-radius: 4px;">
                    <option value="">All Types</option>
                    <option value="Pork">🐖 Pork</option>
                    <option value="Chicken">🍗 Chicken</option>
                    <option value="Goat">🐑 Goat</option>
                    <option value="Beef">🐄 Beef</option>

                </select>
            </div>
            <div id="todayModalContent">
                <div class="breakdown-item">
                    <span class="breakdown-label">Total Count:</span>
                    <span class="breakdown-value"><?php echo $today_count; ?></span>
                </div>
                <?php if (count($category_breakdown) > 0): ?>
                    <div style="margin-top: 16px; padding-top: 16px; border-top: 2px solid #ecf0f1;">
                        <h3 style="margin: 0 0 12px 0;">Meat Types Processed:</h3>
                        <?php
                        $meat_icons = [
                            'Pork' => 'fas fa-pig',
                            'Chicken' => 'fas fa-drumstick-bite',
                            'Cow' => 'fas fa-cow',
                            'Goat' => 'fas fa-sheep',
                            'Beef' => 'fas fa-cow',
                            'Lamb' => 'fas fa-sheep',
                            'Turkey' => 'fas fa-drumstick-bite',
                            'Fish' => 'fas fa-fish',
                            'Other' => 'fas fa-utensils'
                        ];
                        ?>
                        <?php foreach ($category_breakdown as $cat): ?>
                            <div class="breakdown-item">
                                <span class="breakdown-label">
                                    <i class="<?php echo $meat_icons[$cat['product_name']] ?? 'fas fa-utensils'; ?>" style="margin-right: 8px;"></i>
                                    <?php echo htmlspecialchars($cat['product_name']); ?>
                                </span>
                                <span class="breakdown-value"><?php echo $cat['count']; ?> heads</span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="margin-top: 16px; padding-top: 16px; border-top: 2px solid #ecf0f1;">
                        <p style="color: #7f8c8d; margin: 0;">No meats processed today.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal: This Month's Meats -->
    <div id="monthModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Meats Processed This Month</h2>
                <button class="modal-close" onclick="closeModal('monthModal')">&times;</button>
            </div>
            <div class="breakdown-item">
                <span class="breakdown-label">Monthly Total:</span>
                <span class="breakdown-value"><?php echo $month_count; ?></span>
            </div>
            <div style="margin-top: 16px; padding-top: 16px; border-top: 2px solid #ecf0f1;">
                <h3 style="margin: 0 0 12px 0;">Daily Breakdown:</h3>
                <?php if (count($daily_breakdown) > 0): ?>
                    <?php foreach ($daily_breakdown as $day): ?>
                        <div class="breakdown-item">
                            <span class="breakdown-label"><?php echo date('M d, Y', strtotime($day['date'])); ?></span>
                            <span class="breakdown-value"><?php echo $day['count']; ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: #7f8c8d;">No data available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal: Total All Time -->
    <div id="totalModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Total Meats All Time</h2>
                <button class="modal-close" onclick="closeModal('totalModal')">&times;</button>
            </div>
            <div class="breakdown-item">
                <span class="breakdown-label">Complete Total:</span>
                <span class="breakdown-value"><?php echo $total_count; ?></span>
            </div>
            <div style="margin-top: 16px; padding-top: 16px; border-top: 2px solid #ecf0f1;">
                <h3 style="margin: 0 0 12px 0;">Top Processing Days:</h3>
                <?php if (count($top_days) > 0): ?>
                    <?php foreach (array_slice($top_days, 0, 5) as $day): ?>
                        <div class="breakdown-item">
                            <span class="breakdown-label"><?php echo date('M d, Y', strtotime($day['date'])); ?></span>
                            <span class="breakdown-value"><?php echo $day['count']; ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal: Daily Income -->
    <div id="dailyIncomeModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Daily Estimated Income</h2>
                <button class="modal-close" onclick="closeModal('dailyIncomeModal')">&times;</button>
            </div>
            <div class="filter-section" style="margin-bottom: 16px; padding: 12px; background: #f8f9fa; border-radius: 8px;">
                <label for="incomeMeatFilter" style="font-weight: 600; margin-right: 8px;">Filter by Meat Type:</label>
                <select id="incomeMeatFilter" onchange="filterDailyIncome()" style="padding: 6px 12px; border: 1px solid #ddd; border-radius: 4px;">
                    <option value="">All Types</option>
                    <option value="Pork">🐖 Pork</option>
                    <option value="Chicken">🍗 Chicken</option>
                    <option value="Goat">🐑 Goat</option>
                    <option value="Beef">🐄 Beef</option>

                </select>
            </div>
            <div id="dailyIncomeModalContent">
                <div class="breakdown-item">
                    <span class="breakdown-label">Total Income:</span>
                    <span class="breakdown-value">₱<?php echo number_format($daily_income, 2); ?></span>
                </div>
                <div style="margin-top: 16px; padding-top: 16px; border-top: 2px solid #ecf0f1;">
                    <h3 style="margin: 0 0 12px 0;">Income Calculation:</h3>
                    <div class="breakdown-item">
                        <span class="breakdown-label">Meats Processed:</span>
                        <span class="breakdown-value"><?php echo $today_count; ?></span>
                    </div>
                    <div class="breakdown-item">
                        <span class="breakdown-label">Price Per Meat:</span>
                        <span class="breakdown-value">₱<?php echo number_format($price_per_meat, 2); ?></span>
                    </div>
                    <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #ecf0f1; background: #f8f9fa; padding: 12px; border-radius: 8px;">
                        <strong><?php echo $today_count; ?> × ₱<?php echo number_format($price_per_meat, 2); ?> = ₱<?php echo number_format($daily_income, 2); ?></strong>
                    </div>
                </div>
                <?php if (count($category_breakdown) > 0): ?>
                    <div style="margin-top: 16px; padding-top: 16px; border-top: 2px solid #ecf0f1;">
                        <h3 style="margin: 0 0 12px 0;">Income by Meat Type:</h3>
                        <?php
                        $meat_icons = [
                            'Pork' => 'fas fa-pig',
                            'Chicken' => 'fas fa-drumstick-bite',
                            'Cow' => 'fas fa-cow',
                            'Goat' => 'fas fa-sheep',
                            'Beef' => 'fas fa-cow',
                            'Lamb' => 'fas fa-sheep',
                            'Turkey' => 'fas fa-drumstick-bite',
                            'Fish' => 'fas fa-fish',
                            'Other' => 'fas fa-utensils'
                        ];
                        ?>
                        <?php foreach ($category_breakdown as $cat): ?>
                            <div class="breakdown-item">
                                <span class="breakdown-label">
                                    <i class="<?php echo $meat_icons[$cat['product_name']] ?? 'fas fa-utensils'; ?>" style="margin-right: 8px;"></i>
                                    <?php echo htmlspecialchars($cat['product_name']); ?> (<?php echo $cat['count']; ?> heads)
                                </span>
                                <span class="breakdown-value">₱<?php echo number_format($cat['count'] * $price_per_meat, 2); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal: Monthly Income -->
    <div id="monthlyIncomeModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Monthly Estimated Income</h2>
                <button class="modal-close" onclick="closeModal('monthlyIncomeModal')">&times;</button>
            </div>
            <div class="breakdown-item">
                <span class="breakdown-label">Total Income:</span>
                <span class="breakdown-value">₱<?php echo number_format($monthly_income, 2); ?></span>
            </div>
            <div style="margin-top: 16px; padding-top: 16px; border-top: 2px solid #ecf0f1;">
                <h3 style="margin: 0 0 12px 0;">Income Calculation:</h3>
                <div class="breakdown-item">
                    <span class="breakdown-label">Meats This Month:</span>
                    <span class="breakdown-value"><?php echo $month_count; ?></span>
                </div>
                <div class="breakdown-item">
                    <span class="breakdown-label">Price Per Meat:</span>
                    <span class="breakdown-value">₱<?php echo number_format($price_per_meat, 2); ?></span>
                </div>
                <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #ecf0f1; background: #f8f9fa; padding: 12px; border-radius: 8px;">
                    <strong><?php echo $month_count; ?> × ₱<?php echo number_format($price_per_meat, 2); ?> = ₱<?php echo number_format($monthly_income, 2); ?></strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Approved Inspections -->
    <div id="approvedModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Approved Inspections Today</h2>
                <button class="modal-close" onclick="closeModal('approvedModal')">&times;</button>
            </div>
            <div class="breakdown-item">
                <span class="breakdown-label">Total Approved:</span>
                <span class="breakdown-value"><?php echo $approved_today; ?></span>
            </div>
            <div style="margin-top: 16px; padding-top: 16px; border-top: 2px solid #ecf0f1;">
                <h3 style="margin: 0 0 12px 0;">Status:</h3>
                <p style="color: #7f8c8d; margin: 0;">These are certificates that have been verified and approved with remarks today.</p>
            </div>
        </div>
    </div>

    <script>
        function showModal(modalId) {
            document.getElementById(modalId).classList.add('show');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('show');
        }

        // Close modal when clicking outside the modal-content
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.remove('show');
            }
        }

        // Filter functions
        function filterTodayMeats() {
            const meatType = document.getElementById('meatFilter').value;
            const modalContent = document.getElementById('todayModalContent');

            if (!meatType) {
                // Reset to original content
                location.reload();
                return;
            }

            // Show loading
            modalContent.innerHTML = '<div style="text-align: center; padding: 20px;"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';

            // AJAX request to get filtered data
            fetch('reports.php?ajax=filter_today&meat_type=' + encodeURIComponent(meatType))
                .then(response => response.json())
                .then(data => {
                    updateTodayModal(data);
                })
                .catch(error => {
                    modalContent.innerHTML = '<div style="text-align: center; padding: 20px; color: #e74c3c;">Error loading data</div>';
                });
        }

        function filterDailyIncome() {
            const meatType = document.getElementById('incomeMeatFilter').value;
            const modalContent = document.getElementById('dailyIncomeModalContent');

            if (!meatType) {
                // Reset to original content
                location.reload();
                return;
            }

            // Show loading
            modalContent.innerHTML = '<div style="text-align: center; padding: 20px;"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';

            // AJAX request to get filtered data
            fetch('reports.php?ajax=filter_income&meat_type=' + encodeURIComponent(meatType))
                .then(response => response.json())
                .then(data => {
                    updateIncomeModal(data);
                })
                .catch(error => {
                    modalContent.innerHTML = '<div style="text-align: center; padding: 20px; color: #e74c3c;">Error loading data</div>';
                });
        }

        function updateTodayModal(data) {
            const modalContent = document.getElementById('todayModalContent');
            let html = '';

            html += '<div class="breakdown-item">';
            html += '<span class="breakdown-label">Filtered Count (' + data.meat_type + '):</span>';
            html += '<span class="breakdown-value">' + data.count + '</span>';
            html += '</div>';

            if (data.count > 0) {
                html += '<div style="margin-top: 16px; padding-top: 16px; border-top: 2px solid #ecf0f1;">';
                html += '<h3 style="margin: 0 0 12px 0;">Filtered Results:</h3>';
                html += '<div class="breakdown-item">';
                html += '<span class="breakdown-label">';
                html += '<i class="' + getMeatIcon(data.meat_type) + '" style="margin-right: 8px;"></i>';
                html += data.meat_type;
                html += '</span>';
                html += '<span class="breakdown-value">' + data.count + ' heads</span>';
                html += '</div>';
                html += '</div>';
            } else {
                html += '<div style="margin-top: 16px; padding-top: 16px; border-top: 2px solid #ecf0f1;">';
                html += '<p style="color: #7f8c8d; margin: 0;">No ' + data.meat_type.toLowerCase() + ' processed today.</p>';
                html += '</div>';
            }

            modalContent.innerHTML = html;
        }

        function updateIncomeModal(data) {
            const modalContent = document.getElementById('dailyIncomeModalContent');
            let html = '';

            html += '<div class="breakdown-item">';
            html += '<span class="breakdown-label">Filtered Income (' + data.meat_type + '):</span>';
            html += '<span class="breakdown-value">₱' + data.income.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') + '</span>';
            html += '</div>';

            html += '<div style="margin-top: 16px; padding-top: 16px; border-top: 2px solid #ecf0f1;">';
            html += '<h3 style="margin: 0 0 12px 0;">Income Calculation:</h3>';
            html += '<div class="breakdown-item">';
            html += '<span class="breakdown-label">' + data.meat_type + ' Processed:</span>';
            html += '<span class="breakdown-value">' + data.count + '</span>';
            html += '</div>';
            html += '<div class="breakdown-item">';
            html += '<span class="breakdown-label">Price Per Meat:</span>';
            html += '<span class="breakdown-value">₱' + data.price_per_meat.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') + '</span>';
            html += '</div>';
            html += '<div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #ecf0f1; background: #f8f9fa; padding: 12px; border-radius: 8px;">';
            html += '<strong>' + data.count + ' × ₱' + data.price_per_meat.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') + ' = ₱' + data.income.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') + '</strong>';
            html += '</div>';
            html += '</div>';

            if (data.count > 0) {
                html += '<div style="margin-top: 16px; padding-top: 16px; border-top: 2px solid #ecf0f1;">';
                html += '<h3 style="margin: 0 0 12px 0;">Income Breakdown:</h3>';
                html += '<div class="breakdown-item">';
                html += '<span class="breakdown-label">';
                html += '<i class="' + getMeatIcon(data.meat_type) + '" style="margin-right: 8px;"></i>';
                html += data.meat_type + ' (' + data.count + ' heads)';
                html += '</span>';
                html += '<span class="breakdown-value">₱' + data.income.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') + '</span>';
                html += '</div>';
                html += '</div>';
            }

            modalContent.innerHTML = html;
        }

        function getMeatIcon(meatType) {
            const icons = {
                'Pork': 'fas fa-pig',
                'Chicken': 'fas fa-drumstick-bite',
                'Cow': 'fas fa-cow',
                'Goat': 'fas fa-sheep',
                'Beef': 'fas fa-cow',
                'Lamb': 'fas fa-sheep',
                'Turkey': 'fas fa-drumstick-bite',
                'Fish': 'fas fa-fish',
                'Other': 'fas fa-utensils'
            };
            return icons[meatType] || 'fas fa-utensils';
        }

        // Original charts script

        // Daily Trend Chart
        const dailyTrendCtx = document.getElementById('dailyTrendChart').getContext('2d');
        const dailyData = <?php echo json_encode($daily_breakdown); ?>;
        const dailyLabels = dailyData.map(item => {
            const date = new Date(item.date);
            return date.toLocaleDateString('default', { month: 'short', day: 'numeric' });
        });
        const dailyValues = dailyData.map(item => parseInt(item.count));

        new Chart(dailyTrendCtx, {
            type: 'line',
            data: {
                labels: dailyLabels,
                datasets: [{
                    label: 'Meats Processed',
                    data: dailyValues,
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    pointBackgroundColor: '#3498db'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
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

        // Category Chart
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        const categoryData = <?php echo json_encode($category_breakdown); ?>;
        const categoryLabels = categoryData.map(item => item.product_name);
        const categoryValues = categoryData.map(item => parseInt(item.count));
        const colors = ['#ff9a56', '#3498db', '#27ae60', '#e74c3c', '#f39c12', '#9b59b6'];

        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: categoryLabels,
                datasets: [{
                    data: categoryValues,
                    backgroundColor: colors.slice(0, categoryLabels.length),
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</body>
</html>
