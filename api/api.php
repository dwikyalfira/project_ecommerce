<?php
// Include library Midtrans
require_once '../vendor/autoload.php';

// Set header untuk mengizinkan CORS (Cross-Origin Resource Sharing)
header("Access-Control-Allow-Origin: *");

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

// Cetak data yang diterima (untuk debugging)
// echo "Data Received:";
// var_dump($data);

// Memeriksa apakah data diterima sesuai dengan yang diharapkan
if (isset($data['user_id']) && isset($data['items']) && isset($data['customer_address']) && isset($data['total_price'])) {
    $user_id = $data['user_id'];
    $items = $data['items'];
    $customer_address = $data['customer_address'];
    $total_price = $data['total_price'];
    // echo "Data Received:";
    // var_dump($data);

    // Membuat nomor order acak
    $order_id = rand();

    // Menyiapkan parameter transaksi
    $item_details = [];
    foreach ($items as $item) {
        if (isset($item['id'])) {
            $item_details[] = [
                'product_id' => $item['product_id'],
                'product_price' => $item['product_price'],
                'quantity' => $item['qty'], // Jumlah barang mungkin bisa diperoleh dari Flutter
                'name' => $item['product_name'],
            ];
        }
    }

    // Menghitung total gross amount
    $gross_amount = $total_price;

    if (count($item_details) > 0) {
        $params = [
            'transaction_details' => [
                'order_id' => $order_id,
                'gross_amount' => $gross_amount,
            ],
            'item_details' => $item_details
        ];

        // Mendapatkan Snap Token dari Midtrans Snap
        $snapToken = \Midtrans\Snap::getSnapToken($params);

        // Menyimpan data transaksi ke dalam tabel 'payments'
        $conn = new mysqli("localhost", "root", "", "project_ecommerce");

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "INSERT INTO payments (order_id, amount, customer_address, status, snap_token, id_user)
        VALUES ('$order_id', '$gross_amount', '$customer_address', 'pending', '$snapToken', '$user_id')";

        if ($conn->query($sql) === TRUE) {
            // Update stok produk
            foreach ($items as $item) {
                $updateSql = "UPDATE tb_product SET qty = qty - 1 WHERE cart_id = " . $item['id'];
                if ($conn->query($updateSql) !== TRUE) {
                    echo "Error updating record: " . $conn->error;
                    break;
                }
            }

            // Kirim respons JSON dengan snap token
            $response = [
                'snap_token' => $snapToken,
                'user_id' => $user_id,
                'order_id' => $order_id
            ];

            echo json_encode($response);
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $conn->close();
    } else {
        echo "Invalid item details provided";
    }
} else {
    echo "Invalid data provided";
}
