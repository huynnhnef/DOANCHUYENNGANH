<?php
require_once '../../../config.php';

// Kiểm tra nếu có ID sản phẩm cần sửa
if (isset($_GET['id'])) {
    $productID = $_GET['id'];

    // Lấy thông tin sản phẩm hiện tại
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :productID");
    $stmt->execute([":productID" => $productID]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo "Sản phẩm không tồn tại.";
        exit;
    }

    // Lấy thông tin màu sắc của sản phẩm
    $colorStmt = $pdo->prepare("SELECT colors.ColorName FROM product_colors 
                                JOIN colors ON product_colors.ColorID = colors.id 
                                WHERE product_colors.ProductID = :productID");
    $colorStmt->execute([":productID" => $productID]);
    $colors = $colorStmt->fetchAll(PDO::FETCH_ASSOC);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập Nhật</title>
    <link rel="stylesheet" href="../../css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../css/toggle-eye.css">
    <link rel="stylesheet" href="../../css/message.css">
    <style>
        a{
            text-decoration: none;
        }

        .btn-return{
            margin-top: 15px;
            font-size: 1rem;
        }
        .btn-return a:hover{
            color: red;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container" id="loginForm" style="width: 45%; font-size: 1.8rem; font-weight: 700;">
        <h2>Cập nhật</h2>

        <!-- Form sửa sản phẩm -->
            <form action="edit_product.php?id=<?php echo $productID; ?>" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="product_name">Tên sản phẩm:</label>
                    <input type="text" name="product_name" value="<?php echo htmlspecialchars($product['ProductName']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="product_description">Mô tả:</label><br>
                    <textarea name="product_description" required style="width: 100%; height: 200px;"><?php echo htmlspecialchars($product['Description']); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="product_price">Giá:</label>
                    <input type="text" name="product_price" value="<?php echo htmlspecialchars($product['Price']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="category">Danh mục:</label>
                    <select name="category" required>
                        <!-- Lấy danh sách các danh mục -->
                        <?php
                        $categoryStmt = $pdo->query("SELECT * FROM categories");
                        while ($category = $categoryStmt->fetch(PDO::FETCH_ASSOC)) {
                            echo '<option value="' . $category['id'] . '"';
                            if ($category['id'] == $product['CategoryID']) echo ' selected';
                            echo '>' . htmlspecialchars($category['CategoryName']) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="brand">Thương hiệu:</label>
                    <select name="brand" required>
                        <!-- Lấy danh sách các thương hiệu -->
                        <?php
                        $brandStmt = $pdo->query("SELECT * FROM brands");
                        while ($brand = $brandStmt->fetch(PDO::FETCH_ASSOC)) {
                            echo '<option value="' . $brand['id'] . '"';
                            if ($brand['id'] == $product['BrandID']) echo ' selected';
                            echo '>' . htmlspecialchars($brand['BrandName']) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    
                </div>
                <div class="form-group">
                    <label for="colors">Màu sắc:</label>
                    <select name="colors[]" >
                        <?php
                        $allColorsStmt = $pdo->query("SELECT * FROM colors");
                        while ($color = $allColorsStmt->fetch(PDO::FETCH_ASSOC)) {
                            $selected = in_array(['ColorName' => $color['ColorName']], $colors) ? ' selected' : '';
                            echo '<option value="' . $color['ColorName'] . '"' . $selected . '>' . htmlspecialchars($color['ColorName']) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="images">Hình ảnh:</label>
                    <input type="file" name="images[]" multiple>
                </div>
                <button type="submit" class="btn">Cập nhật sản phẩm</button>

                <p class="btn-return"><a href="../product.php">Quay lại</a></p>
            </form>



        <?php
        // Hiển thị thông báo lỗi nếu có
        if (isset($_SESSION['login_error'])) {
            echo "<div class='message'>".$_SESSION['login_error']."</div>";
            unset($_SESSION['login_error']);
        }
        ?>

    </div>

    <script src="../js/toggle-eye.js"></script>
</body>
</html>





