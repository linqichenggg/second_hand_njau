<?php
// 引入认证文件
require_once 'auth.php';

// 引入数据库连接文件
require_once 'db_connect.php';

// 获取用户信息
$user_id = $_SESSION['user_id'];

// 获取物品信息
if (isset($_GET['item_id'])) {
    $item_id = $_GET['item_id'];
    $sql = "SELECT * FROM items WHERE item_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();
    if (!$item) {
        echo "<script>alert('物品不存在'); window.location.href='index.php';</script>";
        exit();
    }

    // 获取卖家的评分
    $seller_id = $item['user_id'];
    $score_sql = "SELECT total_score FROM users WHERE user_id = ?";
    $score_stmt = $conn->prepare($score_sql);
    $score_stmt->bind_param("i", $seller_id);
    $score_stmt->execute();
    $score_result = $score_stmt->get_result();
    $seller_score = $score_result->fetch_assoc()['total_score'] ?? 0; // 如果没有评分，默认为0

} else {
    echo "<script>alert('无效的物品ID'); window.location.href='index.php';</script>";
    exit();
}



// 处理添加到购物车请求
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {
    // 检查是否是物品发布者自己
    if ($item['user_id'] == $user_id) {
        echo "<script>alert('无法将自己的物品添加到购物车'); window.location.href='item_details.php?item_id=$item_id';</script>";
    } else {
        $sql = "INSERT INTO cart_items (user_id, item_id, quantity) VALUES (?, ?, 1)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ii", $user_id, $item_id);
            if ($stmt->execute()) {
                echo "<script>alert('已添加到购物车'); window.location.href='cart.php';</script>";
            } else {
                echo "<script>alert('添加到购物车时出错: " . $stmt->error . "'); window.location.href='item_details.php?item_id=$item_id';</script>";
            }
        } else {
            echo "<script>alert('准备插入数据时出错: " . $conn->error . "'); window.location.href='item_details.php?item_id=$item_id';</script>";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>物品详情 - 小农二手交易系统</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
    <style>
        .seller-score {
            position: absolute;
            top: 30px;
            right: 320px;
            background-color: #000;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            font-size: 16px;
            color:white
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>物品详情</h1>

        <div class="seller-score">
            卖家评分：<?php echo htmlspecialchars($seller_score); ?>
        </div>

        <div class="item-details">
            <img src="uploads/no_image.png" alt="no_image" style="width: 150px; height: auto;">
            <div class="item-info">
                <h2><?php echo htmlspecialchars($item['title']); ?></h2>
                <p>价格：¥<?php echo htmlspecialchars($item['price']); ?></p>
                <p>描述：<?php echo htmlspecialchars($item['description']); ?></p>
                <p>成色：<?php echo htmlspecialchars($item['item_condition']); ?></p>
            </div>
            <?php if ($item['user_id'] != $user_id): ?>
                <form action="item_details.php?item_id=<?php echo $item_id; ?>" method="post">
                    <button type="submit" name="add_to_cart">添加到购物车</button>
                </form>
            <?php endif; ?>
            <button onclick="window.location.href='index.php';" style="margin-top: 10px;">返回主页</button>
        </div>
    </div>
</body>
</html>