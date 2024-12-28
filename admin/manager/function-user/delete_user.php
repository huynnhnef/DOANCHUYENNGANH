<?php
// Kết nối cơ sở dữ liệu
require "../../../config.php";

// Kiểm tra nếu có tham số 'id' trong URL
if (isset($_GET['id'])) {
    $username = $_GET['id'];

    try {
    
        $pdo->beginTransaction();

        $sql = "DELETE FROM warranty_accepts WHERE CustomerID = (SELECT id FROM users WHERE username = :username)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':username' => $username]);


        $sql = "DELETE FROM bills WHERE OrderdetailsID IN (SELECT id FROM orderdetails WHERE OrderID IN (SELECT id FROM orders WHERE CustomerID = (SELECT id FROM users WHERE username = :username)))";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':username' => $username]);


        $sql = "DELETE FROM orderdetails WHERE OrderID IN (SELECT id FROM orders WHERE CustomerID = (SELECT id FROM users WHERE username = :username))";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':username' => $username]);


        $sql = "DELETE FROM orders WHERE CustomerID = (SELECT id FROM users WHERE username = :username)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':username' => $username]);


        $sql = "DELETE FROM carts WHERE CustomerID = (SELECT id FROM users WHERE username = :username)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':username' => $username]);


        $sql = "DELETE FROM users WHERE username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':username' => $username]);


        $pdo->commit();

        // Chuyển hướng về trang người dùng sau khi xóa thành công
        header("Location: ../user.php");

    } catch (PDOException $e) {
        // Nếu có lỗi xảy ra, rollback giao dịch
        $pdo->rollBack();
        echo "Lỗi khi xóa: " . $e->getMessage();
    }
} else {
    echo "Không có người dùng để xóa.";
}
?>
