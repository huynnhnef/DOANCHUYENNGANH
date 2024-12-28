<?php
// Kết nối cơ sở dữ liệu
include '../../../config.php';

// Kiểm tra xem dữ liệu đã được gửi đến chưa
if (isset($_POST['orderID']) && isset($_POST['status'])) {
    // Lấy OrderID và trạng thái mới từ form
    $orderID = $_POST['orderID'];
    $status = $_POST['status'];

    try {
        // Cập nhật trạng thái trong cơ sở dữ liệu
        $sql = "UPDATE orders SET Status = :status WHERE id = :orderID";
        $stmt = $pdo->prepare($sql);

        // Thực thi câu lệnh
        $stmt->execute([':status'=>$status, ':orderID'=>$orderID]);

        // Quay lại trang orders.php sau khi cập nhật thành công
        header("Location: ../order.php");
        exit;
    } catch (PDOException $e) {
        echo "Lỗi khi cập nhật trạng thái đơn hàng: " . $e->getMessage();
    }
}
?>
