<?php
// Cấu hình session
date_default_timezone_set('Asia/Ho_Chi_Minh');
session_set_cookie_params([
    'lifetime' => 3600,           // 3600 giây = 60 phút
    'path' => '/',
    'domain' => '',
    'secure' => !empty($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();

// Nếu chưa login hoặc đã quá hạn 60p → logout
if (empty($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Nếu chưa có thời điểm tạo session thì thêm vào
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
}

// Nếu đã quá 3600s kể từ khi tạo session → logout
if (time() - $_SESSION['created'] > 3600) {
    session_unset();
    session_destroy();
    header("Location: login.php?expired=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>🌟 ADMIN HOÀNG PHÚC 🌟</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    :root {
      --primary: #6c5ce7;
      --secondary: #a29bfe;
      --success: #00b894;
      --info: #0984e3;
      --warning: #fdcb6e;
      --danger: #d63031;
      --dark: #2d3436;
      --light: #f5f6fa;
      --bg-dark: #1e272e;
      --bg-light: #34495e;
      --text: #dfe6e9;
    }
    
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Poppins', sans-serif;
    }
    
    body {
      position: relative; /* 🔥 thêm dòng này để lớp phủ hoạt động đúng */
      background: url('https://i.postimg.cc/HxJFx857/pexels-simon73-1183099.jpg') no-repeat center center fixed;
      background-size: cover;
      color: var(--text);
      min-height: 100vh;
      padding: 20px;
    }
    
    body::before {
      content: "";
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5); /* lớp phủ đen mờ 50% */
      z-index: -1;
    }
    
    .header {
      text-align: center;
      margin: 30px 0 40px;
      position: relative;
    }
    
    .header h1 {
      font-size: 2.5rem;
      background: linear-gradient(90deg, #fdcb6e, #e17055, #6c5ce7);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
      display: inline-block;
      position: relative;
    }
    
    .header h1::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      width: 100px;
      height: 3px;
      background: linear-gradient(90deg, #6c5ce7, #a29bfe);
      border-radius: 3px;
    }
    
    .dashboard {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
      gap: 25px;
      margin-bottom: 40px;
    }
    
    .card {
      background: rgba(52, 73, 94, 0); /* 🌈 màu nền tối trong suốt 55% */
      border-radius: 15px;
      padding: 25px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.35);
      backdrop-filter: blur(8px); /* 💎 hiệu ứng mờ nhẹ giống kính */
      transition: all 0.3s;
    }
    
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
    }
    
    .card h2 {
      font-size: 1.3rem;
      margin-bottom: 20px;
      color: var(--light);
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .card h2 i {
      font-size: 1.2rem;
    }
    
    .form-group {
      margin-bottom: 15px;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 500;
      color: var(--secondary);
    }
    
    input, select, button {
      width: 100%;
      padding: 12px 15px;
      border: none;
      border-radius: 8px;
      font-size: 14px;
      transition: all 0.3s;
    }
    
    input, select {
      background: rgba(255, 255, 255, 0.1);
      color: var(--text);
      border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    input:focus, select:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.2);
    }
    
    .btn {
      font-weight: 600;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }
    
    .btn-create {
      background: var(--success);
      color: white;
    }
    
    .btn-check {
      background: var(--info);
      color: white;
    }
    
    .btn-delete {
      background: var(--danger);
      color: white;
    }
    
    .btn:hover {
      opacity: 0.9;
      transform: translateY(-2px);
    }
    
    .info-box {
      background: rgba(0, 0, 0, 0);
      padding: 15px;
      margin-top: 20px;
      border-radius: 10px;
      border-left: 4px solid var(--info);
      animation: fadeIn 0.5s;
      backdrop-filter: blur(4px);
    }
    
    .info-box p {
      margin-bottom: 8px;
      font-size: 14px;
    }
    
    .info-box p:last-child {
      margin-bottom: 0;
    }
    
    .info-box strong {
      color: var(--secondary);
    }
    
    .keys-table {
      width: 100%;
      background: rgba(44, 62, 80, 0);
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.25);
      margin-bottom: 50px;
      border-collapse: collapse;
      backdrop-filter: blur(6px);
    }
    
    .keys-table thead {
      background: var(--primary);
    }
    
    .keys-table th {
      padding: 15px;
      text-align: left;
      font-weight: 600;
      color: white;
    }
    
    .keys-table td {
      padding: 12px 15px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    .keys-table tr:last-child td {
      border-bottom: none;
    }
    
    .keys-table tr:hover {
      background: rgba(255, 255, 255, 0.03);
    }
    
    .status {
      font-weight: 600;
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 12px;
      display: inline-block;
    }
    
    .status-active {
      background: rgba(0, 184, 148, 0.2);
      color: var(--success);
    }
    
    .status-expired {
      background: rgba(214, 48, 49, 0.2);
      color: var(--danger);
    }
    
    .btn-action {
      padding: 8px 12px;
      border-radius: 6px;
      font-size: 12px;
      font-weight: 600;
      border: none;
      cursor: pointer;
      transition: all 0.3s;
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }
    
    .btn-action:hover {
      transform: translateY(-2px);
    }
    
    .btn-delete-row {
      background: var(--danger);
      color: white;
    }
    
    .stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }
    
    .stat-card {
      background: rgba(52, 73, 94, 0);
      border-radius: 10px;
      padding: 20px;
      text-align: center;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.25);
      backdrop-filter: blur(6px);
    }
    
    .stat-card h3 {
      font-size: 14px;
      color: var(--secondary);
      margin-bottom: 10px;
    }
    
    .stat-card p {
      font-size: 24px;
      font-weight: 700;
      color: var(--light);
    }
    
    .device-list {
      max-width: 250px;
      max-height: 100px;
      overflow-y: auto;
      background: rgba(0, 0, 0, 0.1);
      padding: 8px;
      border-radius: 5px;
      margin-top: 5px;
    }
    
    .device-item {
      display: flex;
      justify-content: space-between;
      font-size: 12px;
      padding: 3px 0;
      border-bottom: 1px dashed rgba(255, 255, 255, 0.1);
    }
    
    .device-item:last-child {
      border-bottom: none;
    }
    
    .time-left {
      font-size: 12px;
      color: var(--warning);
      margin-top: 3px;
    }
    
    .expiry-badge {
      display: inline-block;
      padding: 2px 6px;
      border-radius: 10px;
      font-size: 11px;
      background: rgba(253, 203, 110, 0.2);
      color: var(--warning);
      margin-top: 3px;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    @media (max-width: 768px) {
      .dashboard {
        grid-template-columns: 1fr;
      }
      
      .header h1 {
        font-size: 2rem;
      }
      
      .keys-table {
        display: block;
        overflow-x: auto;
      }
    }
  </style>
</head>
<body>
<?php
// Initialize data array
$data = [];

// Path to data file
$path = 'keys_data.php';

// Load existing data if file exists
if (file_exists($path)) {
    $file_content = file_get_contents($path);
    $data = json_decode($file_content, true) ?: [];
}

$info = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $key = trim($_POST['key'] ?? '');

    if ($action === 'add' && $key && $_POST['expire'] && $_POST['max_devices']) {
        $data[$key] = [
            'expired' => $_POST['expire'],
            'limit' => (int)$_POST['max_devices'],
            'devices' => []
        ];
    }
    if ($action === 'delete' && isset($data[$key])) {
        unset($data[$key]);
    }
    if ($action === 'check' && isset($data[$key])) {
        $info = $data[$key];
    }

    // Save data back to file
    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Sort data by expiration date
if (is_array($data)) {
    uasort($data, function($a, $b) {
        return strtotime($a['expired']) <=> strtotime($b['expired']);
    });
}

// Calculate stats
$totalKeys = is_array($data) ? count($data) : 0;
$activeKeys = is_array($data) ? count(array_filter($data, function($item) {
    return strtotime($item['expired']) > time();
})) : 0;
$expiredKeys = $totalKeys - $activeKeys;
$usedDevices = is_array($data) ? array_sum(array_map(function($item) {
    return count($item['devices']);
}, $data)) : 0;

// Function to calculate time remaining
function timeRemaining($expiry) {
    $now = time();
    $expiry_time = strtotime($expiry);
    $diff = $expiry_time - $now;
    
    if ($diff <= 0) return 'Đã hết hạn';
    
    $days = floor($diff / (60 * 60 * 24));
    $hours = floor(($diff % (60 * 60 * 24)) / (60 * 60));
    $minutes = floor(($diff % (60 * 60)) / 60);
    
    $result = [];
    if ($days > 0) $result[] = $days . ' Ngày';
    if ($hours > 0) $result[] = $hours . ' Giờ';
    if ($minutes > 0) $result[] = $minutes . ' Phút';
    
    return implode(' ', $result) . ' Còn Lại';
}
?>
  <div class="header">
    <h1><i class="fas fa-key"></i> QUẢN LÝ KEY HOÀNG PHÚC</h1>
    <a href="logout.php" style="color: var(--light); float: right; margin-top: -40px;">Đăng xuất</a>
    <p>Trang Quản Trị Hệ Thống KEY VIP</p>
</div>

  
  <div class="stats">
    <div class="stat-card">
      <h3>Tổng Số Key</h3>
      <p><?= $totalKeys ?></p>
    </div>
    <div class="stat-card">
      <h3>Key Hoạt Động</h3>
      <p><?= $activeKeys ?></p>
    </div>
    <div class="stat-card">
      <h3>Key Hết Hạn</h3>
      <p><?= $expiredKeys ?></p>
    </div>
    <div class="stat-card">
      <h3>Thiết Bị Đã Kích Hoạt</h3>
      <p><?= $usedDevices ?></p>
    </div>
  </div>

  <div class="dashboard">
    <form class="card" method="POST">
      <h2><i class="fas fa-plus-circle"></i> Tạo Key Mới</h2>
      <input type="hidden" name="action" value="add">
      
      <div class="form-group">
        <label for="key">Key:</label>
        <input type="text" id="key" name="key" placeholder="Nhập Key..." required>
      </div>
      
      <div class="form-group">
        <label for="expire">Ngày Hết Hạn:</label>
        <input type="datetime-local" id="expire" name="expire" required>
      </div>
      
      <div class="form-group">
        <label for="max_devices">Giới Hạn Thiết Bị:</label>
        <select id="max_devices" name="max_devices">
          <option value="1">1 Thiết Bị</option>
          <option value="2">2 Thiết Bị</option>
          <option value="3">3 Thiết Bị</option>
          <option value="5">5 Thiết Bị</option>
          <option value="10">10 Thiết Bị</option>
        </select>
      </div>
      
      <button class="btn btn-create" type="submit">
        <i class="fas fa-key"></i> Tạo Key
      </button>
    </form>

    <form class="card" method="POST">
      <h2><i class="fas fa-search"></i> Kiểm Tra Key</h2>
      <input type="hidden" name="action" value="check">
      
      <div class="form-group">
        <label for="check_key">Nhập Key Cần Kiểm Tra:</label>
        <input type="text" id="check_key" name="key" placeholder="Nhập Key..." required>
      </div>
      
      <button class="btn btn-check" type="submit">
        <i class="fas fa-check-circle"></i> Kiểm Tra
      </button>

      <?php if (isset($_POST['action']) && $_POST['action'] === 'check' && !empty($info)): ?>
      <div class="info-box">
        <p><strong><i class="fas fa-key"></i> Key:</strong> <?= htmlspecialchars($_POST['key']) ?></p>
        <p><strong><i class="fas fa-clock"></i> Hết Hạn:</strong> <?= date('d/m/Y H:i', strtotime($info['expired'])) ?></p>
        <p><strong><i class="fas fa-hourglass-half"></i> Thời Gian Còn Lại:</strong> <?= timeRemaining($info['expired']) ?></p>
        <p><strong><i class="fas fa-mobile-alt"></i> Thiết Bị Tối Đa:</strong> <?= $info['limit'] ?></p>
        <p><strong><i class="fas fa-check-circle"></i> Đã Dùng:</strong> <?= count($info['devices']) ?></p>
        <?php if (!empty($info['devices'])): ?>
        <div style="margin-top: 10px;">
          <strong><i class="fas fa-list"></i> Danh Sách Thiết Bị:</strong>
          <div class="device-list">
            <?php foreach ($info['devices'] as $device): ?>
              <div class="device-item">
                <span><?= htmlspecialchars($device) ?></span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>
      </div>
      <?php elseif (isset($_POST['action']) && $_POST['action'] === 'check'): ?>
      <div class="info-box" style="border-color: var(--danger);">
        <p><i class="fas fa-exclamation-circle"></i> Key Không Tồn Tại Trong Hệ Thống!</p>
      </div>
      <?php endif; ?>
    </form>

    <form class="card" method="POST">
      <h2><i class="fas fa-trash-alt"></i> Xóa Key</h2>
      <input type="hidden" name="action" value="delete">
      
      <div class="form-group">
        <label for="delete_key">Nhập Key Cần Xóa:</label>
        <input type="text" id="delete_key" name="key" placeholder="Nhập Key..." required>
      </div>
      
      <button class="btn btn-delete" type="submit">
        <i class="fas fa-trash"></i> Xóa Key
      </button>
    </form>
  </div>

  <table class="keys-table">
    <thead>
      <tr>
        <th>Key</th>
        <th>Hạn Dùng</th>
        <th>Thời Gian Còn Lại</th>
        <th>Thiết Bị</th>
        <th>Trạng Thái</th>
        <th>Hành Động</th>
      </tr>
    </thead>
    <tbody>
      <?php if (is_array($data)): ?>
        <?php foreach ($data as $k => $i): 
          $isActive = strtotime($i['expired']) > time();
        ?>
        <tr>
          <td><code><?= htmlspecialchars($k) ?></code></td>
          <td>
            <?= date('d/m/Y H:i', strtotime($i['expired'])) ?>
            <div class="expiry-badge"><?= date('d/m/Y', strtotime($i['expired'])) ?></div>
          </td>
          <td>
            <div class="time-left"><?= timeRemaining($i['expired']) ?></div>
          </td>
          <td>
            <?= count($i['devices']) ?>/<?= $i['limit'] ?>
            <?php if (!empty($i['devices'])): ?>
              <div class="device-list">
                <?php foreach ($i['devices'] as $device): ?>
                  <div class="device-item">
                    <span><?= htmlspecialchars($device) ?></span>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </td>
          <td>
            <span class="status <?= $isActive ? 'status-active' : 'status-expired' ?>">
              <?= $isActive ? 'ACTIVE' : 'EXPIRED' ?>
            </span>
          </td>
          <td>
            <form method="POST" style="display: inline;">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="key" value="<?= htmlspecialchars($k) ?>">
              <button class="btn-action btn-delete-row" type="submit">
                <i class="fas fa-trash"></i> Xoá
              </button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="6" style="text-align: center;">Không Có Dữ Liệu Key Nào</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</body>
</html>