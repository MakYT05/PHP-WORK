<?php require 'config/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = trim($_POST['phone']);
    if (!preg_match('/^\+?7\d{10}$/', $phone)) {
        $error = 'Неверный формат телефона';
    } else {
        $code = rand(1000, 9999);
        $stmt = $pdo->prepare("INSERT INTO users (phone, sms_code) VALUES (?, ?)
            ON DUPLICATE KEY UPDATE sms_code = VALUES(sms_code)");
        $stmt->execute([$phone, $code]);
        $_SESSION['pending_phone'] = $phone;
        $_SESSION['demo_code'] = $code; // только для демо!
        header('Location: login.php');
        exit;
    }
}
?>
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Регистрация</title>
<link rel="stylesheet" href="assets/css/style.css"></head><body>
<h2>Регистрация по номеру телефона</h2>
<?php if (!empty($error)) echo "<p style='color:red'>$error</p>"; ?>
<form method="post">
    <input name="phone" placeholder="+79161234567" required>
    <button>Получить код</button>
</form>
</body></html>