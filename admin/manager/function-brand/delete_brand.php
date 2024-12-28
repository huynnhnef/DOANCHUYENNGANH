<?php

include "../../../config.php"; 

if (isset($_GET['id'])) {
    $brandID = $_GET['id'];

    try {
        $pdo->beginTransaction();

        $sql = "DELETE FROM carts WHERE ProductID IN (SELECT id FROM products WHERE BrandID = :BrandID)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':BrandID' => $brandID]);

        $sql = "DELETE FROM product_colors WHERE ProductID IN (SELECT id FROM products WHERE BrandID = :BrandID)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':BrandID' => $brandID]);
        
        $sql = "DELETE FROM orderdetails WHERE ProductID IN (SELECT id FROM products WHERE BrandID = :BrandID)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':BrandID' => $brandID]);

        $sql = "DELETE FROM products WHERE BrandID = :BrandID ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':BrandID'=>$brandID]);

        $sql = "DELETE FROM brands WHERE id = :BrandID";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':BrandID'=>$brandID]);

        $pdo->commit();
        header("Location: ../brand.php?success=1");
        exit();
    } catch (PDOException $e) {
        header("Location: ../brand.php?error=1");
        exit();
    }
} else {

    header("Location: ../brand.php");
    exit();
}
?>
