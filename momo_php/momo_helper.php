<?php
function execPostRequest($url, $data)
{
    $ch = curl_init($url);

    // Cấu hình các cài đặt của cURL
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data)
    ));
    
    // Tắt kiểm tra chứng chỉ SSL
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    // Cài đặt thời gian chờ cho kết nối và phản hồi
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);  // Thời gian chờ tổng thể cho yêu cầu
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);  // Thời gian chờ cho kết nối
    
    // Thực thi yêu cầu cURL
    $result = curl_exec($ch);
    
    // Kiểm tra lỗi
    if(curl_errno($ch)) {
        // Nếu có lỗi, trả về thông báo lỗi
        $error_msg = curl_error($ch);
        curl_close($ch);
        return "cURL Error: " . $error_msg;
    }

    // Kiểm tra mã HTTP trả về
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    // Đóng kết nối cURL
    curl_close($ch);
    
    // Kiểm tra mã HTTP
    if ($httpCode >= 200 && $httpCode < 300) {
        // Nếu mã HTTP 2xx (thành công), trả về kết quả
        return $result;
    } else {
        // Nếu không phải mã thành công, trả về mã lỗi
        return "HTTP Error: " . $httpCode;
    }
}
?>
