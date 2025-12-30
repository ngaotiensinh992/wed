<?php
// ==========================
// 🔒 CHỐNG TRUY CẬP TRÁI PHÉP
// ==========================
$referer = $_SERVER['HTTP_REFERER'] ?? '';
$expected_domain = 'hoangphuc68.site';
if (empty($referer) || strpos($referer, $expected_domain) === false) {
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Access denied!';
    exit;
}

// ==========================
// ⚙️ CẤU HÌNH
// ==========================
$api_url     = 'https://xanh68-hp.onrender.com/api/du-doan';
$cache_file  = __DIR__ . '/cache_temp.json';
$lock_file   = __DIR__ . '/cache_temp.lock';
$cache_limit = 8;   // giây cache
$timeout     = 15;  // chờ API tối đa 15s

// ==========================
// 📦 ĐỌC CACHE NẾU CÒN HẠN
// ==========================
if (file_exists($cache_file) && (time() - filemtime($cache_file) < $cache_limit)) {
    header('Content-Type: application/json; charset=utf-8');
    echo file_get_contents($cache_file);
    exit;
}

// ==========================
// 🔒 CHỈ 1 TIẾN TRÌNH ĐƯỢC GỌI API
// ==========================
$lock = fopen($lock_file, 'c');
if (!$lock) {
    // fallback: nếu không tạo được lock file, trả cache nếu có
    if (file_exists($cache_file)) {
        header('Content-Type: application/json; charset=utf-8');
        echo file_get_contents($cache_file);
        exit;
    }
}

if (!flock($lock, LOCK_EX | LOCK_NB)) {
    // ❗ Nếu đang có người khác gọi API → chờ họ ghi xong cache
    $wait_start = time();
    while (time() - $wait_start < $timeout) {
        if (file_exists($cache_file) && (time() - filemtime($cache_file) < $cache_limit)) {
            header('Content-Type: application/json; charset=utf-8');
            echo file_get_contents($cache_file);
            fclose($lock);
            exit;
        }
        usleep(200000); // 0.2 giây chờ
    }
    // Nếu chờ lâu quá mà không có cache mới → fallback
    if (file_exists($cache_file)) {
        header('Content-Type: application/json; charset=utf-8');
        echo file_get_contents($cache_file);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Không thể lấy dữ liệu cache hoặc API'], JSON_UNESCAPED_UNICODE);
    }
    fclose($lock);
    exit;
}

// ==========================
// 🌐 GỌI API BACKEND (CHỈ 1 NGƯỜI)
// ==========================
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $api_url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => $timeout,
    CURLOPT_CONNECTTIMEOUT => 8,
    CURLOPT_FAILONERROR => true
]);

$response = curl_exec($ch);
if (curl_errno($ch)) {
    $error_msg = curl_error($ch);
    file_put_contents(__DIR__ . '/error_log_api.txt', date('H:i:s') . " | CURL ERROR: $error_msg\n", FILE_APPEND);

    // fallback trả cache cũ nếu có
    if (file_exists($cache_file)) {
        header('Content-Type: application/json; charset=utf-8');
        echo file_get_contents($cache_file);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Lỗi kết nối API'], JSON_UNESCAPED_UNICODE);
    }

    curl_close($ch);
    flock($lock, LOCK_UN);
    fclose($lock);
    exit;
}
curl_close($ch);

// ==========================
// 🧩 XỬ LÝ DỮ LIỆU
// ==========================
$data = json_decode($response, true);
if (!$data || !isset($data['phien'])) {
    file_put_contents(__DIR__ . '/error_log_api.txt', date('H:i:s') . " | INVALID API RESPONSE\n", FILE_APPEND);
    if (file_exists($cache_file)) {
        header('Content-Type: application/json; charset=utf-8');
        echo file_get_contents($cache_file);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'API không hợp lệ'], JSON_UNESCAPED_UNICODE);
    }
    flock($lock, LOCK_UN);
    fclose($lock);
    exit;
}

$statusRound = empty($data['endTime']) ? 'dang_cho' : 'ket_thuc';

$output = [
    'status'     => 'success',
    'phien'      => $data['phien'],
    'du_doan'    => $data['du_doan'] ?? '',
    'startTime'  => $data['startTime'] ?? null,
    'endTime'    => $data['endTime'] ?? null,
    'createdAt'  => $data['createdAt'] ?? null,
    'trang_thai' => $statusRound
];

// ==========================
// 💾 GHI CACHE & GIẢI PHÓNG LOCK
// ==========================
file_put_contents($cache_file, json_encode($output, JSON_UNESCAPED_UNICODE));

flock($lock, LOCK_UN);
fclose($lock);

// ==========================
// ✅ TRẢ VỀ KẾT QUẢ
// ==========================
header('Content-Type: application/json; charset=utf-8');
echo json_encode($output, JSON_UNESCAPED_UNICODE);
exit;
