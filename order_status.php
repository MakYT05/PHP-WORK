<?php require 'config/db.php';
requireLogin();
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT o.*, c.name AS cafe_name FROM orders o
                       JOIN cafes c ON c.id = o.cafe_id
                       WHERE o.id = ? AND o.user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$order = $stmt->fetch();
if (!$order) die('Заказ не найден');

$statuses = ['accepted'=>'Принят','cooking'=>'Готовится','ready'=>'Готов','done'=>'Выдан','cancelled'=>'Отменён'];
?>
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Статус заказа</title>
<link rel="stylesheet" href="assets/css/style.css"></head><body>
<?php include 'includes/header.php'; ?>
<h1>Заказ №<?= $order['id'] ?></h1>
<p>Кафе: <?= htmlspecialchars($order['cafe_name']) ?></p>
<p>Время: <?= $order['pickup_time'] ?></p>
<p>Сумма: <?= $order['total'] ?> ₽</p>
<p>Статус: <b><?= $statuses[$order['status']] ?></b></p>
<ul class="status-flow">
    <?php foreach ($statuses as $key => $label): ?>
        <li class="<?= $order['status'] === $key ? 'active' : '' ?>"><?= $label ?></li>
    <?php endforeach; ?>
</ul>
</body></html>