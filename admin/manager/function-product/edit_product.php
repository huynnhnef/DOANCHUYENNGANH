<?php
// Kết nối cơ sở dữ liệu
require_once '../../../config.php';

// Kiểm tra nếu form được submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy thông tin từ form
    $productName = $_POST['product_name'];
    $productDescription = $_POST['product_description'];
    $productPrice = $_POST['product_price'];
    $categoryID = $_POST['category'];
    $brandID = $_POST['brand'];
    $colors = isset($_POST['colors']) ? $_POST['colors'] : [];
    $stock = $_POST['stock'];
    $productID = $_GET['id'];  // Lấy ID sản phẩm từ URL

    $stmt = $pdo->prepare("SELECT BrandName FROM brands where id = :BrandID");
    $stmt->execute([":BrandID"=>$brandID]);

    $brandRow = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($brandRow) {
        $brandName=$brandRow['BrandName'];
    }
    // Kiểm tra nếu mảng colors không rỗng và có giá trị hợp lệ
    $colors = array_filter($colors, function($color) {
        return !empty(trim($color));  // Chỉ giữ lại các giá trị màu sắc hợp lệ (không trống)
    });

    if (empty($colors)) {
        echo "Không có màu sắc hợp lệ được chọn.";
        exit;
    }

    try {
        // Bắt đầu transaction
        $pdo->beginTransaction();

        // Cập nhật thông tin sản phẩm
        $stmt = $pdo->prepare("UPDATE products SET ProductName = :productName, Description = :productDescription, Price = :productPrice, BrandID = :brandID, CategoryID = :categoryID WHERE id = :productID");
        $stmt->execute([
            ":productName" => $productName,
            ":productDescription" => $productDescription,
            ":productPrice" => $productPrice,
            ":brandID" => $brandID,
            ":categoryID" => $categoryID,
            ":productID" => $productID
        ]);

        // Xử lý màu sắc cho sản phẩm
        foreach ($colors as $colorName) {
            $colorStmt = $pdo->prepare("SELECT id FROM colors WHERE ColorName = :colorName");
            $colorStmt->execute([":colorName" => $colorName]);
            $colorRow = $colorStmt->fetch(PDO::FETCH_ASSOC);

            if ($colorRow) {
                $colorID = $colorRow['id'];
            } else {
                $insertColorStmt = $pdo->prepare("INSERT INTO colors (ColorName) VALUES (:colorName)");
                $insertColorStmt->execute([":colorName" => $colorName]);
                $colorID = $pdo->lastInsertId();
            }

            // Kiểm tra xem kết hợp ProductID và ColorID đã tồn tại trong bảng product_colors chưa
            $checkColorStmt = $pdo->prepare("SELECT * FROM product_colors WHERE ProductID = :productID AND ColorID = :colorID");
            $checkColorStmt->execute([":productID" => $productID, ":colorID" => $colorID]);

            if ($checkColorStmt->rowCount() == 0) {
                $stmt = $pdo->prepare("INSERT INTO product_colors (ProductID, ColorID) 
                                       VALUES (:productID, :colorID)");
                $stmt->execute([":productID" => $productID, ":colorID" => $colorID]);
            }
        }

        // Xử lý ảnh
        if (!empty($_FILES['images']['name'][0])) {
            $imageFiles = $_FILES['images'];
            $imageCount = count($imageFiles['name']);
            $uploadDir = "../../img/";

            // Xác định thư mục theo thương hiệu
            switch($brandID){
                case "1":
                    $uploadDir .= "samsung/";
                    break;
                case "2":
                    $uploadDir .= "iphone/";
                    break;
                case "3":
                    $uploadDir .= "xiaomi/";
                    break;
                case "4":
                    $uploadDir .= "oppo/";
                    break;
                case "5":
                    $uploadDir .= "vivo/";
                    break;
                case "6":
                    $uploadDir .= "realme/";
                    break;
                default:
                    $uploadDir .= "others/";
                    break;
            }

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Tạo thư mục cho từng sản phẩm
            $productDir = $uploadDir . 'product_' . $productID . '/';
            if (!is_dir($productDir)) {
                mkdir($productDir, 0777, true);
            }

            // Xóa các hình ảnh cũ (nếu có)
            $imageStmt = $pdo->prepare("SELECT Image FROM product_color_images WHERE ProductID = :productID");
            $imageStmt->execute([":productID" => $productID]);
            $oldImages = $imageStmt->fetchAll(PDO::FETCH_ASSOC);

            // Duyệt qua các hình ảnh cũ và xóa chúng
            foreach ($oldImages as $oldImage) {
                $oldImagePath = $productDir . $oldImage['Image'];
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);  // Xóa ảnh cũ
                }
            }

            // Xóa các bản ghi hình ảnh cũ trong database
            $deleteImagesStmt = $pdo->prepare("DELETE FROM product_color_images WHERE ProductID = :productID");
            $deleteImagesStmt->execute([":productID" => $productID]);

            // Duyệt qua các file ảnh mới và lưu vào thư mục riêng của sản phẩm
            for ($i = 0; $i < $imageCount; $i++) {
                // Tạo tên ảnh duy nhất, dựa trên ID sản phẩm và thời gian
                $imageName = uniqid() . '-' . basename($imageFiles['name'][$i]);
                $imagePath = $productDir . $imageName;

                // Di chuyển ảnh vào thư mục đúng
                if (move_uploaded_file($imageFiles['tmp_name'][$i], $imagePath)) {
                    foreach ($colors as $colorName) {
                        // Kiểm tra màu sắc trong bảng colors và thêm ảnh vào bảng product_color_images
                        $colorStmt = $pdo->prepare("SELECT id FROM colors WHERE ColorName = :colorName");
                        $colorStmt->execute([":colorName" => $colorName]);
                        $colorRow = $colorStmt->fetch(PDO::FETCH_ASSOC);

                        if ($colorRow) {
                            $colorID = $colorRow['id'];
                        }
                        // Lưu đường dẫn ảnh vào bảng product_color_images (lưu đường dẫn tương đối từ thư mục gốc)
                        $imageRelativePath = '../../img/' . strtolower($brandName) . '/product_' . $productID . '/' . $imageName;
                        // Thêm ảnh vào bảng product_color_images
                        $stmt = $pdo->prepare("INSERT INTO product_color_images (ProductID, ColorID, Image) 
                                               VALUES (:productID, :colorID, :image)");
                        $stmt->execute([":productID" => $productID, ":colorID" => $colorID, ":image" => $imageRelativePath]);
                    }
                } else {
                    echo "Lỗi khi tải ảnh: " . $imageFiles['name'][$i] . "<br>";
                }
            }
        }

        // Commit transaction
        $pdo->commit();
        header("Location: ../product.php");

    } catch (Exception $e) {
        // Rollback nếu có lỗi
        $pdo->rollBack();
        echo "Có lỗi xảy ra: " . $e->getMessage();
    }
}
?>
