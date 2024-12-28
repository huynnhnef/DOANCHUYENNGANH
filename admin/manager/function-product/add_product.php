<?php
// Bật báo lỗi để hiển thị chi tiết
ini_set('display_errors', 1);
error_reporting(E_ALL);

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

    // Kiểm tra nếu người dùng đã chọn đúng 4 màu sắc (chỉ cần cho Điện thoại)
    if ($categoryID == 1 && count($colors) != 4) {  // categoryID = 1 là Điện thoại
        echo "Vui lòng chọn 4 màu sắc cho sản phẩm Điện thoại.";
        exit;
    }

    // Mảng chứa ảnh màu sắc
    $colorImages = [];

    try {
        // Bắt đầu transaction
        $pdo->beginTransaction();

        // Kiểm tra sản phẩm đã tồn tại chưa
        $stmt = $pdo->prepare("SELECT id FROM products WHERE ProductName = :productName AND BrandID = :brandID AND CategoryID = :categoryID");
        $stmt->execute([":productName" => $productName, ":brandID" => $brandID, ":categoryID" => $categoryID]);
        $existingProduct = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingProduct) {
            // Nếu sản phẩm đã có, lấy ID của sản phẩm
            $productID = $existingProduct['id'];
        } else {
            // Nếu sản phẩm chưa có, thêm sản phẩm mới vào bảng `products`
            $stmt = $pdo->prepare("INSERT INTO products (ProductName, Description, Price, BrandID, CategoryID) 
                                   VALUES (:productName, :productDescription, :productPrice, :brandID, :categoryID)");
            $stmt->execute([":productName" => $productName, ":productDescription" => $productDescription, ":productPrice" => $productPrice, ":brandID" => $brandID, ":categoryID" => $categoryID]);
            $productID = $pdo->lastInsertId();  // Lấy ID sản phẩm mới
        }

        // Xử lý cho Điện thoại (liên kết với bảng product_colors)
        if ($categoryID == 1) {  // Điện thoại
            $colorKeys = ['main', 'second', 'third', 'fourth']; // Mảng các màu
            foreach ($colors as $index => $colorName) {
                // Kiểm tra nếu màu sắc đã có trong bảng colors, nếu không thêm mới
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
                    // Thêm vào bảng `product_colors` nếu chưa có
                    $stmt = $pdo->prepare("INSERT INTO product_colors (ProductID, ColorID) 
                                           VALUES (:productID, :colorID)");
                    $stmt->execute([":productID" => $productID, ":colorID" => $colorID]);
                }

                // Kiểm tra mảng $_FILES và xử lý ảnh cho từng màu
                if (!empty($_FILES['images']['name'][$colorKeys[$index]])) {
                    // Kiểm tra nếu ảnh đã được gửi và không có lỗi
                    $imageFile = $_FILES['images']['name'][$colorKeys[$index]];
                    if ($_FILES['images']['error'][$colorKeys[$index]] == 0) {
                        // Lấy thông tin file ảnh
                        $imageTmpName = $_FILES['images']['tmp_name'][$colorKeys[$index]];

                        // Đảm bảo thư mục tồn tại
                        $uploadDir = "../../../img/";
                        $productDir = $uploadDir . 'product_' . $productID . '/';

                        // Tạo thư mục nếu chưa có
                        if (!is_dir($productDir)) {
                            if (!mkdir($productDir, 0777, true)) {
                                echo "Không thể tạo thư mục: " . $productDir . "<br>";
                            }
                        }

                        // Tạo đường dẫn ảnh
                        $imagePath = $productDir . uniqid() . '-' . basename($imageFile);

                        // Di chuyển file tải lên vào thư mục mong muốn
                        if (move_uploaded_file($imageTmpName, $imagePath)) {
                            // Cập nhật ảnh vào bảng product_colors
                            $stmt = $pdo->prepare("UPDATE product_colors SET Image = :image WHERE ProductID = :productID AND ColorID = :colorID");
                            $stmt->execute([":image" => $imagePath, ":productID" => $productID, ":colorID" => $colorID]);

                            // Lưu ảnh vào mảng colorImages
                            $colorImages[] = $imagePath; // Thêm ảnh vào mảng
                        } else {
                            echo "Lỗi khi tải ảnh lên cho màu " . htmlspecialchars($colorName) . "<br>";
                        }
                    }
                }
            }

            // Nếu có ảnh trong mảng colorImages, chọn ngẫu nhiên một ảnh để làm ảnh gốc cho sản phẩm
            if (count($colorImages) > 0) {
                $randomImage = $colorImages[array_rand($colorImages)]; // Lấy ngẫu nhiên 1 ảnh
                $stmt = $pdo->prepare("UPDATE products SET Image = :image WHERE id = :productID");
                $stmt->execute([":image" => $randomImage, ":productID" => $productID]);
            }
        } else {  // Máy tính bảng (chỉ thêm vào bảng products)
            // Kiểm tra và xử lý ảnh cho Máy tính bảng
            if (!empty($_FILES['images']['name']['main'])) {
                $imageFile = $_FILES['images']['name']['main'];
                $imageTmpName = $_FILES['images']['tmp_name']['main'];

                // Đảm bảo thư mục tồn tại
                $uploadDir = "../../../img/";
                $productDir = $uploadDir . 'product_' . $productID . '/';

                // Tạo thư mục nếu chưa có
                if (!is_dir($productDir)) {
                    if (!mkdir($productDir, 0777, true)) {
                        echo "Không thể tạo thư mục: " . $productDir . "<br>";
                    }
                }

                // Tạo đường dẫn ảnh
                $imagePath = $productDir . uniqid() . '-' . basename($imageFile);

                // Di chuyển file tải lên vào thư mục mong muốn
                if (move_uploaded_file($imageTmpName, $imagePath)) {
                    // Cập nhật ảnh vào bảng products
                    $stmt = $pdo->prepare("UPDATE products SET Image = :image WHERE id = :productID");
                    $stmt->execute([":image" => $imagePath, ":productID" => $productID]);
                } else {
                    echo "Lỗi khi tải ảnh lên cho sản phẩm Máy tính bảng.<br>";
                }
            }
        }

        // Commit transaction
        $pdo->commit();
        header("Location: ../product.php");
    } catch (Exception $e) {
        // Rollback transaction nếu có lỗi
        $pdo->rollBack();
        echo "Lỗi: " . $e->getMessage();
    }
}
?>
