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
                    <span>John Doe</span>
                </div>
            </header>

            <!-- Dashboard Cards Top Row -->
            <div class="dashboard-cards">
                <div class="card">
                    <div class="card-title"><i class="fas fa-certificate"></i> Total encoded certificates today</div>
                    <div class="card-value">124</div>
                    <div class="card-subtitle"><i class="fas fa-arrow-up"></i> Update: 2:15 am</div>
                </div>
                <div class="card green">
                    <div class="card-title"><i class="fas fa-users"></i> Total Meat Heads Inspected</div>
                    <div class="card-value">356</div>
                    <div class="card-subtitle"><i class="fas fa-arrow-up"></i> Update: 2:15 am</div>
                </div>
                <div class="card pink">
                    <div class="card-title"><i class="fas fa-check-circle"></i> Approved inspections</div>
                    <div class="card-value">298</div>
                    <div class="card-subtitle"><i class="fas fa-arrow-up"></i> Update: 2:15 am</div>
                </div>
            </div>

            <!-- Dashboard Cards Bottom Row -->
            <div class="dashboard-cards">
                <div class="card cyan">
                    <div class="card-title"><i class="fas fa-exclamation-circle"></i> Needs review or rejected</div>
                    <div class="card-value">58</div>
                    <div class="card-subtitle"><i class="fas fa-arrow-up"></i> Update: 2:15 am</div>
                </div>
                <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-title"><i class="fas fa-users"></i> Active Inspectors</div>
                    <div style="font-size: 24px; font-weight: 700; margin-bottom: 12px;">6 Working / 2 Idle</div>
                    <div class="card-subtitle"><i class="fas fa-arrow-up"></i> Real-time status</div>
                </div>
                <div class="card" style="background: linear-gradient(135deg, #d0d0d0 0%, #e0e0e0 100%);">
                    <div class="card-title" style="color: #333;"><i class="fas fa-layer-group"></i> Today's Meat Categories</div>
                    <div style="font-size: 14px; color: #333; line-height: 1.8; margin-top: 10px;">
                        <div><strong>Pork</strong> - 120</div>
                        <div><strong>Beef</strong> - 95</div>
                        <div><strong>Chicken</strong> - 80</div>
                        <div><strong>Others</strong> - 61</div>
                    </div>
                </div>
            </div>

            <!-- Number of Heads Section -->
            <div class="section">
                <div class="section-header">
                    <h2>Number of Heads Per Month</h2>
                    <div class="section-header">
                        <div class="actions">
                            <a href="#">Monthly Report</a>
                        </div>
                    </div>
                </div>
                <div class="section-content grid-2">
                    <div class="chart-container">
                        <canvas id="salesChart"></canvas>
                    </div>
                    <div>
                        <p style="color: #7f8c8d; margin-bottom: 20px; line-height: 1.6;">
                            Monthly tracking of total meat heads inspected and processed. This chart displays the trend across different months to help monitor inspection volume and patterns.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Application Sales & User Activity -->
            <div class="grid-2">
                <!-- Application Sales -->
                <div class="section">
                    <div class="section-header">
                        <h2>Application Sales</h2>
                    </div>
                    <div class="section-content">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Application</th>
                                        <th>Sales</th>
                                        <th>Change</th>
                                        <th>Avg Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Apple Pro</td>
                                        <td>16,300</td>
                                        <td><canvas id="miniChart1" width="50" height="25"></canvas></td>
                                        <td>$51</td>
                                        <td style="color: #3498db; font-weight: 600;">$15,852</td>
                                    </tr>
                                    <tr>
                                        <td>Photoshop</td>
                                        <td>26,423</td>
                                        <td><canvas id="miniChart2" width="50" height="25"></canvas></td>
                                        <td>$35</td>
                                        <td style="color: #3498db; font-weight: 600;">$18,789</td>
                                    </tr>
                                    <tr>
                                        <td>Guidable</td>
                                        <td>8,260</td>
                                        <td><canvas id="miniChart3" width="50" height="25"></canvas></td>
                                        <td>$99</td>
                                        <td style="color: #3498db; font-weight: 600;">$9,632</td>
                                    </tr>
                                    <tr>
                                        <td>Feasible</td>
                                        <td>16,652</td>
                                        <td><canvas id="miniChart4" width="50" height="25"></canvas></td>
                                        <td>$30</td>
                                        <td style="color: #3498db; font-weight: 600;">$7,856</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <p style="color: #3498db; text-align: center; margin-top: 15px; font-size: 14px; cursor: pointer;">
                            <i class="fas fa-eye"></i> View all Projects
                        </p>
                    </div>
                </div>

                <!-- User Activity -->
                <div class="section">
                    <div class="section-header">
                        <h2>User Activity</h2>
                    </div>
                    <div class="section-content">
                        <div class="activity-item">
                            <div class="activity-avatar">JD</div>
                            <div class="activity-content">
                                <h4>John Doe</h4>
                                <p>Lorem Ipsum is simply dummy text...</p>
                                <div class="activity-time">2 min ago</div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-avatar">JD</div>
                            <div class="activity-content">
                                <h4>John Dea</h4>
                                <p>Lorem Ipsum is simply dummy text...</p>
                                <div class="activity-time">2 min ago</div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-avatar">JD</div>
                            <div class="activity-content">
                                <h4>John Dea</h4>
                                <p>Lorem Ipsum is simply dummy text...</p>
                                <div class="activity-time">2 min ago</div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-avatar">JD</div>
                            <div class="activity-content">
                                <h4>John Dea</h4>
                                <p>Lorem Ipsum is simply dummy text...</p>
                                <div class="activity-time">2 min ago</div>
                            </div>
                        </div>
                        <p style="color: #3498db; text-align: center; margin-top: 15px; font-size: 14px; cursor: pointer;">
                            <i class="fas fa-eye"></i> View all Projects
                        </p>
                    </div>
                </div>
            </div>

            <!-- Latest Updates & Information -->
            <div class="grid-2">
                <!-- Latest Updates -->
                <div class="section">
                    <div class="section-header">
                        <h2>Latest Updates</h2>
                    </div>
                    <div class="section-content">
                        <div style="display: flex; gap: 15px; padding: 15px 0; border-bottom: 1px solid #ecf0f1;">
                            <div style="width: 8px; background: #e74c3c; border-radius: 4px;"></div>
                            <div>
                                <div style="color: #2c3e50; font-weight: 600; margin-bottom: 5px;">4 hrs ago</div>
                                <div style="color: #7f8c8d; font-size: 14px;">+ 3 New Products were added!</div>
                            </div>
                        </div>
                        <div style="display: flex; gap: 15px; padding: 15px 0;">
                            <div style="width: 8px; background: #f39c12; border-radius: 4px;"></div>
                            <div>
                                <div style="color: #2c3e50; font-weight: 600; margin-bottom: 5px;">1 day ago</div>
                                <div style="color: #7f8c8d; font-size: 14px;">Database backup completed!</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Information -->
                <div class="section">
                    <div class="section-header">
                        <h2>Information</h2>
                    </div>
                    <div class="section-content">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                            <div>
                                <div style="color: #7f8c8d; font-size: 13px; margin-bottom: 5px;">Email</div>
                                <div style="color: #2c3e50; font-weight: 600;">john@gmail.com</div>
                            </div>
                            <div>
                                <div style="color: #7f8c8d; font-size: 13px; margin-bottom: 5px;">Phone</div>
                                <div style="color: #2c3e50; font-weight: 600;">888-333-5736</div>
                            </div>
                        </div>
                        <button style="width: 100%; padding: 12px; background: linear-gradient(135deg, #ff9a56 0%, #ff7e5f 100%); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                            Download Overall Report
                        </button>
                    </div>
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
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                datasets: [{
                    label: 'Number of Heads',
                    data: [280, 320, 295, 350, 380, 410, 445, 420, 390, 410, 435, 470],
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

        // Mini charts in table
        const miniCharts = [
            { el: 'miniChart1', data: [15, 12, 14, 16, 15, 14] },
            { el: 'miniChart2', data: [10, 15, 12, 16, 14, 18] },
            { el: 'miniChart3', data: [18, 16, 14, 16, 15, 12] },
            { el: 'miniChart4', data: [12, 14, 16, 15, 14, 13] }
        ];

        miniCharts.forEach(chart => {
            const ctx = document.getElementById(chart.el).getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['', '', '', '', '', ''],
                    datasets: [{
                        data: chart.data,
                        borderColor: '#3498db',
                        backgroundColor: 'rgba(52, 152, 219, 0.1)',
                        borderWidth: 1,
                        fill: true,
                        pointRadius: 0,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: false
                        }
                    },
                    scales: {
                        y: {
                            display: false
                        },
                        x: {
                            display: false
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
