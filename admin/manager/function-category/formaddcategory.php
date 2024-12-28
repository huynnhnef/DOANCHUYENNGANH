<?php
// Kết nối cơ sở dữ liệu
require_once '../../../config.php';

$brands = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm danh mục mới</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background:  rgb(247, 246, 193);
        }
        a{
            text-decoration: none;
        }
        .container {
            width: 80%;
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        form {
            display: grid;
            gap: 20px;
        }

        label {
            font-weight: bold;
            color: #555;
        }

        input[type="text"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="file"] {
            padding: 5px;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        button[type="submit"] {
            background-color: #fde800c3;
            color: black;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: #fbff7a;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group select {
            height: 40px;
        }

        .form-group input[type="file"] {
            padding: 5px;
        }

        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group textarea {
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .form-group input[type="text"]:focus,
        .form-group input[type="number"]:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #4CAF50;
            outline: none;
        }

        .form-group button {
            margin-top: 10px;
        }
        .btn-return{
            text-align: center;
        }
        .btn-return a:hover{
            color: red;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href=""></a>
        <h1>Thêm danh mục mới</h1>
        <form action="add_category.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="CategoryName">Tên danh mục:</label>
                <input type="text" id="CategoryName" name="CategoryName" required>
            </div>


            <div class="form-group">
                <button type="submit">Thêm danh mục</button>
            </div>

            <p class="btn-return"><a href="../category.php">Quay lại</a></p>
        </form>
    </div>

</body>
</html>
