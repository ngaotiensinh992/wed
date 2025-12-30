<?php
$referer = $_SERVER['HTTP_REFERER'] ?? '';
$expected_domain = 'hoangphuc68.site';

// Nếu truy cập trực tiếp (không có referer)
if (empty($referer)) {
    header('Content-Type: text/plain; charset=utf-8');
    echo 'API CỦA BỐ ĐÒI CRACK CHA HẢ CÓ TRÌNH ĐÂUU HÍ HÍ';
    exit;
}

// Nếu referer không hợp lệ
if (strpos($referer, $expected_domain) === false) {
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Access denied. Invalid referer.';
    exit;
}

// Gọi API gốc
$api_url = "https://sundudoanvip.onrender.com/api/sunwin";

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $api_url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 5,
    CURLOPT_FAILONERROR => true
]);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    curl_close($ch);
    header('Content-Type: application/json');
    echo json_encode([
        'phien_hien_tai' => "...",
        'du_doan' => "...",
        'do_tin_cay' => "...",
        'ket_qua' => null
    ]);
    exit;
}

curl_close($ch);

// Giải mã JSON từ API
$data = json_decode($response, true);

// Trả về đúng định dạng
header('Content-Type: application/json');
echo json_encode([
    'phien_hien_tai' => $data['phien_hien_tai'] ?? "...",
    'du_doan' => $data['du_doan'] ?? "...",
    'do_tin_cay' => $data['do_tin_cay'] ?? "...",
    'ket_qua' => $data['ket_qua'] ?? null
]);