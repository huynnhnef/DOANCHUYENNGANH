<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_email'])) {
    echo "Bạn cần đăng nhập để thực hiện hành động này.";
    exit();
}

// Lấy email người dùng từ session
$user_email = $_SESSION['user_email'];

// Kết nối với cơ sở dữ liệu
include "../../../config.php";

try {
    // Truy vấn thông tin người dùng dựa trên email
    $sql = "SELECT RoleID FROM users WHERE Email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':email' => $user_email]);

    // Lấy kết quả
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Kiểm tra nếu người dùng tồn tại và có RoleID 5 hoặc 6
    if ($user && ($user['RoleID'] == 5 || $user['RoleID'] == 6)) {
        // Người dùng có quyền xóa
        if (isset($_GET['OrderID'])) {
            $orderID = $_GET['OrderID'];

            // Bắt đầu transaction để đảm bảo tính toàn vẹn khi xóa
            $pdo->beginTransaction();

            // 1. Xóa các bản ghi trong bảng 'warranty_accepts' có BillID tham chiếu đến OrderID
            $deleteWarrantySql = "DELETE FROM warranty_accepts WHERE BillID IN (SELECT id FROM bills WHERE OrderdetailsID IN (SELECT id FROM orderdetails WHERE OrderID = :orderID))";
            $stmtWarranty = $pdo->prepare($deleteWarrantySql);
            $stmtWarranty->execute([':orderID' => $orderID]);

            // 2. Xóa các bản ghi trong bảng 'bills' tham chiếu đến OrderID
            $deleteBillsSql = "DELETE FROM bills WHERE OrderdetailsID IN (SELECT id FROM orderdetails WHERE OrderID = :orderID)";
            $stmtBills = $pdo->prepare($deleteBillsSql);
            $stmtBills->execute([':orderID' => $orderID]);

            // 3. Xóa các bản ghi trong bảng 'orderdetails' liên quan đến OrderID
            $deleteDetailsSql = "DELETE FROM orderdetails WHERE OrderID = :orderID";
            $stmtDetails = $pdo->prepare($deleteDetailsSql);
            $stmtDetails->execute([':orderID' => $orderID]);

            // 4. Xóa đơn hàng trong bảng 'orders'
            $deleteOrderSql = "DELETE FROM orders WHERE id = :orderID";
            $stmtOrder = $pdo->prepare($deleteOrderSql);
            $stmtOrder->execute([':orderID' => $orderID]);

            // Commit transaction nếu tất cả các câu lệnh xóa thành công
            $pdo->commit();

            // Chuyển hướng người dùng về trang danh sách đơn hàng sau khi xóa
            header("Location: ../order.php");
            exit();
        } else {
            echo "Không có đơn hàng để xóa.";
            exit();
        }
    } else {
        // Nếu người dùng không có quyền, hiển thị thông báo lỗi
        $_SESSION['message'] = "Bạn không có quyền xóa đơn hàng.";
        header("Location: ../order.php");
        exit();
    }
} catch (PDOException $e) {
    // Nếu có lỗi xảy ra, rollback transaction và hiển thị thông báo lỗi
    $pdo->rollBack();
    echo "Lỗi khi xóa đơn hàng: " . $e->getMessage();
}
?>
