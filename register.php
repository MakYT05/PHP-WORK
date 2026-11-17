<?php require 'config/db.php';
if (isLoggedIn()) { header('Location: profile.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($name && $email && $password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $hash]);
            header('Location: login.php?registered=1');
            exit;
        } catch (PDOException $e) {
            $error = "Этот email уже зарегистрирован";
        }
    } else {
        $error = "Заполните все поля";
    }
}
?>
<!DOCTYPE html><html lang="ru"><head><meta charset="UTF-8"><title>Регистрация</title>
<link rel="stylesheet" href="assets/css/style.css"></head><body>
<?php include 'includes/header.php'; ?>
<div class="container auth-container">
    <div class="auth-card">
        <h1>Регистрация</h1>
        <?php if (!empty($error)) echo "<p style='color:#e53935; margin-bottom:15px;'>$error</p>"; ?>
        <form method="post">
            <label>Ваше имя</label>
            <input type="text" name="name" required placeholder="Иван">
            
            <label>Email</label>
            <input type="email" name="email" required placeholder="ivan@mail.ru">
            
            <label>Пароль</label>
            <input type="password" name="password" required placeholder="••••••••">
            
            <button type="submit" class="btn">Создать аккаунт</button>
        </form>
        <p class="auth-link">Уже есть аккаунт? <a href="login.php">Войти</a></p>
    </div>
</div>
</body></html>