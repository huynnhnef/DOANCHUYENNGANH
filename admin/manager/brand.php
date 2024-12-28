
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
        $brandID=isset($_POST['brand']);
        $roleID = isset($_SESSION["RoleID"]) ? $_SESSION["RoleID"] :"";

        $searchTerm = isset($_GET['search']) ? $_GET['search'] :'';

        if (isset($_GET['error'])) {
            echo "<script>alert('Lỗi không thể xóa thương hiệu');</script>";
        }
    ?>
            

    <div class="container">
    <div class="left-sidebar">
        <div class="dropdown">
            <span class="dropdown-title">Quản lý</span>
            <div class="dropdown-content">
                <a href="user.php" class="<?php echo ($roleID != 5) ? 'disabled' : '' ?>">Người dùng</a>
                <a href="order.php" class="<?php echo ($roleID == 7) ? 'disabled' : '' ?>">Đơn hàng</a>
                <a href="product.php"   class="<?php echo ($roleID==7) ?'disabled' : '' ?>">Sản phẩm</a>
                <a href="category.php" class="<?php echo ($roleID==7) ?'disabled' : '' ?>">Danh mục</a>
                <a href="brand.php" style="background-color: #fbff7a;" class="<?php echo ($roleID == 7) ? 'disabled' : '' ?>">Thương hiệu</a>
                <a href="warranty.php" class="<?php echo ($roleID == 7) ? 'disabled' : '' ?>">Đơn bảo hành</a>
            </div>
        </div>
    </div>
    <div class="right-content">

    
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
    table img{
        width: 80px;
        margin-left: 15px;
        height: 100%;
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
    .btn{
        font-size: 15px;
        background:  rgb(247, 246, 193);
    }
    .add_user_btn a{
        color: black;
        text-decoration: none;
    }
    .btn a{
        color: black;
        text-decoration: none;
    }
    .add_user_btn:hover{
        background-color: #fbff7a;
    }
    .btn:hover{
        background-color: #fbff7a;
    }
</style>
<?php
    $brand=[];
    $stmt="SELECT * from brands ";

    $recordsPerPage = 5;
    $page=isset($_GET['page']) ? (int)($_GET['page']) :1;
    $offset = ($page - 1) * $recordsPerPage;
    

    $countSql = "SELECT COUNT(*) FROM brands";
    
    // Nếu có tìm kiếm, thêm điều kiện tìm kiếm vào câu lệnh SQL
    if ($searchTerm != '') {
        $countSql = 'SELECT COUNT(*) FROM brands where BrandName like :SearchTemp';
    }

    // Thực thi câu lệnh đếm
    $countStmt = $pdo->prepare($countSql);
    if ($searchTerm != '') {
        $countStmt->execute([":SearchTemp" => "%$searchTerm%"]);
    } else {
        $countStmt->execute();
    }
    $totalRecords = $countStmt->fetchColumn(); // Lấy số lượng bản ghi

    // Tính số trang
    $recordsPerPage = 5; // Số sản phẩm trên mỗi trang
    $totalPage = ceil($totalRecords / $recordsPerPage);

    if($searchTerm!=''){
        $stmt .= 'WHERE BrandName like :SearchTemp';
    }
    
    $stmt .= " LIMIT $offset, $recordsPerPage"; 
    $stm = $pdo->prepare($stmt);
    if($searchTerm!= ''){
        $stm->execute([":SearchTemp"=>"%$searchTerm%"]);
    }
    else{
        $stm->execute();
    }
    $brand = $stm->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- HTML - Giao diện người dùng -->
<h1>Thương hiệu</h1>
    <div class="search-bar">
            <form method="get" action="brand.php">
                <input type="text" name="search" placeholder="Tìm kiếm..." value="<?php echo $searchTerm; ?>" />
                <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
        </div>
    <table>
        <thead>
            <tr>
                <th>Mã thương hiệu</th>
                <th>Tên thương hiệu</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
    <?php
    if (count($brand) > 0) {
        foreach ($brand as $row) {
            ?>
            <tr>

                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['BrandName']; ?></td>
                
                <td>
                    <a href="function-brand/formeditbrand.php?id=<?php echo $row['id'];?>"><i class='fa-solid fa-pen'></i></a>&nbsp;&nbsp;&nbsp;
                    <a href="function-brand/delete_brand.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa thương hiệu này không?')"><i class='fa-solid fa-trash'></i></a>
                    <a href=""></a>
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
        <!-- Liên kết trang đầu tiên -->
        <?php if ($page > 1): ?>
            <a href="brand.php?page=1<?php echo $searchTerm ? '&search=' . urlencode($searchTerm) : ''; ?>"></a>
            <a href="brand.php?page=<?php echo $page - 1; ?><?php echo $searchTerm ? '&search=' . urlencode($searchTerm) : ''; ?>"><<</a>
        <?php endif; ?>

        <span>Page <?php echo $page; ?> Of <?php echo $totalPage; ?></span>

        <!-- Liên kết trang tiếp theo -->
        <?php if ($page < $totalPage): ?>
            <a href="brand.php?page=<?php echo $page + 1; ?><?php echo $searchTerm ? '&search=' . urlencode($searchTerm) : ''; ?>">>></a>
            <a href="brand.php?page=<?php echo $totalPage; ?><?php echo $searchTerm ? '&search=' . urlencode($searchTerm) : ''; ?>"></a>
        <?php endif; ?>
    </div>
</div>


        <!-- Thêm người dùng -->
        <div class="add_user">
            <button class="btn"><a href="function-brand/formaddbrand.php">+ Add</a></button>
        </div>


    <footer>

    </footer>
</body>
</html>

        
</div>
    <script src="../js/logoutModal.js"></script>
</body>
</html>
