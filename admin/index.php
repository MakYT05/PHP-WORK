<?php require '../config/db.php';
if (($_SESSION['role'] ?? '') !== 'manager' && ($_SESSION['role'] ?? '') !== 'admin') {
    die('Доступ запрещён');
}

$sales = $pdo->query("SELECT DATE(created_at) AS d, SUM(total) AS s, COUNT(*) AS cnt
                      FROM orders GROUP BY DATE(created_at) ORDER BY d DESC LIMIT 30")->fetchAll();
$bonus = $pdo->query("SELECT SUM(CASE WHEN amount>0 THEN amount ELSE 0 END) AS earned,
                             SUM(CASE WHEN amount<0 THEN -amount ELSE 0 END) AS spent
                      FROM bonus_history")->fetch();
?>
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Админка</title>
<link rel="stylesheet" href="../assets/css/style.css"></head><body>
<h1>Отчёты</h1>
<h2>Продажи по дням</h2>
<table class="cart">
    <tr><th>Дата</th><th>Заказов</th><th>Выручка</th></tr>
    <?php foreach ($sales as $s): ?>
    <tr><td><?= $s['d'] ?></td><td><?= $s['cnt'] ?></td><td><?= $s['s'] ?> ₽</td></tr>
    <?php endforeach; ?>
</table>
<h2>Бонусная программа</h2>
<p>Начислено: <?= $bonus['earned'] ?> | Списано: <?= $bonus['spent'] ?></p>
</body></html>