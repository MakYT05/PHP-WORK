<?php require 'config/db.php';
requireLogin();
$user = $pdo->query("SELECT * FROM users WHERE id = " . (int)$_SESSION['user_id'])->fetch();
$history = $pdo->prepare("SELECT * FROM bonus_history WHERE user_id = ? ORDER BY created_at DESC");
$history->execute([$_SESSION['user_id']]);
?>
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Бонусы</title>
<link rel="stylesheet" href="assets/css/style.css"></head><body>
<?php include 'includes/header.php'; ?>
<h1>Бонусный счёт</h1>
<p>Баланс: <b><?= $user['bonus_balance'] ?></b> баллов</p>
<h2>История операций</h2>
<table class="cart">
    <tr><th>Дата</th><th>Сумма</th><th>Причина</th></tr>
    <?php foreach ($history as $h): ?>
    <tr>
        <td><?= $h['created_at'] ?></td>
        <td><?= ($h['amount'] > 0 ? '+' : '') . $h['amount'] ?></td>
        <td><?= htmlspecialchars($h['reason']) ?></td>
    </tr>
    <?php endforeach; ?>
</table>
</body></html>