<?php require 'config/db.php';
requireLogin();

$cafes = $pdo->query("SELECT * FROM cafes WHERE is_active = 1")->fetchAll();
$user = $pdo->query("SELECT * FROM users WHERE id = " . (int)$_SESSION['user_id'])->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cafe_id = (int)$_POST['cafe_id'];
    $pickup  = $_POST['pickup_time'];
    $bonus_use = min((int)$_POST['bonus_use'], $user['bonus_balance']);

    // Считаем сумму
    $total = 0;
    $ids = array_keys($_SESSION['cart']);
    $rows = $pdo->query("SELECT * FROM dishes WHERE id IN (" . implode(',', array_map('intval',$ids)) . ")")->fetchAll();
    $byId = [];
    foreach ($rows as $r) { $byId[$r['id']] = $r; }
    foreach ($_SESSION['cart'] as $id => $qty) {
        if (isset($byId[$id])) $total += $byId[$id]['price'] * $qty;
    }
    $payable = max(0, $total - $bonus_use);
    $earned  = (int)round($payable * 0.05); // 5% бонусами

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, cafe_id, pickup_time, total, bonus_used, bonus_earned)
                               VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $cafe_id, $pickup, $payable, $bonus_use, $earned]);
        $orderId = $pdo->lastInsertId();

        $ins = $pdo->prepare("INSERT INTO order_items (order_id, dish_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($_SESSION['cart'] as $id => $qty) {
            $ins->execute([$orderId, $id, $qty, $byId[$id]['price']]);
        }

        // Бонусы: списание и начисление
        if ($bonus_use > 0) {
            $pdo->prepare("INSERT INTO bonus_history (user_id, order_id, amount, reason) VALUES (?, ?, ?, 'Списание при оплате')")
                ->execute([$_SESSION['user_id'], $orderId, -$bonus_use]);
        }
        if ($earned > 0) {
            $pdo->prepare("INSERT INTO bonus_history (user_id, order_id, amount, reason) VALUES (?, ?, ?, 'Начисление за заказ')")
                ->execute([$_SESSION['user_id'], $orderId, $earned]);
        }
        $pdo->prepare("UPDATE users SET bonus_balance = bonus_balance - ? + ? WHERE id = ?")
            ->execute([$bonus_use, $earned, $_SESSION['user_id']]);

        // Списание со склада
        foreach ($_SESSION['cart'] as $id => $qty) {
            $ing = $pdo->prepare("SELECT stock_id, amount FROM dish_ingredients WHERE dish_id = ?");
            $ing->execute([$id]);
            foreach ($ing->fetchAll() as $row) {
                $pdo->prepare("UPDATE stock SET quantity = quantity - ? WHERE id = ?")
                    ->execute([$row['amount'] * $qty, $row['stock_id']]);
            }
        }

        $pdo->commit();
        unset($_SESSION['cart']);
        header("Location: order_status.php?id=$orderId");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = 'Ошибка оформления: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html><html><head><meta charset="utf-8"><title>Оформление</title>
<link rel="stylesheet" href="assets/css/style.css"></head><body>
<?php include 'includes/header.php'; ?>
<h1>Оформление заказа</h1>
<?php if (!empty($error)) echo "<p style='color:red'>$error</p>"; ?>
<form method="post">
    <label>Кафе:
        <select name="cafe_id" required>
            <?php foreach ($cafes as $c): ?>
                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?> — <?= htmlspecialchars($c['address']) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>Время самовывоза:
        <input type="datetime-local" name="pickup_time" required>
    </label>
    <label>Списать бонусов (доступно: <?= $user['bonus_balance'] ?>):
        <input type="number" name="bonus_use" min="0" max="<?= $user['bonus_balance'] ?>" value="0">
    </label>
    <button class="btn">Подтвердить заказ</button>
</form>
</body></html>