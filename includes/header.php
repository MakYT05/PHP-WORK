<header class="topbar">
    <a href="index.php" class="logo">🍔 Умное кафе</a>
    <nav>
        <a href="index.php">Меню</a>
        <a href="cart.php" class="cart-link">🛒 Корзина (<?= array_sum($_SESSION['cart'] ?? []) ?>)</a>
        <?php if (isLoggedIn()): ?>
            <a href="profile.php">Профиль</a>
            <a href="logout.php" style="color: #e53935;">Выйти</a>
        <?php else: ?>
            <a href="login.php">Войти</a>
            <a href="register.php" style="background:#2e7d32; color:#fff; padding:8px 16px; border-radius:20px;">Регистрация</a>
        <?php endif; ?>
    </nav>
</header>