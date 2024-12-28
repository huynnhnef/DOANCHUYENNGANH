<footer>
        <div class="footer-content">
            <div class="contact">
                <h2>Liên hệ</h2>
                <span class="contact-icon-fb"><a href=""><img src="img/fb.png" alt=""></a></span>
                <span class="contact-icon-zalo"><a href=""><img src="img/zl.png" alt=""></a></span>
            </div>
            <div class="about">
                <h2>Về công ty</h2>
                <p><a href="#">Giới thiệu công ty</a></p>
                <p><a href="#">Gửi góp ý, khiếu nại</a></p>
            </div>
            <div class="other">
                <h2>Thương hiệu nổi tiếng</h2>

                <?php
                    $stmt = $pdo->prepare("SELECT id, BrandName FROM brands");
                    $stmt->execute();
                    $brands = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach($brands as $brand){
                        ?>
                            <p><a href="product.php?category_id=4&brand_id=<?php echo $brand['id']; ?>"><?php echo $brand['BrandName']; ?></a></p>
                        <?php
                    }
                ?>
            </div>
            <div class="account">
                <h2>Tài khoản</h2>
                <p><a href="../account.php">Thông tin tài khoản</a></p>
                <p><a href="../cart.php">Xem giỏ hàng</a></p>
                <p><a href="../order.php">Theo dõi đơn hàng</a></p>
            </div>
        </div>
        <div class="address">
            <p>PhoneVibe - Cửa hàng điện thoại số 2 Việt Nam</p>
            <p>Địa chỉ: 180 Cao Lỗ, Phường 4, Quận 8</p>
        </div>
    </footer>