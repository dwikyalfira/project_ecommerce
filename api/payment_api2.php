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

// Menerima data dari permintaan POST
$data = json_decode(file_get_contents('php://input'), true);

if (
    isset($data['user_id']) &&
    isset($data['items']) &&
    isset($data['customer_address']) &&
    isset($data['total_price'])
) {
    // Ekstraksi data dari JSON
    $user_id = $data['user_id'];
    $items = $data['items'];
    $customer_address = $data['customer_address'];
    $total_price = $data['total_price'];

    // Membuat nomor order acak
    $order_id = rand();

    // Menyiapkan parameter transaksi
    $item_details = [];
    foreach ($items as $item) {
        if (isset($item['id'], $item['product_name'], $item['product_price'], $item['qty'])) {
            $item_details[] = [
                'id' => $item['id'],
                'price' => $item['product_price'],
                'quantity' => $item['qty'],
                'name' => $item['product_name'],
            ];
        } else {
            echo json_encode(['message' => 'Invalid item details provided']);
            exit;
        }
    }

    // Menghitung total gross amount
    $gross_amount = $total_price;

    // Menyiapkan parameter transaksi
    $params = [
        'transaction_details' => [
            'order_id' => $order_id,
            'gross_amount' => $gross_amount,
        ],
        'item_details' => $item_details
    ];

    // Mendapatkan Snap Token dari Midtrans Snap
    $snapToken = \Midtrans\Snap::getSnapToken($params);

    // Simpan data transaksi ke dalam tabel 'payments'
    $conn = new mysqli("localhost", "root", "", "project_ecommerce");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO payments (order_id, amount, customer_address, status, snap_token, id_user)
    VALUES ('$order_id', '$gross_amount', '$customer_address', 'pending', '$snapToken', '$user_id')";

    if ($conn->query($sql) === TRUE) {
        // Kirim respons JSON dengan snap token
        $response = [
            'snap_token' => $snapToken
        ];

        echo json_encode($response);
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
} else {
    echo json_encode(['message' => 'Invalid data provided']);
}
