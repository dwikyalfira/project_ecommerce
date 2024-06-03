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

// Data dari request API menggunakan $_POST
$id = isset($_POST['id']) ? $_POST['id'] : null;
$price = isset($_POST['price']) ? $_POST['price'] : null;
$name = isset($_POST['name']) ? $_POST['name'] : null;
$cart_id = isset($_POST['cart_id']) ? $_POST['cart_id'] : null;
$customer_address = isset($_POST['customer_address']) ? $_POST['customer_address'] : null;
$id_user = 14; // Simulasi Auth user

// Membuat nomor order acak
$order_id = rand();

// Menyiapkan parameter transaksi
if ($id && $price && $name) {
    $params = [
        'transaction_details' => [
            'order_id' => $order_id,
            'gross_amount' => $price,
        ],
        'item_details' => [
            [
                'id' => $id,
                'price' => $price,
                'quantity' => 1,
                'name' => $name,
                'cart_id' => $cart_id,
            ]
        ]
    ];

    // Mendapatkan Snap Token dari Midtrans Snap
    $snapToken = \Midtrans\Snap::getSnapToken($params);

    // Simpan data transaksi ke dalam tabel 'payments'
    $conn = new mysqli("localhost", "root", "", "project_ecommerce");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO payments (order_id, amount, customer_address, status, snap_token, cart_product_id, id_user)
    VALUES ('$order_id', '$price', '$customer_address', 'pending', '$snapToken', '$cart_id', '$id_user')";

    if ($conn->query($sql) === TRUE) {
        // Mengurangi stok produk dalam tabel 'tb_product'
        $updateSql = "UPDATE tb_product SET qty = qty - 1 WHERE product_id = '$id'";

        if ($conn->query($updateSql) === TRUE) {
            $response = [
                'snap_token' => $snapToken
            ];

            echo json_encode($response);
        } else {
            echo "Error updating record: " . $conn->error;
        }
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
} else {
    echo json_encode(['message' => 'Invalid data provided']);
}