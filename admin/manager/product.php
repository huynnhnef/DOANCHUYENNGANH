<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/product.css">
    <link rel="stylesheet" href="../css/menuBrands.css">
    <link rel="stylesheet" href="../css/page.css">
    <link rel="stylesheet" href="../css/role.css">
    <title>Phonevibe</title>
</head>
<body>
    
    <?php
        include "../include/header.php";
        $roleID = isset($_SESSION["RoleID"]) ? $_SESSION["RoleID"] : "";
        $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
    ?>
    
    <div class="container">
    <div class="left-sidebar">
        <div class="dropdown">
            <span class="dropdown-title">Quản lý</span>
            <div class="dropdown-content">
                <a href="user.php" class="<?php echo ($roleID != 5) ? 'disabled' : '' ?>">Người dùng</a>
                <a href="order.php" class="<?php echo ($roleID == 7) ? 'disabled' : '' ?>">Đơn hàng</a>
                <a href="product.php" style="background-color: #fbff7a;" >Sản phẩm</a>
                <a href="category.php" class="<?php echo ($roleID==7) ?'disabled' : '' ?>">Danh mục</a>
                <a href="brand.php" class="<?php echo ($roleID == 7) ? 'disabled' : '' ?>">Thương hiệu</a>
                <a href="warranty.php" class="<?php echo ($roleID == 7) ? 'disabled' : '' ?>">Đơn bảo hành</a>
            </div>
        </div>
    </div>
    <div class="right-content">

    <style>
        .right-content h1 {
            text-align: center;
            font-size: 35px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 40px;
        }
        table img {
            width: 80px;
            height: auto;
            margin-left: 15px;
        }
        th, td {
            padding: 20px;
            text-align: left;
            border: 1px solid #ddd;
            font-size: 18px;
        }
        
        th {
            background-color: #34495e;
            color: white;
        }
        a {
            color: #1abc9c;
        }
        td a:hover {
            text-decoration: none;
            color: red;
        }
        .search-bar {
            margin-top: 40px;
        }
        .search-bar input[type="text"] {
            padding: 5px;
            width: 200px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .search-bar button {
            padding: 9px 15px;
            background: rgb(247, 246, 193);
            color: black;
            border-radius: 5px;
            cursor: pointer;
        }
        .search-bar button:hover {
            background-color: #fbff7a;
        }
        .add_user{
            text-align: center;
        }
        .add_user_btn{
            font-size: 15px;
            background:  rgb(247, 246, 193);
        }
        .add_user_btn a{
            color: black;
            text-decoration: none;
        }
        .add_user_btn:hover{
            background-color: #fbff7a;
        }
    </style>
    
    <?php
        // Số sản phẩm mỗi trang là 5
        $recordsPerPage = 5;  
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $recordsPerPage;
        
        // Truy vấn tổng số sản phẩm để tính toán số trang
        $stmt_total = "SELECT COUNT(DISTINCT p.id) as total_products
                    FROM products p
                    JOIN brands b on p.BrandID = b.id
                    JOIN categories c on p.CategoryID = c.id";
        if ($searchTerm != '') {
            $stmt_total .= " WHERE p.ProductName LIKE :SearchTemp 
                            OR p.Description LIKE :SearchTemp
                            OR b.BrandName LIKE :SearchTemp
                            OR c.CategoryName LIKE :SearchTemp";
        }

        $stm_total = $pdo->prepare($stmt_total);
        if ($searchTerm != '') {
            $stm_total->execute([":SearchTemp" => "%$searchTerm%"]);
        } else {
            $stm_total->execute();
        }
        $totalResults = $stm_total->fetch(PDO::FETCH_ASSOC)['total_products'];
        $totalPages = ceil($totalResults / $recordsPerPage);  // Tính số trang
        
        // Truy vấn lấy thông tin sản phẩm với phân trang
        $stmt = "SELECT p.id, p.ProductName, p.Description, p.Price, p.Image, b.BrandName,
                        c.CategoryName
                FROM products p
                JOIN brands b on p.BrandID = b.id
                JOIN categories c on p.CategoryID = c.id";
        
        if ($searchTerm != '') {
            $stmt .= " WHERE p.ProductName LIKE :SearchTemp
                    OR b.BrandName LIKE :SearchTemp 
                    OR c.CategoryName LIKE :SearchTemp";
        }

        $stmt .= " LIMIT $offset, $recordsPerPage";  // Sử dụng offset và recordsPerPage để phân trang
        $stm = $pdo->prepare($stmt);
        if ($searchTerm != '') {
            $stm->execute([":SearchTemp" => "%$searchTerm%"]);
        } else {
            $stm->execute();
        }
        $products = $stm->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <h1>Sản phẩm</h1>
    <div class="search-bar">
        <form method="get" action="product.php">
            <input type="text" name="search" placeholder="Tìm kiếm..." value="<?php echo htmlspecialchars($searchTerm); ?>" />
            <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>
    </div>

    <table>
    <thead style="font-size: 10px;">
        <tr>
            <th>Ảnh</th>
            <th>Tên sản phẩm</th>
            <th>Giá</th>
            <th>Thương hiệu</th>
            <th>Danh mục</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if (count($products) > 0) {
            foreach ($products as $row) {
                ?>
                <tr>
                    <td>
                        <?php
                        // Hiển thị ảnh sản phẩm từ trường Image trong bảng products
                        $imagePath = $row['Image'];
                        $baseDir = '../img/';
                        $fullImagePath = $baseDir . $imagePath;
                        ?>
                        <img src="<?= htmlspecialchars($fullImagePath) ?>" alt="<?= htmlspecialchars($row['ProductName']) ?>">
                    </td>
                    <td><?= htmlspecialchars($row['ProductName']) ?></td>
                    <td><?= htmlspecialchars($row['Price']) ?></td>
                    <td><?= htmlspecialchars($row['BrandName']) ?></td>
                    <td><?= htmlspecialchars($row['CategoryName']) ?></td>
                    <td>
                        <a href="function-product/formeditproduct.php?id=<?= $row['id']; ?>"><i class="fa-solid fa-pen"></i></a>&nbsp;&nbsp;&nbsp;
                        <a href="function-product/delete_product.php?id=<?= $row['id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?')"><i class="fa-solid fa-trash"></i></a>
                    </td>
                </tr>
                <?php
            }
        }
        ?>
    </tbody>
</table>

<!-- Phân trang -->
<div class="page">
    <div class="page-links">
        <?php if ($page > 1): ?>
            <a href="product.php?page=1<?php echo $searchTerm ? '&search=' . urlencode($searchTerm) : ''; ?>">First</a>
            <a href="product.php?page=<?php echo $page - 1; ?><?php echo $searchTerm ? '&search=' . urlencode($searchTerm) : ''; ?>"><<</a>
        <?php endif; ?>

        <span>Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>

        <?php if ($page < $totalPages): ?>
            <a href="product.php?page=<?php echo $page + 1; ?><?php echo $searchTerm ? '&search=' . urlencode($searchTerm) : ''; ?>">>></a>
            <a href="product.php?page=<?php echo $totalPages; ?><?php echo $searchTerm ? '&search=' . urlencode($searchTerm) : ''; ?>">Last</a>
        <?php endif; ?>
    </div>
</div>

        <!-- Thêm sản phẩm -->
        <div class="add_user">
            <button class="add_user_btn"><a href="function-product/formaddproduct.php">+ Add</a></button>
        </div>

    </div>
    </div>

    <script src="../js/logoutModal.js"></script>
</body>
</html>
