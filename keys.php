<?php
$referer = $_SERVER['HTTP_REFERER'] ?? '';
if (strpos($referer, 'hoangphuc68.site') === false) {
    echo json_encode(['status' => 'error', 'message' => 'Access denied']);
    exit;
}
date_default_timezone_set('Asia/Ho_Chi_Minh');
// File: keys.php
header('Content-Type: application/json');

$path = 'vipadminhp/keys_data.php';
$keys = file_exists($path) ? json_decode(file_get_contents($path), true) : [];

// Lấy key và device_id từ query string
$key = $_GET['key'] ?? '';
$device_id = $_GET['device_id'] ?? '';
$now = date('c'); // ISO 8601 format

// Nếu key không tồn tại
if (!$key || !isset($keys[$key])) {
    echo json_encode(['status' => 'error', 'message' => 'Key không tồn tại']);
    exit;
}

$data = &$keys[$key];

// Nếu chưa có device_id thì tự tạo từ User Agent + IP
if (!$device_id) {
    $device_id = 'device_' . substr(md5($_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']), 0, 8);
}

// Nếu chưa có danh sách thiết bị thì khởi tạo
if (!isset($data['devices']) || !is_array($data['devices'])) {
    $data['devices'] = [];
}

// Nếu device chưa từng được sử dụng
if (!in_array($device_id, $data['devices'])) {
    if (count($data['devices']) >= $data['limit']) {
        echo json_encode(['status' => 'error', 'message' => 'Key đã đủ số thiết bị']);
        exit;
    }
    // Thêm device mới
    $data['devices'][] = $device_id;

    // Nếu chưa có expired, thì bắt đầu tính hạn từ bây giờ (ví dụ: 30 ngày)
    if (!isset($data['expired'])) {
        $data['expired'] = date('c', strtotime('+30 days'));
    }

    // Ghi lại dữ liệu mới
    file_put_contents($path, json_encode($keys, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

}

// Nếu đã có hạn sử dụng thì kiểm tra xem có hết hạn chưa
if (isset($data['expired']) && strtotime($now) > strtotime($data['expired'])) {
    unset($keys[$key]);
file_put_contents($path, json_encode($keys, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    echo json_encode(['status' => 'error', 'message' => 'Key đã hết hạn và bị xoá']);


    exit;
}

// Trả về kết quả thành công
echo json_encode([
    'status' => 'success',
    'message' => 'Đăng nhập thành công',
    'device_id' => $device_id,
    'devices' => $data['devices'],
    'expired' => $data['expired']
]);