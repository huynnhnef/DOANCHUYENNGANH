<?php
// Kết nối cơ sở dữ liệu
require "../../../config.php";

// Thêm người dùng khi form được gửi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $categoryname = $_POST['CategoryName'];


    try {
        $sql = "INSERT INTO categories (CategoryName) values(:CategoryName)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':CategoryName' => $categoryname,
        ]);
        header("Location: ../category.php");
    } catch (PDOException $e) {
        echo "Lỗi khi thêm người dùng: " . $e->getMessage();
    }
}
?>

