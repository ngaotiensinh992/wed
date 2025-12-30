<?php
$referer = $_SERVER['HTTP_REFERER'] ?? '';
$expected_domain = 'hoangphuc68.site/games/68gbsic.html';

// Direct access check
if (empty($referer)) {
    header('Content-Type: text/plain; charset=utf-8');
    echo ' ĐỊT MẸ KHÔNG TRÌNH ĐÒI CRACK BỐ HAHAHAH!';
    exit;
}

// Referer validation
if (strpos($referer, $expected_domain) === false) {
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Access denied. Invalid referer.';
    exit;
}

// API call
$api_url = 'https://sicbo68gb-ubkf.onrender.com/api/sicbo';
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
    ], JSON_UNESCAPED_UNICODE);
    curl_close($ch);
    exit;
}

curl_close($ch);

$data = json_decode($response, true);

if (!$data || !isset($data['phien_hien_tai'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid API response'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Format the response exactly as specified
$output = [
    'Phien' => isset($data['phien_hien_tai']) ? $data['phien_hien_tai'] - 1 : 0,
    'phien_hien_tai' => $data['phien_hien_tai'] ?? '',
    'du_doan' => $data['du_doan'] ?? '',
    'dudoan_vi' => $data['du_doan_vi'] ?? '',
    'do_tin_cay' => $data['do_tin_cay'] ?? 0
];

header('Content-Type: application/json; charset=utf-8');
echo json_encode($output, JSON_UNESCAPED_UNICODE);