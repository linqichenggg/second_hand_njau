<?php
// 引入认证文件
require_once 'auth.php';

// 引入数据库连接文件
require_once 'db_connect.php';

// 获取用户信息
$username = $_SESSION['username'];
$role = $_SESSION['role'];

// 获取最新发布的物品
$sql = "SELECT * FROM items WHERE status = 'available' AND status = 'available' ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>首页 - 小农二手交易系统</title>
    <link rel="stylesheet" href="style.css">
    <!-- <script src="script.js" defer></script> -->
</head>
<body>
    <header>
        <h1>欢迎来到小农二手交易系统</h1>
    </header>
    <nav>
        <ul>
            <li><a href="#">首页</a></li>
            <li><a href="my_items.php">我的物品</a></li>
            <li><a href="sell_item.php">发布物品</a></li>
            <li><a href="profile.php">个人中心</a></li>
            <li><a href="inbox.php">收件箱</a></li>
            <li><a href="logout.php">退出/切换帐号</a></li>
            <li><a href="cart.php"><img src="cart.png" alt="购物车" class="cart-icon"></a></li>
        </ul>
    </nav>
    <main>
        <div class="welcome">
            <h2>欢迎你，<?php echo htmlspecialchars($username); ?>同学！</h2>
        </div>
        <section class="items-section">
            <h2>最新发布的物品</h2>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="item-card">
                        <img src="uploads/no_image.png" alt="no_image" style="width: 150px; height: auto;">
                        <p>价格：<?php echo htmlspecialchars($row['price']); ?></p>
                        <a href="item_details.php?item_id=<?php echo htmlspecialchars($row['item_id']); ?>" class="btn">查看详情</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>暂时没有卖家上传商品。</p>
            <?php endif; ?>
        </section>
    </main>
    <footer>
        <p>© 2024 小农二手交易系统</p>
    </footer>
    <a href="orders.php" style="position: fixed; right: 20px; bottom: 20px; padding: 10px; background-color: #000; color: red; border-radius: 5px; text-decoration: none;">我的订单</a>
</body>
</html>
<?php
$conn->close();
?>