<?php 
require 'config/db.php';

// Добавление в корзину
if (isset($_GET['add'])) {
    $id = (int)$_GET['add'];
    $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
    header('Location: index.php');
    exit;
}

$dishes = $pdo->query("
    SELECT d.*, c.name AS category
    FROM dishes d
    JOIN categories c ON c.id = d.category_id
    ORDER BY c.sort_order, d.name
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Умное кафе - Меню</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include 'includes/header.php'; ?>
<div class="container">
    <h1>Наше меню</h1>
    <div class="menu-grid">
        <?php foreach ($dishes as $d): ?>
            <div class="card">
                <!-- Если картинки нет, покажет серый фон -->
                <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500&q=80" alt="<?= htmlspecialchars($d['name']) ?>">
                <div class="card-content">
                    <h3><?= htmlspecialchars($d['name']) ?></h3>
                    <p><?= htmlspecialchars($d['description']) ?></p>
                    <div class="price"><?= number_format($d['price'], 0, '', ' ') ?> ₽</div>
                    
                    <?php if ($d['is_available']): ?>
                        <a href="index.php?add=<?= $d['id'] ?>" class="btn">В корзину</a>
                    <?php else: ?>
                        <span class="btn disabled">Стоп-лист</span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>