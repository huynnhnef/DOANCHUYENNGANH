<?php
require "../../../config.php";

// Kiểm tra xem form có được gửi đi không
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $brandID = $_POST['brand_id'];
    $brandName = $_POST['brand_name'];

    try {
        // Cập nhật thông tin thương hiệu
        $sql = "UPDATE brands SET BrandName = :BrandName WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':BrandName' => $brandName,
            ':id' => $brandID,
        ]);

        // Sau khi cập nhật thành công, chuyển hướng về trang danh sách thương hiệu
        header("Location: ../brand.php");
    } catch (PDOException $e) {
        echo "Lỗi khi cập nhật thương hiệu: " . $e->getMessage();
    }
}
?>
