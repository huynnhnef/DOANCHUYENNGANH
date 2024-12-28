<?php
session_start();
require "config.php";

$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : 0;

// Truy vấn chi tiết sản phẩm
$stmt = $pdo->prepare("SELECT p.id, p.ProductName, p.Description, p.Price, 
                        p.Image AS ProductImage,
                        pc.Image AS ColorImage, 
                        co.ColorName, pc.ColorID
                    FROM products p 
                    LEFT JOIN product_colors pc ON p.id = pc.ProductID
                    LEFT JOIN colors co ON pc.ColorID = co.id
                    WHERE p.id = :product_id");

$stmt->execute(['product_id' => $product_id]);
$product = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$product) {
    echo "Sản phẩm không tồn tại.";
    exit;
}


$imagePath = !empty($product[0]['ProductImage']) ? str_replace("../../../img/", "./img/", $product[0]['ProductImage']) : './img/default.jpg';

// Kiểm tra xem sản phẩm có màu sắc hay không
$colorImages = [];
if (count($product) > 1) {
    // Nếu có nhiều màu, lấy các màu khác
    foreach ($product as $item) {
        if ($item['ColorImage']) {
            $colorImages[] = [
                'Image' => $item['ColorImage'],
                'ColorName' => $item['ColorName'],
                'ColorID' => $item['ColorID']
            ];
        }
    }
} else {
    // Nếu không có màu sắc, không cần xử lý thêm
    $colorImages = [];
}


$description = '<ul>' . preg_replace('/- (.*?)(\n|$)/', '<li style="list-style-type: none; margin: 0; padding-left: 0;">- $1</li>', $product[0]['Description']) . '</ul>';


// Kiểm tra người dùng đã đăng nhập chưa
$user_email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : null;

// Khi người dùng nhấn "Mua ngay", chúng ta lưu thông tin vào session
if (isset($_POST['buy_now'])) {
    $quantity = $_POST['quantity']; 
    $price = $product[0]['Price'];
    $total_amount = $price * $quantity;

    $_SESSION['direct_buy'] = [
        'product_id' => $product[0]['id'],
        'product_name' => $product[0]['ProductName'],
        'quantity' => $quantity,
        'price' => $price,
        'total_amount' => $total_amount
    ];

    header("Location: vnpay_php/vnpay_pay.php?total-price=" . $total_amount);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/product.css">
    <link rel="icon" href="img/android-icon-36x36.png" type="image/png">
    <title>Phonevibe</title>
    <style>
        .color-thumbnails img {
            width: 80px;
            height: 80px;
            margin-right: 10px;
            cursor: pointer;
            border: 2px solid transparent;
        }

        .color-thumbnails img.selected {
            border-color: #f39c12;
        }

        .product-image img {
            width: 400px;
            height: 400px;
            object-fit: contain;
        }
    </style>
</head>
<body>
    
    <?php include "include/header.php"; ?>

    <div class="container">
        <div class="product-details">
            <div class="product-image">
                <img id="mainImage" src="<?php echo $imagePath; ?>" alt="<?php echo $product[0]['ProductName']; ?>">
                <div class="description"><?php echo $description; ?></div>
            </div>

            <div class="product-info">
                <h1><?php echo $product[0]['ProductName']; ?></h1>
                <p><strong>Giá: </strong><span style="color: red;"><?php echo number_format($product[0]['Price'], 2); ?> VND</span></p>

                <?php if (count($colorImages) > 0): ?>
                    <div class="color-thumbnails">
                        <?php foreach ($colorImages as $colorImage): ?>
                            <img src="./img/<?php echo $colorImage['Image']; ?>" 
                                 alt="<?php echo $colorImage['ColorName']; ?>"
                                 onclick="changeProductImage('<?php echo './img/' . $colorImage['Image']; ?>', '<?php echo $colorImage['ColorName']; ?>')"
                                 class="<?php echo ($colorImage['Image'] === $product[0]['ProductImage']) ? 'selected' : ''; ?>"/>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="product-buttons">
                    <?php if ($user_email): ?>
                        <form method="POST" action="">
                            <input type="hidden" name="quantity" value="1"> 
                            <button type="submit" name="buy_now" class="btn_mua">Mua ngay</button>
                        </form>
                    <?php else: ?>
                        <a href="account/login.php"><button class="btn_mua">Mua ngay</button></a>
                    <?php endif; ?>
                    
                    <button class="btn_add_cart" onclick="addToCart(<?php echo $product[0]['id']; ?>)">Thêm vào giỏ hàng</button>
                </div>

            </div>
        </div>
    </div>

    <?php include "include/footer.php"; ?>

    <script>
        function changeProductImage(imageSrc, colorName) {
            document.getElementById('mainImage').src = imageSrc;
            document.getElementById('mainImage').alt = colorName;

            const colorThumbnails = document.querySelectorAll('.color-thumbnails img');
            colorThumbnails.forEach(thumbnail => thumbnail.classList.remove('selected'));

            const selectedThumbnail = Array.from(colorThumbnails).find(thumbnail => thumbnail.src.includes(imageSrc));
            if (selectedThumbnail) selectedThumbnail.classList.add('selected');
        }

        function addToCart(productId) {
            <?php if (!$user_email): ?>
                window.location.href = "account/login.php";
                return;
            <?php endif; ?>

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "cart/add_cart.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    window.location.href="cart.php";
                }
            };
            xhr.send("product_id=" + productId + "&quantity=1");
        }
    </script>

    <script src="js/logoutModal.js"></script>
</body>
</html>
