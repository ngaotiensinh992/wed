<?php
$referer = $_SERVER['HTTP_REFERER'] ?? '';
$expected_domain = 'hoangphuc68.site';

// Nếu người dùng truy cập trực tiếp (không có referer), thì hiện thông báo
if (empty($referer)) {
    header('Content-Type: text/plain; charset=utf-8');
    echo 'API CỦA BỐ ĐỊT MẸ KHÔNG TRÌNH ĐÒI CRACK BỐ HAHAHAHAH CAYYYYYYY CAYYYYY KHÔNG TRÌNH ĐÒI CRACK CHA HẢ MẤY CON TRAI';
    exit;
}

// Nếu referer không đúng, chặn truy cập
if (strpos($referer, $expected_domain) === false) {
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Access denied. Invalid referer.';
    exit;
}

// Truy cập hợp lệ => trả JSON
header('Content-Type: application/json');

$api_url = "https://wsb52md5.onrender.com/api/sunwin/mrtinhios";
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $api_url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 5,
    CURLOPT_FAILONERROR => true
]);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Could not fetch data from API',
        'error' => curl_error($ch)
    ]);
    curl_close($ch);
    exit;
}

curl_close($ch);
echo $response;