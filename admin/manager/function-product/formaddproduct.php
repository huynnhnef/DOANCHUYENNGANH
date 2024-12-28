<?php
// Kết nối cơ sở dữ liệu
require_once '../../../config.php';

// Lấy danh sách categories và brands từ cơ sở dữ liệu
$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
$brands = $pdo->query("SELECT * FROM brands")->fetchAll(PDO::FETCH_ASSOC);
$colors = $pdo->query("SELECT * FROM colors")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Sản Phẩm Mới</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }
        a{
            text-decoration: none;
        }
        .container {
            width: 80%;
            max-width: 1000px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .tab-buttons {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .tab-buttons button {
            width: 100%;
            padding: 13px 30px;
            margin: 0 10px;
            background-color: #fde800;
            color: black;
            font-size: 16px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .tab-buttons button:hover {
            background-color: #fbd700;
        }

        .tab-buttons button.active {
            background-color: #fbd700;
            font-weight: bold;
        }

        .form-container {
            display: none;
            margin-top: 20px;
        }

        .form-container.active {
            display: block;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            font-size: 14px;
            font-weight: bold;
            color: #555;
            margin-bottom: 8px;
        }

        input[type="text"],
        input[type="number"],
        input[type="file"],
        select,
        textarea {
            width: 100%;
            padding: 12px;
            font-size: 14px;
            border-radius: 6px;
            border: 1px solid #ccc;
            outline: none;
            transition: border 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        select:focus,
        textarea:focus {
            border-color: #4CAF50;
        }

        textarea {
            min-height: 150px;
            resize: vertical;
        }

        .form-group select {
            height: 50px;
        }

        .form-group input[type="file"] {
            padding: 5px;
        }

        .form-group button[type="submit"] {
            width: 100%;
            background-color: #fde800;
            color: black;
            padding: 15px;
            font-size: 18px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .form-group button[type="submit"]:hover {
            background-color: #fbd700;
        }

        .form-group .error {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }

        .btn-return{
            text-align: center;
        }

        .btn-return a:hover{
            color: red;
            text-decoration: underline;
        }

        /* Responsive Design */
        @media screen and (max-width: 768px) {
            .container {
                width: 90%;
            }

            .tab-buttons button {
                padding: 8px 20px;
            }

            .form-group input[type="text"],
            .form-group input[type="number"],
            .form-group select,
            .form-group textarea {
                font-size: 12px;
            }

            .form-group button[type="submit"] {
                font-size: 16px;
                padding: 12px;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Thêm Sản Phẩm Mới</h1>

        <div class="tab-buttons">
            <button type="button" id="btnPhone" onclick="showForm('phone')" class="active">Điện thoại</button>
            <button type="button" id="btnTablet" onclick="showForm('tablet')">Máy tính bảng</button>
        </div>

        <div class="form-container" id="phoneForm" class="active">
            <form action="add_product.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="product_name">Tên sản phẩm:</label>
                    <input type="text" id="product_name" name="product_name" required>
                </div>

                <div class="form-group">
                    <label for="product_description">Mô tả sản phẩm:</label>
                    <textarea id="product_description" name="product_description" required></textarea>
                </div>

                <div class="form-group">
                    <label for="product_price">Giá sản phẩm:</label>
                    <input type="number" id="product_price" name="product_price" required step="0.01">
                </div>

                <div class="form-group">
                    <label for="category">Danh mục sản phẩm:</label>
                    <select name="category" id="category" required>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>"><?php echo $category['CategoryName']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="brand">Hãng sản phẩm:</label>
                    <select name="brand" id="brand" required>
                        <?php foreach ($brands as $brand): ?>
                            <option value="<?php echo $brand['id'] ?>"><?php echo $brand['BrandName']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="main_color">Màu chính:</label>
                    <select name="colors[]" id="main_color" required>
                        <?php foreach ($colors as $color): ?>
                            <option value="<?php echo $color['ColorName']; ?>"><?php echo $color['ColorName']; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="main_color_image">Ảnh cho màu chính:</label>
                    <input type="file" name="images[main]" id="main_color_image" accept="image/*" required>
                </div>

                <div class="form-group">
                    <label for="second_color">Màu thứ 2:</label>
                    <select name="colors[]" id="second_color">
                        <?php foreach ($colors as $color): ?>
                            <option value="<?php echo $color['ColorName']; ?>"><?php echo $color['ColorName']; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="second_color_image">Ảnh cho màu thứ 2:</label>
                    <input type="file" name="images[second]" id="second_color_image" accept="image/*">
                </div>

                <div class="form-group">
                    <label for="third_color">Màu thứ 3:</label>
                    <select name="colors[]" id="third_color">
                        <?php foreach ($colors as $color): ?>
                            <option value="<?php echo $color['ColorName']; ?>"><?php echo $color['ColorName']; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="third_color_image">Ảnh cho màu thứ 3:</label>
                    <input type="file" name="images[third]" id="third_color_image" accept="image/*">
                </div>

                <div class="form-group">
                    <label for="fourth_color">Màu thứ 4:</label>
                    <select name="colors[]" id="fourth_color">
                        <?php foreach ($colors as $color): ?>
                            <option value="<?php echo $color['ColorName']; ?>"><?php echo $color['ColorName']; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="fourth_color_image">Ảnh cho màu thứ 4:</label>
                    <input type="file" name="images[fourth]" id="fourth_color_image" accept="image/*">
                </div>

                <div class="form-group">
                    <button type="submit">Thêm sản phẩm</button>
                </div>
                <p class="btn-return"><a href="../product.php">Quay lại</a></p>
            </form>
        </div>

        <div class="form-container" id="tabletForm">
            <form action="add_product.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="product_name">Tên sản phẩm:</label>
                    <input type="text" id="product_name" name="product_name" required>
                </div>

                <div class="form-group">
                    <label for="product_description">Mô tả sản phẩm:</label>
                    <textarea id="product_description" name="product_description" required></textarea>
                </div>

                <div class="form-group">
                    <label for="product_price">Giá sản phẩm:</label>
                    <input type="number" id="product_price" name="product_price" required step="0.01">
                </div>

                <div class="form-group">
                    <label for="category">Danh mục sản phẩm:</label>
                    <select name="category" id="category" required>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>"><?php echo $category['CategoryName']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="brand">Hãng sản phẩm:</label>
                    <select name="brand" id="brand" required>
                        <?php foreach ($brands as $brand): ?>
                            <option value="<?php echo $brand['id']; ?>"><?php echo $brand['BrandName']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="main_color_image">Ảnh cho sản phẩm (Máy tính bảng):</label>
                    <input type="file" name="images[main]" id="main_color_image" accept="image/*" required>
                </div>


                <div class="form-group">
                    <button type="submit">Thêm sản phẩm</button>
                </div>

                <p class="btn-return"><a href="../product.php">Quay lại</a></p>
            </form>
        </div>
    </div>

    <script>
        function showForm(type) {
            document.querySelectorAll('.tab-buttons button').forEach(button => {
                button.classList.remove('active');
            });
            document.querySelectorAll('.form-container').forEach(form => {
                form.classList.remove('active');
            });

            if (type === 'phone') {
                document.getElementById('btnPhone').classList.add('active');
                document.getElementById('phoneForm').classList.add('active');
            } else {
                document.getElementById('btnTablet').classList.add('active');
                document.getElementById('tabletForm').classList.add('active');
            }
        }

        window.onload = function() {
            showForm('phone');
        }
    </script>
</body>
</html>
