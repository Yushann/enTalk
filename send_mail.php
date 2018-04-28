<?php
	date_default_timezone_set("Asia/Taipei");

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'vendor/phpmailer/phpmailer/src/Exception.php';
    require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require 'vendor/phpmailer/phpmailer/src/SMTP.php';

	$data = json_decode($_POST['data']);
	$mailto = trim(htmlspecialchars($_POST['to'], ENT_QUOTES));
	$character = htmlspecialchars($_POST['character'], ENT_QUOTES);

	$epsoide_data = explode('/', $data->reference);
	$epsoide = "S{$epsoide_data[2]}E{$epsoide_data[3]}";
	$filename = "{$epsoide}.txt";
	$series = $epsoide_data[1];

	$start_time = date("Y-m-d H:i:s", strtotime($data->start_time));
	$start_date = date("Y/m/d", strtotime($data->start_time));

	$subject = "英文口說練習 {$start_date} {$data->title}";
    $filepath = "/home/lalala/source/{$series}/{$filename}";
    //$filepath = "source/{$series}/{$filename}";

    $message = "<br>時間：{$start_time}<br>地點：{$data->mode_data}<br>美劇：{$data->title}<br>角色：{$character}<br>主持人：{$data->host}<br>聯絡方式：{$data->host_email}";

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPAuth = true; // turn on SMTP authentication
    //這幾行是必須的
    $mail->Host = 'tls://mail.gmx.com:587';
    $mail->SMTPOptions = array(
       'ssl' => array(
         'verify_peer' => false,
         'verify_peer_name' => false,
         'allow_self_signed' => true
        )
    );

    $mail->CharSet = "utf-8"; //郵件編碼

    $mail->Username = "yushann@gmx.com";
    $mail->Password = "87654321";
    //這邊是你的gmail帳號和密碼

    $mail->FromName = "英文口說練習";
    // 寄件者名稱(你自己要顯示的名稱)
    $mail->From = "info@lalala.acsite.org"; 
    //回覆信件至此信箱


    $email = $mailto;
    // 收件者信箱
    $name = "mailto";
    // 收件者的名稱or暱稱

    $mail->SMTPKeepAlive = true;   
    $mail->Mailer = “smtp”; // don't change the quotes!


    $mail->AddAddress($email,$name);

    $mail->IsHTML(true); // send as HTML

    $mail->Subject = $subject; 
    // 信件標題
    $mail->Body = $message;
    //信件內容(html版，就是可以有html標籤的如粗體、斜體之類)

    $mail->addAttachment($filepath);

    if(!$mail->Send()){
    error_log("寄信發生錯誤：" . $mail->ErrorInfo);
    //如果有錯誤會印出原因
    }
    else{ 
    error_log("寄信成功");
    }