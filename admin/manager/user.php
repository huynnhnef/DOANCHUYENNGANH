

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/menuBrands.css">
    <link rel="stylesheet" href="../css/page.css">
    <title>Phonevibe</title>
</head>
<body>
    
    <?php
        include "../include/header.php";
    ?>
            

    <div class="container">
    <div class="left-sidebar">
        <div class="dropdown">
            <span class="dropdown-title">Quản lý</span>
            <div class="dropdown-content">
                <a href="user.php" style="background-color: #fbff7a;">Người dùng</a>
                <a href="order.php">Đơn hàng</a>
                <a href="product.php">Sản phẩm</a>
                <a href="category.php">Danh mục</a>
                <a href="brand.php">Thương hiệu</a>
                <a href="warranty.php">Đơn bảo hành</a>
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

// Lấy từ khóa tìm kiếm nếu có
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Khởi tạo mảng rỗng để tránh lỗi Undefined variable
$users = [];

try {
    // Truy vấn tổng số người dùng (không phân trang)
    $countSql = "SELECT COUNT(*) FROM users";
    if ($searchTerm != '') {
        // Truy vấn tìm kiếm người dùng
        $countSql = "SELECT COUNT(*) FROM users a
                     JOIN roles r ON a.RoleID = r.ID
                     WHERE a.Username LIKE :searchTerm OR a.Email LIKE :searchTerm OR r.RoleName LIKE :searchTerm";
    }
    $countStmt = $pdo->prepare($countSql);
    if ($searchTerm != '') {
        $countStmt->execute([':searchTerm' => "%$searchTerm%"]);
    } else {
        $countStmt->execute();
    }
    $totalRecords = $countStmt->fetchColumn();

    // Tính tổng số trang
    $totalPages = ceil($totalRecords / $recordsPerPage);

    // Truy vấn lấy người dùng, có phân trang và tìm kiếm nếu cần
    $sql = "
        SELECT a.Username, a.Email, a.RoleID, r.RoleName, a.user_status
        FROM users a
        JOIN roles r ON a.RoleID = r.ID
    ";

    if ($searchTerm != '') {
        $sql .= " WHERE a.Username LIKE :searchTerm OR a.Email LIKE :searchTerm OR r.RoleName LIKE :searchTerm";
    }

    // Thay vì dùng :offset và :limit, trực tiếp thêm giá trị số
    $sql .= " LIMIT $offset, $recordsPerPage"; // Chú ý thay thế trực tiếp offset và recordsPerPage vào câu truy vấn

    $stmt = $pdo->prepare($sql);
    if ($searchTerm != '') {
        $stmt->execute([':searchTerm' => "%$searchTerm%"]);
    } else {
        $stmt->execute();
    }

    // Lấy tất cả dữ liệu người dùng
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        background:  rgb(247, 246, 193);
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

<!-- HTML - Giao diện người dùng -->
<h1>Danh sách người dùng</h1>

        <!-- Thanh tìm kiếm -->
        <div class="search-bar">
            <form method="get" action="user.php">
                <input type="text" name="search" placeholder="Tìm kiếm..." value="<?php echo htmlspecialchars($searchTerm); ?>" />
                <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
        </div>

        <!-- Bảng dữ liệu người dùng -->
        <table>
            <thead>
                <tr>
                    <th>Tên người dùng</th>
                    <th>Email</th>
                    <th>Vai trò</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
            <?php if (count($users) > 0): ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['Username']; ?></td>
                        <td><?php echo $user['Email']; ?></td>
                        <td><?php echo $user['RoleName']; ?></td>
                        <td><?php echo $user['user_status']; ?></td>
                        <td>
                            <a href="function-user/formedituser.php?id=<?php echo $user['Username']; ?>"><i class="fa-solid fa-pen"></i></a> &nbsp;&nbsp;&nbsp;
                            <a href="function-user/delete_user.php?id=<?php echo $user['Username']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng này không?')"><i class="fa-solid fa-trash"></i></a> &nbsp;&nbsp;&nbsp;
                            <a href="userdetails.php?id=<?php echo $user['Username']; ?>">Chi tiết</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">Không có người dùng nào khớp với tìm kiếm.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>

        <!-- Phân trang -->
        <div class="page">
        <div class="page-links">
            <?php if ($page > 1): ?>
                <a href="user.php?page=1<?php echo $searchTerm ? '&search=' . urlencode($searchTerm) : ''; ?>"></a>
                <a href="user.php?page=<?php echo $page - 1; ?><?php echo $searchTerm ? '&search=' . urlencode($searchTerm) : ''; ?>"><<</a>
            <?php endif; ?>

            <span>Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>

            <?php if ($page < $totalPages): ?>
                <a href="user.php?page=<?php echo $page + 1; ?><?php echo $searchTerm ? '&search=' . urlencode($searchTerm) : ''; ?>">>></a>
                <a href="user.php?page=<?php echo $totalPages; ?><?php echo $searchTerm ? '&search=' . urlencode($searchTerm) : ''; ?>"></a>
            <?php endif; ?>
        </div>

        </div>

        <!-- Thêm người dùng -->
        <div class="add_user">
            <button class="add_user_btn"><a href="function-user/add_user.php">+ Thêm</a></button>
        </div>



</body>
</html>

<?php
// Đóng kết nối
$pdo = null;
?>


    </div>
</div>


        <footer>

        </footer>
</div>
    <script src="../js/logoutModal.js"></script>    
</body>
</html>
