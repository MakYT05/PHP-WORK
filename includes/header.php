<header class="topbar">
    <a href="index.php" class="logo">🍔 Умное кафе</a>
    <nav>
        <a href="index.php">Меню</a>
        <a href="cart.php" class="cart-link">🛒 Корзина (<?= array_sum($_SESSION['cart'] ?? []) ?>)</a>
        <?php if (isLoggedIn()): ?>
            <a href="#">Бонусы: <?= $_SESSION['bonus_balance'] ?? 0 ?></a>
            <a href="logout.php">Выйти</a>
        <?php else: ?>
            <a href="login.php">Войти</a>
        <?php endif; ?>
    </nav>
</header>