<?php
session_start();
require "config.php";
$stmt = $pdo->query("SELECT p.id, p.ProductName, p.Description, 
                    p.Price, b.BrandName, c.CategoryName, p.Image
                    FROM products p 
                    JOIN brands b ON p.BrandID = b.id
                    JOIN categories c ON p.CategoryID = c.id
                    ORDER BY rand() ASC limit 0, 10");

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/slideshow.css">
    <link rel="stylesheet" href="css/product.css">
    <script type="text/javascript" src="https://ahachat.com//customer-chats/customer_chat_vjylJEcSOd675dc45759b5e.js"></script>
    <link rel="icon" href="img/android-icon-36x36.png" type="image/png">
    <title>Phonevibe</title>
</head>
<body>
    
    <?php
        include "include/header.php";
    ?>

   
    <div class="slider-container">
        <div class="slider" onmouseover="stopAutoSlide()" onmouseout="startAutoSlide()">
            <img src="img/iphone16bg.jpg" alt="Slide 1">
            <img src="img/oppoa57bg.jpg" alt="Slide 2">
            <img src="img/iphone15prmbg.jpg" alt="Slide 3">
        </div>
        <span class="arrow-btn arrow-left" onclick="prevSlide()">
            <i class="fas fa-chevron-left"></i>
        </span>
        <span class="arrow-btn arrow-right" onclick="nextSlide()">
            <i class="fas fa-chevron-right"></i>
        </span>
        <span class="close-btn" onclick="closeSlider()">×</span>
    </div>




<div class="container">
    <div class="product-tittle">Sản phẩm nổi bật</div>
    <?php
            if ($products) {
                ?>
                <div class="product-list">
                <?php
                foreach ($products as $product) {
                    $imagePath = './img/' . $product['Image']; 
                    
                    ?>
                    <div class="product-item">
                        <a href="product-details.php?product_id=<?php echo $product['id']; ?>">
                            
                            <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($product['ProductName']); ?>">
                            <h2><?php echo htmlspecialchars($product['ProductName']); ?></h2>
                            <p><span style="color: red;"><?php echo number_format($product['Price'], 2); ?> VND</span></p>
                            <button class="btn_mua">Mua ngay</button>
                        </a>
                    </div>
                    <?php
                }
                ?>
                </div>
                <?php
            } else {
                echo 'Không có sản phẩm nào.';
            }
        ?>
</div>    

    <?php
        include "include/footer.php";
    ?>
    <script src="js/logoutModal.js"></script>

    <script src="js/brands.js"></script>

    <script>
        let currentSlide = 0;
        const slides = document.querySelectorAll('.slider img');
        const totalSlides = slides.length;
        let autoSlideInterval;

        function showSlide(index) {
            const slider = document.querySelector('.slider');
            const offset = -index * 100; 
            slider.style.transform = `translateX(${offset}%)`;
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % totalSlides;
            showSlide(currentSlide);
        }

        function prevSlide() {
            currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
            showSlide(currentSlide);
        }

        // Chuyển tự động mỗi 5 giây
        function startAutoSlide() {
            if (!autoSlideInterval) {
                autoSlideInterval = setInterval(nextSlide, 5000);
            }
        }

        // Dừng chuyển động tự động
        function stopAutoSlide() {
            clearInterval(autoSlideInterval);
            autoSlideInterval = null;
        }

        // Hiển thị slide đầu tiên
        showSlide(currentSlide);

        // Bắt đầu chuyển động tự động khi tải trang
        startAutoSlide();

        function closeSlider() {
            const sliderContainer = document.querySelector('.slider-container');
            sliderContainer.style.display = 'none';
        }
    </script>
</body>
</html>
