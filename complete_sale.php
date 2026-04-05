<?php
date_default_timezone_set('America/New_York');
session_start();

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

require_once "db_connection.php";

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || empty($data['items'])) {
    echo json_encode(['success' => false, 'message' => 'No items in cart']);
    exit;
}

$total_amount = 0;
$items = $data['items'];

foreach ($items as $item) {
    $total_amount += (float)$item['price'] * (int)$item['qty'];
}

$conn->begin_transaction();

try {
    // Reduce stock
    foreach ($items as $item) {
        $name = $conn->real_escape_string($item['name']);
        $qty = (int)$item['qty'];
        $conn->query("UPDATE products SET stock = stock - $qty WHERE name = '$name'");
    }

    // Diagnostic: Try to see the table structure
    $result = $conn->query("SHOW COLUMNS FROM sales");
    $columns = [];
    while ($row = $result->fetch_assoc()) {
        $columns[] = $row['Field'];
    }

    // For now, try a very basic insert
    $sql = "INSERT INTO sales (id) VALUES (NULL)";   // Try with just id or nothing

    if (!$conn->query($sql)) {
        throw new Exception("Insert failed: " . $conn->error . " | Columns in sales table: " . implode(", ", $columns));
    }

    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Sale completed successfully!',
        'total' => $total_amount,
        'columns' => $columns
    ]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>