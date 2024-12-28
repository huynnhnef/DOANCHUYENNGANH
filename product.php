<?php
session_start(); 
require "config.php";

$brand_id = isset($_GET['brand_id']) ? (int)$_GET['brand_id'] : 0;
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/slideshow.css">
    <link rel="stylesheet" href="css/product.css">
    <script type="text/javascript" src="https://ahachat.com//customer-chats/customer_chat_vjylJEcSOd675dc45759b5e.js"></script>
    <link rel="icon" href="img/android-icon-36x36.png" type="image/png">
    <title>Phonevibe</title>
</head>
<body>
    
    <?php include "include/header.php"; ?>

    <div class="container">
        <div class="category">
            <ul>
                <?php 
                    $stmt = $pdo->prepare("SELECT * FROM categories");
                    $stmt->execute();
                    $category = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($category as $cate) {
                        ?>
                            <a href="product.php?category_id=<?= $cate['id']; ?>" class="<?= ($cate['id'] == $category_id ? 'active' : ''); ?>">
                                <?= htmlspecialchars($cate['CategoryName']); ?>
                            </a>
                        <?php
                    }
                ?>
            </ul>
        </div>

        <?php if ($category_id > 0): ?>
        <div class="brands">
            <ul class="brand-list">
                <?php
                    // Lấy tất cả các thương hiệu từ bảng brands
                    $stmt = $pdo->prepare("SELECT * FROM brands");
                    $stmt->execute();
                    $brands = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Hiển thị các thương hiệu
                    foreach ($brands as $brand) {
                        ?>
                            <li>
                                <a href="product.php?category_id=<?= $category_id ?>&brand_id=<?= $brand['id']; ?>" class="<?= ($brand['id'] == $brand_id ? 'active' : ''); ?>">
                                    <?= htmlspecialchars($brand['BrandName']); ?>
                                </a>
                            </li>
                        <?php
                    }
                ?>
            </ul>
        </div>
        <?php endif; ?>

        <div class="products">
                <h3 style="font-size: 2rem; margin-left: 20px;">
                    <?php
                        // Hiển thị tên thương hiệu hoặc tên danh mục nếu có
                        if ($brand_id > 0) {
                            $stmt = $pdo->prepare("SELECT BrandName FROM brands WHERE id = :brand_id");
                            $stmt->execute(['brand_id' => $brand_id]);
                            $brand = $stmt->fetch(PDO::FETCH_ASSOC);
                            echo htmlspecialchars($brand['BrandName']);
                        } else if ($category_id > 0) {
                            $stmt = $pdo->prepare("SELECT CategoryName FROM categories WHERE id = :category_id");
                            $stmt->execute(['category_id' => $category_id]);
                            $category = $stmt->fetch(PDO::FETCH_ASSOC);
                            echo htmlspecialchars($category['CategoryName']);
                        } else {
                            echo "Sản phẩm";
                        }
                    ?>
                </h3>

                <div class="product-list">
                    <?php
                    if (!empty($searchQuery)) {
                        // Truy vấn sản phẩm theo từ khóa
                        $stmt = $pdo->prepare("SELECT p.id, p.ProductName, p.Description, p.Price, p.Image, b.BrandName, c.CategoryName
                                                FROM products p 
                                                JOIN brands b ON p.BrandID = b.id
                                                JOIN categories c ON p.CategoryID = c.id
                                                WHERE p.ProductName LIKE :searchQuery
                                                ORDER BY RAND() ASC");
                        $stmt->execute(['searchQuery' => '%' . $searchQuery . '%']);
                    } else {
                        // Nếu không có từ khóa tìm kiếm, hiển thị tất cả sản phẩm 
                        if ($category_id > 0 && $brand_id > 0) {
                            $stmt = $pdo->prepare("SELECT p.id, p.ProductName, p.Description, p.Price, p.Image, b.BrandName, c.CategoryName
                                                    FROM products p 
                                                    JOIN brands b ON p.BrandID = b.id
                                                    JOIN categories c ON p.CategoryID = c.id
                                                    WHERE p.CategoryID = :category_id AND p.BrandID = :brand_id
                                                    ORDER BY RAND() ASC");
                            $stmt->execute(['category_id' => $category_id, 'brand_id' => $brand_id]);
                        } else if ($category_id > 0) {
                            // Truy vấn theo category_id
                            $stmt = $pdo->prepare("SELECT p.id, p.ProductName, p.Description, p.Price, p.Image, b.BrandName, c.CategoryName
                                                    FROM products p 
                                                    JOIN brands b ON p.BrandID = b.id
                                                    JOIN categories c ON p.CategoryID = c.id
                                                    WHERE p.CategoryID = :category_id
                                                    ORDER BY RAND() ASC");
                            $stmt->execute(['category_id' => $category_id]);
                        } else if ($brand_id > 0) {
                            // Truy vấn theo brand_id
                            $stmt = $pdo->prepare("SELECT p.id, p.ProductName, p.Description, p.Price, p.Image, b.BrandName, c.CategoryName
                                                    FROM products p 
                                                    JOIN brands b ON p.BrandID = b.id
                                                    JOIN categories c ON p.CategoryID = c.id
                                                    WHERE p.BrandID = :brand_id
                                                    ORDER BY RAND() ASC");
                            $stmt->execute(['brand_id' => $brand_id]);
                        } else {
                            // Nếu không có category_id và brand_id, lấy tất cả sản phẩm
                            $stmt = $pdo->prepare("SELECT p.id, p.ProductName, p.Description, p.Price, p.Image, b.BrandName, c.CategoryName
                                                    FROM products p 
                                                    JOIN brands b ON p.BrandID = b.id
                                                    JOIN categories c ON p.CategoryID = c.id");
                            $stmt->execute();
                        }
                    }

                    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Hiển thị các sản phẩm
                    if (count($products) > 0) {
                        foreach ($products as $product) {
                            $imagePath = $product['Image'];
                            $baseDir = 'img/';
                            $fullImagePath = $baseDir . $imagePath;
                            ?>
                                <div class="product-item">
                                    <a href="product-details.php?product_id=<?php echo $product['id']; ?>">
                                        <img src="<?= htmlspecialchars($fullImagePath) ?>" alt="<?php echo htmlspecialchars($product['ProductName']); ?>" />
                                        <h2><?php echo htmlspecialchars($product['ProductName']); ?></h2>
                                        <p><span style="color: red;"><?php echo number_format($product['Price'], 2); ?> VND</span></p>
                                        <button class="btn_mua">Mua ngay</button>
                                    </a>
                                </div>
                            <?php
                        }
                    } else {
                        echo '<p style="text-align: center; font-size: 1.7rem;">Không có sản phẩm nào.</p>';
                    }
                    ?>
                </div>

            </div>

    </div>

    <?php include "include/footer.php"; ?>
    <script src="js/logoutModal.js"></script>                
</body>
</html>
