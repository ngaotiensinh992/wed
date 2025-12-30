<?php
session_start();

// Cấu hình tài khoản admin
$admin_user = "adminhoangphuc";
$admin_pass = "091002";

$login_error = '';
$expired_msg = '';

// Nếu session hết hạn thì redirect có ?expired=1
if (isset($_GET['expired']) && $_GET['expired'] == 1) {
    $expired_msg = "⏰ Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại!";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars(trim($_POST['username'] ?? ''));
    $password = htmlspecialchars(trim($_POST['password'] ?? ''));

    if ($username === $admin_user && $password === $admin_pass) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['created'] = time(); // Ghi lại thời điểm đăng nhập
        header("Location: dashboard.php");
        exit;
    } else {
        $login_error = "Tên đăng nhập hoặc mật khẩu không đúng!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>🌟 Admin Hoàng Phúc 🌟</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: url('https://i.postimg.cc/Fzxcz6D0/pexels-arnie-chou-304906-1229042.jpg') no-repeat center center fixed;
  background-size: cover;
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  margin: 0;
}
.login-card {
  background: rgba(20, 20, 30, 0.15); /* gần như trong suốt, chỉ 15% tối */
  backdrop-filter: blur(10px) saturate(150%);
  -webkit-backdrop-filter: blur(10px) saturate(150%);
  border: 1px solid rgba(255, 255, 255, 0.3);
  padding: 40px 30px;
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
  width: 360px;
  text-align: center;
  color: #fff;
  animation: fadeIn 0.8s ease-in-out;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.login-card h2 {
  margin-bottom: 25px;
  font-size: 1.8rem;
  background: linear-gradient(90deg, #fdcb6e, #6c5ce7);
  -webkit-background-clip: text;
  color: transparent;
}
.login-card input {
  width: 100%;
  padding: 12px 15px;
  margin: 10px 0;
  border-radius: 10px;
  border: none;
  outline: none;
  background-color: #2b343d;
  color: #fff;
  font-size: 0.95rem;
  box-shadow: inset 0 1px 2px rgba(0,0,0,0.6);
  transition: all 0.25s ease;
  box-sizing: border-box; /* ✅ giúp tính luôn padding + border vào width, không bị tràn */
}

.login-card input::placeholder {
  color: rgba(255,255,255,0.6);
}
.login-card input:focus {
  border: 1px solid #6c5ce7;
  box-shadow: 0 0 0 3px rgba(108,92,231,0.25);
}
.login-card button {
  width: 100%;
  padding: 12px;
  margin-top: 15px;
  border: none;
  border-radius: 8px;
  background: linear-gradient(90deg, #6c5ce7, #8c7ae6);
  color: #fff;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}
.login-card button:hover {
  background: linear-gradient(90deg, #5a4acb, #7b68ee);
  transform: translateY(-2px);
}
.login-error {
  margin-top: 12px;
  font-size: 0.9rem;
}
@media(max-width: 400px){
  .login-card {
    width: 90%;
    padding: 30px 20px;
  }
}
</style>
</head>
<body>
<div class="login-card">
    <h2><i class="fas fa-user-shield"></i> Admin Hoàng Phúc</h2>
    <form method="POST">
        <input type="text" name="username" placeholder="Tên đăng nhập" required>
        <input type="password" name="password" placeholder="Mật khẩu" required>
        <button type="submit"><i class="fas fa-sign-in-alt"></i> Đăng nhập</button>
    </form>
    <?php if($login_error): ?>
    <div class="login-error"><i class="fas fa-exclamation-triangle"></i> <?= $login_error ?></div>
    <?php endif; ?>

    <?php if($expired_msg): ?>
    <div class="login-error" style="color:#fdcb6e">
        <i class="fas fa-clock"></i> <?= $expired_msg ?>
    </div>
    <?php endif; ?>
</div>
</body>
</html>

