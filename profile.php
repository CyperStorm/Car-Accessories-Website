<?php
session_start();

// 1. Verify user session
if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

// 2. Connect to Database
$conn = new mysqli("localhost", "root", "", "workshopdb");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userId = (int) $_SESSION['user_id'];

// 3. Fetch User Details from `users` table (Removed 'phone')
$userStmt = $conn->prepare("SELECT username, email FROM users WHERE user_id = ?");
$userStmt->bind_param("i", $userId);
$userStmt->execute();
$userResult = $userStmt->get_result();
$userData = $userResult->fetch_assoc();
$userStmt->close();

// Fallback values if fields are missing
$username = $userData['username'] ?? ($_SESSION['username'] ?? 'User');
$email = $userData['email'] ?? ($_SESSION['email'] ?? '');

// Get user initial for avatar icon
$userInitial = !empty($username) ? strtoupper(substr($username, 0, 1)) : 'U';

// 4. Fetch Purchase History from `purchases` table
$orderStmt = $conn->prepare(
    "SELECT order_id, cart_data, total_price, created_at 
     FROM purchases 
     WHERE user_id = ? 
     ORDER BY created_at DESC"
);
$orderStmt->bind_param("i", $userId);
$orderStmt->execute();
$orders = $orderStmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Profile - MR Car Auto Accessories</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      background-image:
        linear-gradient(rgba(0, 0, 0, 0.85), rgba(0, 0, 0, 0.85)),
        url("img/dark_background.jpg");
      background-repeat: no-repeat;
      background-position: center;
      background-size: cover;
      background-attachment: fixed;
      font-family: 'Poppins', sans-serif;
      color: white;
      line-height: 1.7;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* Navigation */
    nav {
      background-color: rgba(10, 10, 10, 0.95);
      min-height: 70px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 12px 8%;
      position: sticky;
      top: 0;
      z-index: 1000;
      border-top: 1px solid #333;
      border-bottom: 1px solid #333;
    }

    .logo {
      color: white;
      font-size: 25px;
      font-weight: 800;
      text-decoration: none;
    }

    .logo span {
      color: #ff4d00;
    }

    nav ul {
      list-style: none;
      display: flex;
      gap: 30px;
    }

    nav ul li a {
      color: white;
      text-decoration: none;
      font-size: 15px;
      font-weight: 500;
      transition: color 0.3s ease;
    }

    nav ul li a:hover {
      color: #ff4d00;
    }

    .profile-btn {
      display: flex;
      align-items: center;
      gap: 8px;
      background-color: #ff4d00;
      color: #111;
      border: 1px solid #ff4d00;
      padding: 8px 18px;
      border-radius: 20px;
      text-decoration: none;
      font-size: 14px;
      font-weight: 600;
    }

    /* Main Container */
    .container {
      max-width: 1100px;
      margin: 40px auto 60px;
      padding: 0 20px;
      width: 100%;
      flex: 1;
    }

    .page-title {
      font-size: 38px;
      font-weight: 800;
      margin-bottom: 30px;
    }

    .page-title span {
      color: #ff4d00;
    }

    /* Layout Grid */
    .profile-grid {
      display: grid;
      grid-template-columns: 320px 1fr;
      gap: 30px;
    }

    @media (max-width: 850px) {
      .profile-grid {
        grid-template-columns: 1fr;
      }
    }

    /* Cards */
    .card {
      background-color: rgba(20, 20, 20, 0.94);
      border: 1px solid #333;
      border-radius: 12px;
      padding: 30px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.6);
    }

    /* User Info Card */
    .user-card {
      text-align: center;
      border-top: 4px solid #ff4d00;
    }

    .avatar-circle {
      width: 90px;
      height: 90px;
      border-radius: 50%;
      background-color: #ff4d00;
      color: white;
      font-size: 36px;
      font-weight: bold;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 20px;
      box-shadow: 0 0 15px rgba(255, 77, 0, 0.4);
    }

    .user-card h3 {
      font-size: 22px;
      margin-bottom: 4px;
    }

    .user-card p {
      color: #888;
      font-size: 14px;
      margin-bottom: 20px;
    }

    .btn-logout {
      margin-top: 25px;
      width: 100%;
      background-color: transparent;
      color: #ff4d00;
      border: 1px solid #ff4d00;
      padding: 10px;
      border-radius: 6px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn-logout:hover {
      background-color: #ff4d00;
      color: #111;
    }

    /* Purchase History Table */
    .history-card h2 {
      font-size: 22px;
      margin-bottom: 20px;
      color: #ffffff;
      border-bottom: 1px solid #333;
      padding-bottom: 12px;
    }

    .history-card h2 span {
      color: #ff4d00;
    }

    .table-responsive {
      overflow-x: auto;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      text-align: left;
      font-size: 14px;
    }

    th {
      background-color: #161616;
      color: #aaa;
      font-weight: 600;
      padding: 14px 16px;
      border-bottom: 2px solid #333;
    }

    td {
      padding: 16px;
      border-bottom: 1px solid #2a2a2a;
      color: #ddd;
    }

    tr:hover td {
      background-color: rgba(255, 77, 0, 0.05);
    }

    .empty-message {
      text-align: center;
      color: #aaa;
      padding: 30px 0;
    }

    footer {
      background-color: #080808;
      color: #888;
      text-align: center;
      padding: 20px;
      border-top: 1px solid #333;
      font-size: 14px;
    }
  </style>
</head>
<body>

  <header>
    <nav>
      <a class="logo" href="index.html">MR Car<span> Auto</span></a>
      <ul>
        <li><a href="index.html">Home</a></li>
        <li><a href="purchase.html">Purchase</a></li>
        <li><a href="about.html">About Us</a></li>
        <li><a href="contact.html">Contact</a></li>
      </ul>
      <a href="profile.php" class="profile-btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
          <circle cx="12" cy="7" r="4"></circle>
        </svg>
        <span>Profile</span>
      </a>
    </nav>
  </header>

  <main class="container">
    <h1 class="page-title">My <span>Account</span></h1>

    <div class="profile-grid">
      <!-- Left Column: User Information -->
      <div class="card user-card">
        <div class="avatar-circle"><?= htmlspecialchars($userInitial) ?></div>
        <h3><?= htmlspecialchars($username) ?></h3>
        <p><?= htmlspecialchars($email) ?></p>

        <button class="btn-logout" onclick="logoutUser()">Sign Out</button>
      </div>

      <!-- Right Column: Purchase History -->
      <div class="card history-card">
        <h2>Purchase <span>History</span></h2>
        
        <div class="table-responsive">
          <?php if ($orders && $orders->num_rows > 0): ?>
            <table>
              <thead>
                <tr>
                  <th>Order ID</th>
                  <th>Date</th>
                  <th>Items</th>
                  <th>Total</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($order = $orders->fetch_assoc()): ?>
                  <tr>
                    <td><?= htmlspecialchars($order['order_id']) ?></td>
                    <td><?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></td>
                    <td><?= htmlspecialchars($order['cart_data']) ?></td>
                    <td>RM <?= number_format((float)$order['total_price'], 2) ?></td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          <?php else: ?>
            <p class="empty-message">No purchases found.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </main>

  <footer>
    <p>© 2026 MR Car Auto Accessories. All Rights Reserved.</p>
  </footer>

  <script>
  async function logoutUser() {
      try {
          const response = await fetch("logout.php", {
              method: "POST"
          });
          window.location.href = "login.html";
      } catch (error) {
          console.error("Logout error:", error);
          window.location.href = "login.html";
      }
  }
  </script>

</body>
</html>
<?php
$orderStmt->close();
$conn->close();
?>