<?php require '../config/db.php';
if (($_SESSION['role'] ?? '') !== 'manager' && ($_SESSION['role'] ?? '') !== 'admin') die('Доступ запрещён');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $qty = (float)$_POST['quantity'];
    $pdo->prepare("UPDATE stock SET quantity = quantity + ? WHERE id = ?")->execute([$qty, $id]);
    header('Location: stock.php'); exit;
}
$stock = $pdo->query("SELECT * FROM stock ORDER BY name")->fetchAll();
?>
<h1>Склад</h1>
<table class="cart">
    <tr><th>Ингредиент</th><th>Остаток</th><th>Мин.</th><th>Приход</th></tr>
    <?php foreach ($stock as $s): ?>
    <tr class="<?= $s['quantity'] < $s['min_quantity'] ? 'low' : '' ?>">
        <td><?= htmlspecialchars($s['name']) ?></td>
        <td><?= $s['quantity'] ?> <?= $s['unit'] ?></td>
        <td><?= $s['min_quantity'] ?></td>
        <td>
            <form method="post" style="display:flex;gap:4px">
                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                <input type="number" step="0.1" name="quantity" placeholder="+">
                <button>OK</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>