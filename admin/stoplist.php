<?php require '../config/db.php';
if (($_SESSION['role'] ?? '') !== 'manager' && ($_SESSION['role'] ?? '') !== 'admin') die('Доступ запрещён');

if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $pdo->prepare("UPDATE dishes SET is_available = 1 - is_available WHERE id = ?")->execute([$id]);
    header('Location: stoplist.php'); exit;
}
$dishes = $pdo->query("SELECT * FROM dishes ORDER BY name")->fetchAll();
?>
<h1>Стоп-лист</h1>
<table class="cart">
    <tr><th>Блюдо</th><th>Статус</th><th></th></tr>
    <?php foreach ($dishes as $d): ?>
    <tr>
        <td><?= htmlspecialchars($d['name']) ?></td>
        <td><?= $d['is_available'] ? 'В продаже' : 'Стоп-лист' ?></td>
        <td><a href="stoplist.php?toggle=<?= $d['id'] ?>">Переключить</a></td>
    </tr>
    <?php endforeach; ?>
</table>