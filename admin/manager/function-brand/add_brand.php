<?php
// Kết nối cơ sở dữ liệu
require "../../../config.php";

// Thêm người dùng khi form được gửi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $brandname = $_POST['brand_name'];


    try {
        $sql = "INSERT INTO brands (BrandName) values(:BrandName)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':BrandName' => $brandname,
        ]);
        header("Location: ../brand.php");
    } catch (PDOException $e) {
        echo "Lỗi khi thêm người dùng: " . $e->getMessage();
    }
}
?>

