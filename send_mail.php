<?php
	date_default_timezone_set("Asia/Taipei");

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'vendor/phpmailer/phpmailer/src/Exception.php';
    require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require 'vendor/phpmailer/phpmailer/src/SMTP.php';
    require 'private.php';

    define("SEND_MODE_JOIN", 1);
    define("SEND_MODE_FULL_NOTIFY", 2);
    define("SEND_MODE_HOST_CANCEL", 3);

	$data = json_decode($_POST['data']);
	$mailto = json_decode($_POST['to']);
	$character = htmlspecialchars($_POST['character'], ENT_QUOTES);
    $mode = (int)$_POST['mode'];
    error_log(print_r($mailto, true));

	$epsoide_data = explode('/', $data->reference);
	$epsoide = "S{$epsoide_data[2]}E{$epsoide_data[3]}";
	$filename = "{$epsoide}.txt";
	$series = $epsoide_data[1];

	$start_time = date("Y-m-d H:i", strtotime($data->start_time));
	$start_date = date("Y/m/d", strtotime($data->start_time));
    

    switch ($mode) {
        case SEND_MODE_JOIN:
            $subject = "英文讀書會 {$start_date} {$data->title}";
            $message = "<br>時間：{$start_time}<br>地點：{$data->mode_data}<br>美劇：{$data->title}<br>角色：{$character}<br>主辦人：{$data->host}<br>聯絡方式：{$data->host_email}";
            //$filepath = "/home/lalala/source/{$series}/{$filename}";
            $filepath = "source/{$series}/{$filename}";
            break;

        case SEND_MODE_FULL_NOTIFY:
            $subject = "【成團通知】英文讀書會 {$start_date} {$data->title}";
            $message = "{$start_date} {$data->title} 的讀書會滿團啦！<br>記得在 <span style=\"color:red\">{$start_time}</span> 時前往 {$data->mode_data} 參加～";
            $filepath = null;
            break;

        case SEND_MODE_HOST_CANCEL:
            $subject = "【取消通知】英文讀書會 {$start_date} {$data->title}";
            $message = "您參加的 {$start_date} {$data->title} 英文讀習會已被主辦人取消<br>如需要更多的練習可以<a href=\"https://lalala.acsite.org/entalk/\">自行開團</a>喔 :)";
            $filepath = null;
            break;

        default:
            exit();
            break;
    }

    sendmail($mailto, $subject, $message, $filepath);

function sendmail($email, $subject, $message, $filepath = null){
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

    $mail->Username = $mail_account;
    $mail->Password = $mail_password;
    //這邊是你的gmail帳號和密碼

    $mail->FromName = "英文讀書會";
    // 寄件者名稱(你自己要顯示的名稱)
    $mail->From = "info@lalala.acsite.org"; 
    //回覆信件至此信箱

    // 收件者信箱
    //$name = "mailto";
    // 收件者的名稱or暱稱

    $mail->SMTPKeepAlive = true;   
    // $mail->Mailer = “smtp”; // don't change the quotes!
    $mail->Mailer = '“smtp”'; // don't change the quotes!

    foreach ($email as $mailto) { 
        if($email == "") {
            continue;
        }
        $mail->AddAddress($mailto, $mailto);
    }

    $mail->IsHTML(true); // send as HTML

    $mail->Subject = $subject; 
    // 信件標題
    $mail->Body = $message;
    //信件內容(html版，就是可以有html標籤的如粗體、斜體之類)

    if($filepath) {
        $mail->addAttachment($filepath);
    }

    if(!$mail->Send()){
        error_log("寄信發生錯誤：" . $mail->ErrorInfo);
    //如果有錯誤會印出原因
    }
    else{ 
        error_log("寄信成功:". print_r($email, true));
    }

    $mail->ClearAddresses();
}