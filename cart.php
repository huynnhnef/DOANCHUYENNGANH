<?php
session_start();
require "config.php";

$user_email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : null;
if (!$user_email) {
    header("Location: account/login.php");
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM users WHERE email = :user_email");
$stmt->execute(['user_email' => $user_email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Không tìm thấy người dùng!";
    exit;
}

$customer_id = $user['id'];  

$stmt = $pdo->prepare("SELECT c.ProductID, p.ProductName, p.Price, c.Quantity, p.Image 
                       FROM carts c 
                       JOIN products p ON c.ProductID = p.id
                       WHERE c.CustomerID = :customer_id");
$stmt->execute(['customer_id' => $customer_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
foreach ($cart_items as $item) {
    $total += $item['Price'] * $item['Quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="img/android-icon-36x36.png" type="image/png">
    
    <title>Giỏ hàng - Phonevibe</title>
    <style>
        .container {
            width: 80%;
            margin: 0 auto;
            padding-top: 50px;
            margin-bottom: 24px;
        }

        .cart-container {
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .cart-item {
            display: flex;
            align-items: center;
            border-bottom: 1px solid #e1e1e1;
            padding: 20px 0;
        }

        .cart-item img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            margin-right: 20px;
            border-radius: 8px;
        }

        .cart-item-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .cart-item-info h3 {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }

        .cart-item-info p {
            font-size: 16px;
            color: #555;
        }

        .cart-item-info .price {
            color: #f39c12;
            font-size: 18px;
            font-weight: bold;
        }

        .quantity-input {
            width: 60px;
            padding: 8px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
            text-align: center;
            margin-top: 10px;
        }

        .quantity-input:focus {
            border-color: #f39c12;
            outline: none;
        }

        .cart-summary {
            margin-top: 20px;
            text-align: right;
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }

        .cart-summary p {
            margin: 10px 0;
            font-size: 18px;
        }

        .cart-summary p strong {
            color: #f39c12;
        }

        .checkout-btn {
            padding: 12px 25px;
            background-color: #fde800c3;
            color: black;
            border: none;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
            width: 100%;
            margin-top: 20px;
            transition: background-color 0.3s;
        }

        .checkout-btn:hover {
            background-color: #fbff7a;
        }

        .empty-cart {
            text-align: center;
            font-size: 18px;
            color: #555;
            padding: 50px 0;
        }

        .remove-btn{
            background-color: #fde800c3;
            color: black;
            border: none;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
        }
        .remove-btn:hover {
            background-color: #fbff7a;
        }

        /* Modal */
        .modalPay {
            display: none; 
            position: fixed;
            z-index: 1; 
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4); 
            justify-content: center; 
            align-items: center; 
        }

        /* Nội dung của Modal */
        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            top: 10%;
            left: 0;
            width: 40%;
            max-width: 500px;
            text-align: center;
            z-index: 10; 
        }

        .modal-content h2 {
            margin-bottom: 20px;
            font-size: 2.2rem;
        }

        .modal-content a {
            text-decoration: none;
            color: black;
            font-size: 2rem;
            padding: 10px 20px;
            width: 80%;
            border-radius: 5px;
            margin: 10px;
            display: inline-block;
            transition: background-color 0.3s;
        }

        .modal-content a:hover {
            background-color: #fbff7a;
        }

        .modal-content a:hover img{
            transform: translateY(-1%);
        }

        #closeModalBtn {
            background-color: #fde800c3;
            color: black;
            padding: 10px 20px;
            border-radius: 5px;
            margin-top: 10px;
            cursor: pointer;
        }

        #closeModalBtn:hover{
            background-color: #fbff7a;
        }


    </style>
</head>
<body>

    <?php include "include/header.php"; ?>

    <div class="container">
        <div class="cart-container">
            <?php if (count($cart_items) > 0): ?>
                <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item">
                        <img src="./img/<?php echo $item['Image']; ?>" alt="<?php echo $item['ProductName']; ?>">
                        <div class="cart-item-info">
                            <h3><?php echo $item['ProductName']; ?></h3>
                            <p><strong>Giá: </strong><span class="price"><?php echo number_format($item['Price'], 2); ?> VND</span></p>
                            <input type="number" class="quantity-input" value="<?php echo $item['Quantity']; ?>" min="1" data-product-id="<?php echo $item['ProductID']; ?>">
                        </div>
                        <button class="remove-btn" data-product-id="<?php echo $item['ProductID']; ?>"><i class="fas fa-trash-alt"></i></button>
                    </div>
                <?php endforeach; ?>
                <div class="cart-summary">
                    <p><strong>Tổng tiền: </strong><span id="total-price"><?php echo number_format($total, 2); ?> VND</span></p>
                    <button class="checkout-btn" id="openModalBtn">Thanh toán</button>
                </div>

                
            <?php else: ?>
                <p class="empty-cart">Chưa có sản phẩm trong giỏ hàng.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal for payment method -->
    <div id="paymentModal" class="modalPay">
        <div class="modal-content">
            <h2>Chọn phương thức thanh toán</h2>
            <a href="momo_php/payment.php?total-price=<?php echo str_replace(',', '', number_format($total, 2)); ?>"><img src="img/momo.png" alt="" style="width: 30%;"></a>
            <a href="vnpay_php/vnpay_pay.php?total-price=<?php echo str_replace(',', '', number_format($total, 2)); ?>"><img src="img/vnpay.png" alt="" style="width: 30%;"></a>
            <button id="closeModalBtn" class="checkout-btn">Đóng</button>
        </div>
    </div>

    <?php include "include/footer.php"; ?>
    <script src="js/logoutModal.js"></script> 
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
       $(document).ready(function () {
            $("#paymentModal").css("display", "none"); 

            $("#openModalBtn").on("click", function() {
                $("#paymentModal").css("display", "flex"); 
            });

            $("#closeModalBtn").on("click", function() {
                $("#paymentModal").css("display", "none");  
            });

            $(window).on("click", function(event) {
                if ($(event.target).is("#paymentModal")) {
                    $("#paymentModal").css("display", "none"); 
                }
            });

            // Lắng nghe sự kiện thay đổi số lượng sản phẩm trong giỏ hàng
            $(".quantity-input").on("input", function () {
                var productId = $(this).data("product-id");
                var quantity = $(this).val();

                if (quantity < 1) {
                    quantity = 1;  
                    $(this).val(quantity);
                }

                
                $.ajax({
                    url: 'cart/add_cart.php',
                    type: 'POST',
                    data: {
                        product_id: productId,
                        quantity: quantity
                    },
                    success: function (response) {
                        var data = JSON.parse(response);
                        if (data.success) {
                            updateCartTotal();
                        } else {
                            alert("Có lỗi khi cập nhật giỏ hàng.");
                        }
                    }
                });
            });

            // Lắng nghe sự kiện xóa sản phẩm khỏi giỏ hàng
            $(".remove-btn").on("click", function () {
                var productId = $(this).data("product-id");

                $.ajax({
                    url: 'cart/remove_cart.php',
                    type: 'POST',
                    data: {
                        product_id: productId
                    },
                    success: function (response) {
                        var data = JSON.parse(response);
                        if (data.success) {
                            updateCartTotal();
                            $(this).closest(".cart-item").remove();  
                            location.reload();  
                        } else {
                            alert("Có lỗi khi xóa sản phẩm.");
                        }
                    }
                });
            });

            // Hàm cập nhật lại tổng giá trị giỏ hàng
            function updateCartTotal() {
                $.ajax({
                    url: 'cart.php',  
                    success: function (response) {
                        var total = $(response).find("#total-price").text();
                        $("#total-price").text(total);  
                    }
                });
            }
        });


    </script>
    
</body>
</html>

