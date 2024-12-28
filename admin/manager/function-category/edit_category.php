<?php
require "../../../config.php";

// Kiểm tra xem form có được gửi đi không
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $categoryID = $_POST['Category_id'];
    $categoryName = $_POST['CategoryName'];

    try {
        // Cập nhật thông tin thương hiệu
        $sql = "UPDATE categories SET CategoryName = :CategoryName WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':CategoryName' => $categoryName,
            ':id' => $categoryID,
        ]);

        // Sau khi cập nhật thành công, chuyển hướng về trang danh sách thương hiệu
        header("Location: ../category.php");
    } catch (PDOException $e) {
        echo "Lỗi khi cập nhật thương hiệu: " . $e->getMessage();
    }
}
?>
