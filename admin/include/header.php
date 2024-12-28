<?php
    session_start(); 
    require "../../config.php";
    $email=isset($_SESSION['user_email']) ? $_SESSION['user_email'] :'';
    
    $stmt=$pdo->prepare("SELECT * FROM users a JOIN roles r on a.RoleID=r.id where a.Email=:Email ");
    $stmt->execute([":Email"=>$email]);
    $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<header>
        
            <header class="header">
                <div class="header-1">
                    <a href="../index.php" class="logo"><img src="../../img/logo_PhoneVibe.png" alt=""><span>PhoneVibe</span></a>

                    <div class="icons icons-accept" style="padding: 0 16px;">
                        <div id="search-btn" class="fas fa-search" aria-hidden="true" ></div>
                        
                        
                        <?php if (isset($_SESSION['user_email'])): ?>
                        <div class="user-icon">
                            <a href="#" id="user-btn" class="fas fa-user" aria-hidden="true"></a>
                            <?php 
                                foreach ($result as $row){
                                    
                                  ?>
                                  
                                        <span style="font-size: 1.2rem;"><?php echo $row['Username']; ?>&nbsp;|
                                             <?php echo $row['RoleName']; ?> 
                                        </span>
                                  <?php
                                }
                            ?>

                            <div class="dropdown">
                                <a href="#" id="logout-btn">Đăng xuất</a> 
                            </div>
                        </div>
                        
                        <div id="logoutModal" class="modal">
                            <div class="modal-content">
                                <span class="close" onclick="closeModal()">&times;</span>
                                <i class="fas fa-exclamation-circle"></i>
                                <p>Bạn có muốn đăng xuất không?</p>
                                <div class="btn-logout-cancel">
                                    <button id="confirm-logout" onclick="logout()">Đăng xuất</button>
                                    <button id="cancel-logout" onclick="closeModal()">Trở lại</button>
                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                        <a href="account/login.php" id="login-btn" class="fas fa-user" aria-hidden="true" ></a>
                        <?php endif; ?>
                </div>
                
            </div>
    </header>