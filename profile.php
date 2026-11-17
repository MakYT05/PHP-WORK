<?php require 'config/db.php';
requireLogin();

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Получаем историю заказов (если они есть)
$orders = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$orders->execute([$_SESSION['user_id']]);
?>
<!DOCTYPE html><html lang="ru"><head><meta charset="UTF-8"><title>Личный кабинет</title>
<link rel="stylesheet" href="assets/css/style.css"></head><body>
<?php include 'includes/header.php'; ?>
<div class="container">
    <h1>Личный кабинет</h1>
    <div class="profile-grid">
        <div class="profile-card">
            <h2>👤 <?= htmlspecialchars($user['name']) ?></h2>
            <p>Email: <?= htmlspecialchars($user['email']) ?></p>
            <p style="margin-top:10px;">Бонусный баланс: <b><?= $user['bonus_balance'] ?> баллов</b></p>
        </div>
        
        <div class="profile-card">
            <h2>История заказов</h2>
            <?php if ($orders->rowCount() == 0): ?>
                <p>У вас пока нет заказов. <a href="index.php">Сделать первый заказ</a></p>
            <?php else: ?>
                <ul class="order-list">
                    <?php foreach ($orders as $o): ?>
                        <li>Заказ №<?= $o['id'] ?> от <?= date('d.m.Y', strtotime($o['created_at'])) ?> на сумму <?= $o['total'] ?> ₽</li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>
</body></html>