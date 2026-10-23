<?php
session_start();

header('Content-Type: application/json');

// 1. Verify user session
if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Please log in to place an order.']);
    exit;
}

// 2. Database Connection
$conn = new mysqli("localhost", "root", "", "workshopdb");

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

// Helper function to generate unique Order ID (e.g., ORD-8F92A1B3)
function generateOrderId($conn) {
    do {
        $random_str = strtoupper(bin2hex(random_bytes(4)));
        $order_id = "ORD-" . $random_str;

        // Check if generated ID already exists
        $check_stmt = $conn->prepare("SELECT order_id FROM purchases WHERE order_id = ?");
        $check_stmt->bind_param("s", $order_id);
        $check_stmt->execute();
        $check_stmt->store_result();
        $exists = $check_stmt->num_rows > 0;
        $check_stmt->close();
    } while ($exists);

    return $order_id;
}

// 3. Read input payload
$input = json_decode(file_get_contents('php://input'), true);

$user_id        = $_SESSION['user_id'];
$customer_name  = trim($input['customerName'] ?? '');
$customer_phone = trim($input['customerPhone'] ?? '');
$address        = trim($input['address'] ?? '');
$payment_method = trim($input['paymentMethod'] ?? '');
$cart           = $input['cart'] ?? [];

// 4. Input Validation
if (!$customer_name || !$customer_phone || !$address || !$payment_method || empty($cart)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please complete all required fields.']);
    exit;
}

// 5. Generate unique Order ID & format cart data
$order_id = generateOrderId($conn);
$total_price = 0;
$item_strings = [];

foreach ($cart as $item) {
    $total_price += ($item['price'] * $item['quantity']);
    $item_strings[] = $item['name'] . ":" . $item['quantity'];
}

$formatted_cart = implode(", ", $item_strings);

// 6. Insert Purchase Record
$stmt = $conn->prepare(
    "INSERT INTO purchases (order_id, user_id, customer_name, customer_phone, address, payment_method, cart_data, total_price) 
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Prepare statement failed: ' . $conn->error]);
    exit;
}

// Bind parameters: order_id (s), user_id (i), name (s), phone (s), address (s), method (s), cart (s), total (d) -> 8 items: "sisssssd"
$stmt->bind_param("sisssssd", $order_id, $user_id, $customer_name, $customer_phone, $address, $payment_method, $formatted_cart, $total_price);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true, 
        'message' => 'Order placed successfully!',
        'order_id' => $order_id
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to save order: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>