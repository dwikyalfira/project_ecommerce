<?php
// Include library Midtrans
require_once '../vendor/autoload.php';

// Set server key Midtrans
\Midtrans\Config::$serverKey = 'SB-Mid-server-kEJiLxnhVza9cP-OSLqOsgPY';
// Set environment ke Development/Sandbox (default). Set ke true untuk Production Environment.
\Midtrans\Config::$isProduction = false;
// Set sanitization ke true (default)
\Midtrans\Config::$isSanitized = true;
// Set 3DS transaction untuk kartu kredit ke true
\Midtrans\Config::$is3ds = true;

// Mendapatkan data JSON dari request
$jsonData = json_decode(file_get_contents('php://input'), true);

// Cek apakah JSON valid
if (empty($jsonData) || !is_array($jsonData)) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['message' => 'Invalid JSON data']);
    exit;
}

// Ekstrak data dari JSON
$userId = isset($jsonData['user_id']) ? $jsonData['user_id'] : null;
$items = isset($jsonData['items']) && is_array($jsonData['items']) ? $jsonData['items'] : [];
$customerAddress = isset($jsonData['customer_address']) ? $jsonData['customer_address'] : null;
$totalPrice = isset($jsonData['total_price']) ? $jsonData['total_price'] : null;

// Validasi data
if (!$userId || empty($items) || !$customerAddress || !$totalPrice) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['message' => 'Missing required data']);
    exit;
}

// Siapkan parameter transaksi
$params = [
    'transaction_details' => [
        'order_id' => rand(), // Buat order ID acak
        'gross_amount' => $totalPrice,
    ],
    'item_details' => [], // Siapkan array untuk detail item
    'customer_details' => [
        'user_id' => $userId, // Simpan user ID
        'address' => $customerAddress,
    ],
];

// Proses detail item
foreach ($items as $item) {
    $params['item_details'][] = [
        'id' => $item['id'],
        'price' => $item['product_price'],
        'quantity' => $item['qty'],
        'name' => $item['product_name'],
    ];
}

// Dapatkan Snap Token dari Midtrans Snap
try {
    $snapToken = \Midtrans\Snap::getSnapToken($params);
} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['message' => $e->getMessage()]);
    exit;
}

// Simpan data transaksi ke database (opsional)
// ... (Implementasi database connection dan query untuk menyimpan data)

// Kirim respons dengan Snap Token
header('HTTP/1.1 200 OK');
echo json_encode(['snap_token' => $snapToken]);
