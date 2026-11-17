<?php require 'config/db.php';
$phone = $_SESSION['pending_phone'] ?? '';
$demo  = $_SESSION['demo_code'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code']);
    $stmt = $pdo->prepare("SELECT * FROM users WHERE phone = ? AND sms_code = ?");
    $stmt->execute([$phone, $code]);
    $user = $stmt->fetch();
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'] ?: $phone;
        $_SESSION['role'] = $user['role'];
        unset($_SESSION['pending_phone'], $_SESSION['demo_code']);
        header('Location: index.php');
        exit;
    }
    $error = 'Неверный код';
}
?>
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Вход</title>
<link rel="stylesheet" href="assets/css/style.css"></head><body>
<h2>Вход по SMS-коду</h2>
<?php if (!empty($error)) echo "<p style='color:red'>$error</p>"; ?>
<?php if ($demo) echo "<p>Демо-код: <b>$demo</b></p>"; ?>
<form method="post">
    <input name="code" placeholder="Код из SMS" required>
    <button>Войти</button>
</form>
</body></html>