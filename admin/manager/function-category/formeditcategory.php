<?php
require "../../../config.php";


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Thương Hiệu</title>
    <link rel="stylesheet" href="../../css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../css/role.css">
    <style>
        a{
            text-decoration: none;
        }

        .btn-return{
            margin-top: 15px;
        }
        .btn-return a:hover{
            color: red;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php 
        // Lấy ID thương hiệu từ URL
        $categoryID = isset($_GET['id']) ? $_GET['id'] : null;

        if ($categoryID) {
            // Lấy dữ liệu thương hiệu từ cơ sở dữ liệu
            $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = :id");
            $stmt->execute([':id' => $categoryID]);
            $category = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    ?>

    <div class="container">
        <h2>Sửa danh mục</h2>
        <form action="edit_category.php" method="POST">
            <input type="hidden" name="Category_id" value="<?php echo $category['id']; ?>">
            <div class="form-group">
                <label for="CategoryName">Tên danh mục</label>
                <input type="text" id="CategoryName" name="CategoryName" value="<?php echo $category['CategoryName']; ?>" required>
            </div>
            

            <button type="submit" class="btn">Cập nhật</button>
        </form>

        <p class="btn-return"><a href="../category.php">Quay lại</a></p>
    </div>

    <footer>
        <!-- Nội dung Footer -->
    </footer>
</body>
</html>
