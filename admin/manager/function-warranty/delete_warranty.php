<?php
session_start();

// Kiểm tra quyền truy cập
if (!isset($_SESSION['user_email']) || !in_array($_SESSION['RoleID'], [5, 6])) {
    die("Bạn không có quyền truy cập trang này.");
}

// Kết nối với cơ sở dữ liệu
include "../../../config.php";

// Kiểm tra xem có ID bảo hành trong URL không
if (isset($_GET['warrantyID'])) {
    $warrantyID = $_GET['warrantyID'];

    try {
        // Xóa bảo hành từ bảng warranty_accepts
        $sql = "DELETE FROM warranty_accepts WHERE id = :warrantyID";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':warrantyID' => $warrantyID]);

        // Sau khi xóa, chuyển hướng về trang danh sách bảo hành
        header("Location: ../warranty.php");
        exit();
    } catch (PDOException $e) {
        die("Lỗi khi xóa bảo hành: " . $e->getMessage());
    }
} else {
    die("Không có ID bảo hành để xóa.");
}
?>
