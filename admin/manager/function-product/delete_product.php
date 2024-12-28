<?php
require_once '../../../config.php';

// Kiểm tra nếu có ID sản phẩm cần xóa
if (isset($_GET['id'])) {
    $productID = $_GET['id'];

    try {
        // Bắt đầu transaction
        $pdo->beginTransaction();

        // Lấy thông tin sản phẩm từ bảng `products` dựa trên `id`
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :productID");
        $stmt->execute([":productID" => $productID]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            // Lấy các hình ảnh của sản phẩm từ bảng `product_color_images`
            $imageStmt = $pdo->prepare("SELECT Image FROM product_colors WHERE ProductID = :productID");
            $imageStmt->execute([":productID" => $productID]);
            $images = $imageStmt->fetchAll(PDO::FETCH_ASSOC);

            // Xóa các hình ảnh trong thư mục nếu có
            foreach ($images as $image) {
                // Đường dẫn đến ảnh của sản phẩm
                $imagePath = "../../img/" . $image['Image'];

                // Kiểm tra nếu file ảnh tồn tại và xóa nó
                if (file_exists($imagePath)) {
                    unlink($imagePath);  // Xóa tệp hình ảnh
                }
            }

           

            // Xóa các bản ghi trong bảng `product_colors` (Màu sắc của sản phẩm)
            $deleteColorsStmt = $pdo->prepare("DELETE FROM product_colors WHERE ProductID = :productID");
            $deleteColorsStmt->execute([":productID" => $productID]);

            // Nếu sản phẩm có ảnh trong thư mục riêng (product_ID), xóa thư mục đó
            $brandDir = '';
            switch ($product['BrandID']) {
                case 1:
                    $brandDir = 'samsung/';
                    break;
                case 2:
                    $brandDir = 'iphone/';
                    break;
                case 3:
                    $brandDir = 'xiaomi/';
                    break;
                case 4:
                    $brandDir = 'oppo/';
                    break;
                case 5:
                    $brandDir = 'vivo/';
                    break;
                case 6:
                    $brandDir = 'realme/';
                    break;
                default:
                    $brandDir = 'others/';
                    break;
            }

            // Thư mục sản phẩm
            $productDir = "../../img/" . $brandDir . "product_" . $productID;

            // Nếu thư mục sản phẩm tồn tại, xóa thư mục và tất cả các file bên trong
            if (is_dir($productDir)) {
                // Xóa tất cả các tệp trong thư mục
                $files = glob($productDir . '/*'); // Lấy tất cả tệp trong thư mục
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);  // Xóa từng tệp
                    }
                }

                // Sau khi xóa các tệp, xóa thư mục
                rmdir($productDir);
            }

            // Cuối cùng, xóa sản phẩm trong bảng `products`
            $deleteProductStmt = $pdo->prepare("DELETE FROM products WHERE id = :productID");
            $deleteProductStmt->execute([":productID" => $productID]);

            // Commit transaction
            $pdo->commit();

            // Quay lại trang danh sách sản phẩm
            header("Location: ../product.php");
            exit;
        } else {
            echo "Sản phẩm không tồn tại.";
        }
    } catch (Exception $e) {
        // Rollback nếu có lỗi
        $pdo->rollBack();
        echo "Có lỗi xảy ra: " . $e->getMessage();
    }
} else {
    echo "Không có ID sản phẩm.";
}
?>








