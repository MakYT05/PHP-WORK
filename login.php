<?php require 'config/db.php';
if (isLoggedIn()) { header('Location: profile.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['bonus_balance'] = $user['bonus_balance'];
        header('Location: profile.php');
        exit;
    } else {
        $error = "Неверный email или пароль";
    }
}
?>
<!DOCTYPE html><html lang="ru"><head><meta charset="UTF-8"><title>Вход</title>
<link rel="stylesheet" href="assets/css/style.css"></head><body>
<?php include 'includes/header.php'; ?>
<div class="container auth-container">
    <div class="auth-card">
        <h1>Вход</h1>
        <?php if (isset($_GET['registered'])) echo "<p style='color:#2e7d32; margin-bottom:15px;'>Аккаунт создан! Войдите.</p>"; ?>
        <?php if (!empty($error)) echo "<p style='color:#e53935; margin-bottom:15px;'>$error</p>"; ?>
        <form method="post">
            <label>Email</label>
            <input type="email" name="email" required>
            
            <label>Пароль</label>
            <input type="password" name="password" required>
            
            <button type="submit" class="btn">Войти</button>
        </form>
        <p class="auth-link">Нет аккаунта? <a href="register.php">Зарегистрироваться</a></p>
    </div>
</div>
</body></html>