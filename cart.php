<?php 
require 'config/db.php';

// Удаление из корзины
if (isset($_GET['remove'])) {
    unset($_SESSION['cart'][(int)$_GET['remove']]);
    header('Location: cart.php');
    exit;
}

$items = [];
$total = 0;

if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
    $rows = $pdo->query("SELECT * FROM dishes WHERE id IN ($ids)")->fetchAll();
    
    foreach ($rows as $r) {
        $qty = $_SESSION['cart'][$r['id']];
        $sum = $qty * $r['price'];
        $total += $sum;
        $items[] = ['dish' => $r, 'qty' => $qty, 'sum' => $sum];
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Корзина</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include 'includes/header.php'; ?>
<div class="container">
    <h1>Ваша корзина</h1>
    
    <?php if (!$items): ?>
        <p>Корзина пока пуста. <a href="index.php">Перейти в меню</a></p>
    <?php else: ?>
        <table class="cart">
            <tr>
                <th>Блюдо</th>
                <th>Цена</th>
                <th>Кол-во</th>
                <th>Сумма</th>
                <th></th>
            </tr>
            <?php foreach ($items as $it): ?>
            <tr>
                <td><?= htmlspecialchars($it['dish']['name']) ?></td>
                <td><?= number_format($it['dish']['price'], 0, '', ' ') ?> ₽</td>
                <td><?= $it['qty'] ?> шт.</td>
                <td><?= number_format($it['sum'], 0, '', ' ') ?> ₽</td>
                <td><a href="cart.php?remove=<?= $it['dish']['id'] ?>" class="remove-btn">✕ Удалить</a></td>
            </tr>
            <?php endforeach; ?>
        </table>
        
        <div class="total-box">
            Итого: <span style="color: #d32f2f;"><?= number_format($total, 0, '', ' ') ?> ₽</span>
        </div>
        
        <div style="text-align: right; margin-top: 20px;">
            <a href="checkout.php" class="btn" style="display: inline-block; padding: 15px 30px; font-size: 1.2em;">Перейти к оформлению</a>
        </div>
    <?php endif; ?>
</div>
</body>
</html>