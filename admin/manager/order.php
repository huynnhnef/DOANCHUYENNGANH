<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/menuBrands.css">
    <link rel="stylesheet" href="../css/page.css">
    <link rel="stylesheet" href="../css/role.css">
    <title>Orders</title>
</head>
<body>
    
    <?php
        include "../include/header.php";

        if (isset($_SESSION['message'])) {
            echo "<script type='text/javascript'>alert('" . $_SESSION['message'] . "');</script>";
            // Xóa thông báo khỏi session sau khi hiển thị
            unset($_SESSION['message']);
        }
        $roleID = isset($_SESSION["RoleID"]) ? $_SESSION["RoleID"] : "";

        // Lấy từ khóa tìm kiếm
        $searchTerm = isset($_GET['search']) ? $_GET['search'] : "";
    ?>

    <div class="container">
    <div class="left-sidebar">
        <div class="dropdown">
            <span class="dropdown-title">Quản lý</span>
            <div class="dropdown-content">
                <a href="user.php" class="<?php echo ($roleID != 5) ? 'disabled' : '' ?>">Người dùng</a>
                <a href="order.php" style="background-color: #fbff7a;">Đơn hàng</a>
                <a href="product.php" class="<?php echo ($roleID == 7) ? 'disabled' : '' ?>">Sản phẩm</a>
                <a href="category.php" class="<?php echo ($roleID == 7) ? 'disabled' : '' ?>">Danh mục</a>
                <a href="brand.php" class="<?php echo ($roleID == 7) ? 'disabled' : '' ?>">Thương hiệu</a>
                <a href="warranty.php" class="<?php echo ($roleID == 7) ? 'disabled' : '' ?>">Đơn bảo hành</a>
            </div>
        </div>
    </div>
    <div class="right-content">

    <?php
    // Số lượng bản ghi mỗi trang
    $recordsPerPage = 5;

    // Kiểm tra và xác định trang hiện tại từ tham số 'page' trong URL
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($page < 1) {
        $page = 1; // Đảm bảo rằng page không nhỏ hơn 1
    }

    // Tính toán vị trí bắt đầu (OFFSET) của dữ liệu trong cơ sở dữ liệu
    $offset = ($page - 1) * $recordsPerPage;

    try {
        // Truy vấn tổng số đơn hàng
        $countSql = "SELECT COUNT(*) FROM orders";
        
        // Nếu có tìm kiếm theo tên khách hàng, sửa lại câu truy vấn
        if ($searchTerm) {
            $countSql = "SELECT COUNT(*) FROM orders o 
                         JOIN users u ON o.CustomerId = u.id
                         WHERE u.FullName LIKE :searchTerm";
        }
        
        $countStmt = $pdo->prepare($countSql);

        // Nếu có tìm kiếm, thêm điều kiện
        if ($searchTerm) {
            $countStmt->execute([':searchTerm' => "%$searchTerm%"]);
        } else {
            $countStmt->execute();
        }

        $totalRecords = $countStmt->fetchColumn();

        // Tính tổng số trang
        $totalPages = ceil($totalRecords / $recordsPerPage);

        // Truy vấn lấy đơn hàng, có phân trang và tìm kiếm nếu có
        $sql = "SELECT o.id as OrderID, o.OrderDate, o.Status, u.Username, u.FullName, o.ShippingAddress
                FROM orders o
                JOIN users u ON o.CustomerId = u.id
                ";

        // Nếu có tìm kiếm theo tên khách hàng
        if ($searchTerm) {
            $sql .= "WHERE u.FullName LIKE :searchTerm ";
        }

        $sql .= "LIMIT $offset, $recordsPerPage";

        $stmt = $pdo->prepare($sql);

        // Thực thi câu truy vấn tìm kiếm nếu có
        if ($searchTerm) {
            $stmt->execute([':searchTerm' => "%$searchTerm%"]);
        } else {
            $stmt->execute();
        }

        // Lấy tất cả dữ liệu đơn hàng
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Lỗi truy vấn: " . $e->getMessage();
    }
    ?>

    <!-- HTML - Giao diện người dùng -->
    <style>
        .right-content h1{
            text-align: center;
            font-size: 35px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 40px;
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

        a:hover {
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
        form select {
            padding: 15px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
            cursor: pointer;
        }
        form select option{
            cursor: pointer;
        }
        form select:focus {
            border-color: #fbff7a;
        }
    </style>

    <h1>Danh sách đơn hàng</h1>

    <!-- Tìm kiếm đơn hàng theo tên khách hàng -->
    <div class="search-bar">
        <form method="get" action="order.php">
            <input type="text" name="search" placeholder="Tìm khách hàng..." value="<?php echo $searchTerm; ?>" />
            <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>
    </div>

    <table>
    <thead>
        <tr>
            <th>Mã đơn hàng</th>
            <th>Khách hàng</th>
            <th>Trạng thái</th>
            <th>Địa chỉ</th>
            <th>Ngày đặt hàng</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
    <?php if (count($orders) > 0): ?>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td><?php echo $order['OrderID']; ?></td>
                <td><?php echo $order['FullName']; ?></td>
                <td>
                    <!-- Form để thay đổi trạng thái -->
                    <form method="POST" action="function-order/update_status.php">
                        <select name="status" onchange="this.form.submit()">
                            <option value="Đang xử lý" <?php echo ($order['Status'] == 'Đang xử lý') ? 'selected' : ''; ?>>Đang xử lý</option>
                            <option value="Đang vận chuyển" <?php echo ($order['Status'] == 'Đang vận chuyển') ? 'selected' : ''; ?>>Đang vận chuyển</option>
                            <option value="Đã hoàn thành" <?php echo ($order['Status'] == 'Đã hoàn thành') ? 'selected' : ''; ?>>Đã hoàn thành</option>
                            <option value="Đã hủy" <?php echo ($order['Status'] == 'Đã hủy') ? 'selected' : ''; ?>>Đã hủy</option>
                        </select>
                        <input type="hidden" name="orderID" value="<?php echo $order['OrderID']; ?>" />
                    </form>
                </td>
                <td><?php echo $order['ShippingAddress']; ?></td>
                <td><?php echo $order['OrderDate']; ?></td>
                <td>
                    <a href="function-order/delete_order.php?OrderID=<?php echo $order['OrderID']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa đơn hàng này không?')"><i class="fa-solid fa-trash"></i></a> &nbsp;&nbsp;&nbsp;
                    <a href="orderdetails.php?orderID=<?php echo $order['OrderID']; ?>">Chi tiết</a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

<!-- Phân trang -->
<div class="page">
    <div class="page-links">
        <?php if ($page > 1): ?>
            <a href="order.php?page=1<?php echo $searchTerm ? '&search=' . urlencode($searchTerm) : ''; ?>">First</a>
            <a href="order.php?page=<?php echo $page - 1; ?><?php echo $searchTerm ? '&search=' . urlencode($searchTerm) : ''; ?>"><<</a>
        <?php endif; ?>

        <span>Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>

        <?php if ($page < $totalPages): ?>
            <a href="order.php?page=<?php echo $page + 1; ?><?php echo $searchTerm ? '&search=' . urlencode($searchTerm) : ''; ?>">>></a>
            <a href="order.php?page=<?php echo $totalPages; ?><?php echo $searchTerm ? '&search=' . urlencode($searchTerm) : ''; ?>">Last</a>
        <?php endif; ?>
    </div>
</div>

    </div>
    </div>

    <footer>
        <!-- Footer content -->
    </footer>

    <script src="../js/logoutModal.js"></script>
</body>
</html>
