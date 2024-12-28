<?php
// Kết nối với cơ sở dữ liệu
include "../../../config.php"; 


if (isset($_GET['id'])) {
    $categoryID = $_GET['id'];

    try {

        $pdo->beginTransaction();

        $sql="DELETE FROM carts WHERE ProductID IN (SELECT id FROM products WHERE CategoryID=:categoryID)";
        $stmt=$pdo->prepare($sql);
        $stmt->execute([':categoryID'=>$categoryID]);

        $sql="DELETE FROM product_colors WHERE ProductID IN (SELECT id FROM products WHERE CategoryID=:categoryID)";
        $stmt=$pdo->prepare($sql);
        $stmt->execute([':categoryID'=>$categoryID]);

        $sql="DELETE FROM orderdetails WHERE ProductID IN(SELECT id FROM products WHERE CategoryID=:categoryID)";
        $stmt=$pdo->prepare($sql);
        $stmt->execute([':categoryID'=>$categoryID]);

        $sql="DELETE FROM products WHERE CategoryID=:categoryID";
        $stmt=$pdo->prepare($sql);
        $stmt->execute([':categoryID'=>$categoryID]);

        $sql = "DELETE FROM categories WHERE id = :categoryID";
        $stmt = $pdo->prepare($sql);

        $stmt->execute([':categoryID'=>$categoryID]);
        $pdo->commit();
        header("Location: ../category.php?success=1");
        exit();
    } catch (PDOException $e) {

        header("Location: ../category.php?error=1");
        exit();
    }
} else {

    header("Location: ../category.php");
    exit();
}
?>
