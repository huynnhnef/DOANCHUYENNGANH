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
        $brandID = isset($_GET['id']) ? $_GET['id'] : null;

        if ($brandID) {
            // Lấy dữ liệu thương hiệu từ cơ sở dữ liệu
            $stmt = $pdo->prepare("SELECT * FROM brands WHERE id = :id");
            $stmt->execute([':id' => $brandID]);
            $brand = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    ?>

    <div class="container">
        <h2>Sửa Thương Hiệu</h2>
        <form action="edit_brand.php" method="POST">
            <input type="hidden" name="brand_id" value="<?php echo $brand['id']; ?>">
            <div class="form-group">
                <label for="brand_name">Tên thương hiệu</label>
                <input type="text" id="brand_name" name="brand_name" value="<?php echo$brand['BrandName']; ?>" required>
            </div>
            

            <button type="submit" class="btn">Cập nhật</button>

            <p class="btn-return"><a href="../brand.php">Quay lại</a></p>
        </form>
    </div>

    <footer>
        <!-- Nội dung Footer -->
    </footer>
</body>
</html>
