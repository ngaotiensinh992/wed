<?php
$referer = $_SERVER['HTTP_REFERER'] ?? '';
$expected_domain = 'hoangphuc68.site';

// 1️⃣ Kiểm tra domain hợp lệ
if (empty($referer) || strpos($referer, $expected_domain) === false) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid referer',
        'phien_hien_tai' => "...",
        'du_doan' => "...",
        'do_tin_cay' => "..."
    ]);
    exit;
}

$api_url   = "https://ws68-hp.onrender.com/api/68gb";
$cacheFile = __DIR__ . '/cache_68gb.json';
$lockFile  = __DIR__ . '/cache_68gb.lock';
$cacheTime = 8; // thời gian cache (giây)
$logFile   = __DIR__ . '/apiws68_error.log';

// 2️⃣ Nếu cache còn mới → dùng ngay
if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $cacheTime)) {
    header('Content-Type: application/json; charset=utf-8');
    echo file_get_contents($cacheFile);
    exit;
}

// 3️⃣ Tạo file lock để chỉ 1 người gọi API thật
$lock = fopen($lockFile, 'c');
if (!$lock) {
    // Không tạo được lock → fallback đọc cache
    if (file_exists($cacheFile)) {
        header('Content-Type: application/json; charset=utf-8');
        echo file_get_contents($cacheFile);
        exit;
    }
}

if (!flock($lock, LOCK_EX | LOCK_NB)) {
    // Có người khác đang gọi API → chờ cache được cập nhật
    $waitStart = time();
    while (time() - $waitStart < 10) { // chờ tối đa 10 giây
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $cacheTime)) {
            header('Content-Type: application/json; charset=utf-8');
            echo file_get_contents($cacheFile);
            fclose($lock);
            exit;
        }
        usleep(200000); // chờ 0.2 giây rồi kiểm tra lại
    }

    // Nếu chờ quá lâu mà vẫn chưa có cache mới
    if (file_exists($cacheFile)) {
        header('Content-Type: application/json; charset=utf-8');
        echo file_get_contents($cacheFile);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Cache not available'], JSON_UNESCAPED_UNICODE);
    }
    fclose($lock);
    exit;
}

// 4️⃣ Ghi log ai đang gọi API thật
file_put_contents($logFile, date('c') . " - Caller IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . "\n", FILE_APPEND);

// 5️⃣ Gọi Render API (tối đa 3 lần)
$maxRetry = 3;
$response = false;
for ($i = 0; $i < $maxRetry; $i++) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $api_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_FAILONERROR => false
    ]);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    if ($response !== false && $response !== "") break;
    sleep(1);
}

if ($response === false || $response === "") {
    file_put_contents($logFile, date('c')." - Curl failed: $error\n", FILE_APPEND);
    if (file_exists($cacheFile)) {
        echo file_get_contents($cacheFile);
    } else {
        echo json_encode(['status'=>'error','message'=>'Curl failed']);
    }
    flock($lock, LOCK_UN);
    fclose($lock);
    exit;
}

// 6️⃣ Giải mã JSON
$response = preg_replace('/^\xEF\xBB\xBF/', '', $response);
$data = json_decode($response, true);
if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
    file_put_contents($logFile, date('c')." - Invalid JSON\nRaw: ".substr($response,0,100)."\n", FILE_APPEND);
    if (file_exists($cacheFile)) {
        echo file_get_contents($cacheFile);
    } else {
        echo json_encode(['status'=>'error','message'=>'Invalid JSON']);
    }
    flock($lock, LOCK_UN);
    fclose($lock);
    exit;
}

// 7️⃣ Chuẩn hoá dữ liệu mới
$newData = [
    'status' => 'success',
    'phien_hien_tai' => $data['phien_hien_tai'] ?? $data['phien'] ?? "...",
    'du_doan' => $data['du_doan'] ?? $data['ketqua'] ?? "...",
    'do_tin_cay' => $data['do_tin_cay'] ?? $data['confidence'] ?? "..."
];

// 8️⃣ So sánh với cache cũ → tránh ghi đè khi không có phiên mới
if (file_exists($cacheFile)) {
    $oldData = json_decode(file_get_contents($cacheFile), true);
    if (isset($oldData['phien_hien_tai']) && $oldData['phien_hien_tai'] === $newData['phien_hien_tai']) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($oldData, JSON_UNESCAPED_UNICODE);
        flock($lock, LOCK_UN);
        fclose($lock);
        exit;
    }
}

// 9️⃣ Ghi cache và giải phóng lock
file_put_contents($cacheFile, json_encode($newData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

flock($lock, LOCK_UN);
fclose($lock);

// 🔟 Trả kết quả ra JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode($newData, JSON_UNESCAPED_UNICODE);
exit;
?>
