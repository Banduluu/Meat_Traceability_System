<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header('Location: Login.php');
    exit();
}

require_once 'databaseconnection.php';

$error_message = '';
$success_message = '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$database = new Database();
$conn = $database->getConnection();
$users = [];

if ($conn) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_type'])) {
        $form_type = $_POST['form_type'];

        if ($form_type === 'toggle_status') {
            $user_id = intval($_POST['user_id'] ?? 0);
            $current_status = trim($_POST['current_status'] ?? '');

            if ($user_id > 0 && $user_id !== intval($_SESSION['user_id']) && ($current_status === 'Active' || $current_status === 'Inactive')) {
                $new_status = ($current_status === 'Active') ? 'Inactive' : 'Active';
                $update_query = "UPDATE users SET account_status = ? WHERE user_id = ?";

                if ($update_stmt = $conn->prepare($update_query)) {
                    $update_stmt->bind_param('si', $new_status, $user_id);
                    if ($update_stmt->execute()) {
                        $action = ($new_status === 'Active') ? 'activated' : 'deactivated';
                        $success_message = 'User ' . $action . ' successfully.';
                    } else {
                        $error_message = 'Unable to update user status.';
                    }
                    $update_stmt->close();
                } else {
                    $error_message = 'Database error. Please try again later.';
                }
            } else {
                $error_message = 'Only active or inactive users can be toggled from this panel.';
            }
        } elseif ($form_type === 'delete_user') {
            $user_id = intval($_POST['user_id'] ?? 0);
            if ($user_id > 0 && $user_id !== intval($_SESSION['user_id'])) {
                $status_query = "SELECT account_status FROM users WHERE user_id = ? LIMIT 1";
                if ($status_stmt = $conn->prepare($status_query)) {
                    $status_stmt->bind_param('i', $user_id);
                    $status_stmt->execute();
                    $status_result = $status_stmt->get_result();

                    if ($status_result && $row = $status_result->fetch_assoc()) {
                        if ($row['account_status'] === 'Pending') {
                            $error_message = 'Pending users cannot be deleted from this panel.';
                        } else {
                            $delete_query = "DELETE FROM users WHERE user_id = ?";
                            if ($delete_stmt = $conn->prepare($delete_query)) {
                                $delete_stmt->bind_param('i', $user_id);
                                if ($delete_stmt->execute()) {
                                    $success_message = 'User removed successfully.';
                                } else {
                                    $error_message = 'Unable to delete that user.';
                                }
                                $delete_stmt->close();
                            } else {
                                $error_message = 'Database error. Please try again later.';
                            }
                        }
                    } else {
                        $error_message = 'User not found.';
                    }
                    $status_stmt->close();
                } else {
                    $error_message = 'Database error. Please try again later.';
                }
            } else {
                $error_message = 'You cannot delete your own account.';
            }
        }
    }

    $query = "SELECT user_id, full_name, username, role, branch_or_slaughterhouse, account_status, created_at FROM users WHERE account_status IN ('Active', 'Inactive')";
    if ($search !== '') {
        $query .= " AND (full_name LIKE ? OR username LIKE ? OR role LIKE ? OR branch_or_slaughterhouse LIKE ?)";
    }
    $query .= " ORDER BY created_at DESC";

    if ($stmt = $conn->prepare($query)) {
        if ($search !== '') {
            $term = '%' . $search . '%';
            $stmt->bind_param('ssss', $term, $term, $term, $term);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        $stmt->close();
    } else {
        $error_message = 'Unable to load users. Please try again later.';
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
    <title>User Management - Meat Traceability System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="users.css">
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
                    <li><a href="users.php" class="nav-link active"><i class="fas fa-users"></i> <span>Users</span></a></li>
                    <li><a href="approvals.php" class="nav-link"><i class="fas fa-check-square"></i> <span>User Approvals</span></a></li>
                    <li><a href="reports.php" class="nav-link"><i class="fas fa-chart-bar"></i> <span>Reports</span></a></li>
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
                <h1>User Management</h1>
                <div class="user-info">
                    <span><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                </div>
            </header>

            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <div class="users-actions">
                <form method="GET" class="search-form">
                    <input type="text" name="search" placeholder="Search users..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>

            <div class="users-grid">
                <section class="card user-card full-width">
                    <div class="card-header">
                        <h2>User Accounts</h2>
                        <p class="small-note">Total users: <?php echo count($users); ?></p>
                    </div>
                    <div class="table-wrap">
                        <table class="user-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Branch</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($users) > 0): ?>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                                            <td><?php echo htmlspecialchars($user['branch_or_slaughterhouse']); ?></td>
                                            <td><span class="status status-<?php echo strtolower($user['account_status']); ?>"><?php echo htmlspecialchars($user['account_status']); ?></span></td>
                                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                            <td class="actions-cell">
                                                <?php if ((($user['account_status'] === 'Active' || $user['account_status'] === 'Inactive') && intval($user['user_id']) !== intval($_SESSION['user_id']))): ?>
                                                    <form method="POST" class="action-form">
                                                        <input type="hidden" name="form_type" value="toggle_status">
                                                        <input type="hidden" name="user_id" value="<?php echo intval($user['user_id']); ?>">
                                                        <input type="hidden" name="current_status" value="<?php echo htmlspecialchars($user['account_status']); ?>">
                                                        <button type="submit" class="btn btn-tertiary">
                                                            <?php echo $user['account_status'] === 'Active' ? 'Deactivate' : 'Activate'; ?>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>

                                                <?php if (intval($user['user_id']) !== intval($_SESSION['user_id']) && $user['account_status'] !== 'Pending'): ?>
                                                    <form method="POST" class="action-form" onsubmit="return confirm('Delete this user?');">
                                                        <input type="hidden" name="form_type" value="delete_user">
                                                        <input type="hidden" name="user_id" value="<?php echo intval($user['user_id']); ?>">
                                                        <button type="submit" class="btn btn-danger">Delete</button>
                                                    </form>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="no-data">No users found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
