<?php
	date_default_timezone_set("Asia/Taipei");

	$data = json_decode($_POST['data']);
	$mailto = htmlspecialchars($_POST['to'], ENT_QUOTES);
	$character = htmlspecialchars($_POST['character'], ENT_QUOTES);

	$epsoide_data = explode('/', $data->reference);
	$epsoide = "S{$epsoide_data[2]}E{$epsoide_data[3]}";
	$filename = "{$epsoide}.txt";
	$series = $epsoide_data[1];

	$start_time = date("Y-m-d H:i:s", strtotime($data->start_time));
	$start_date = date("Y/m/d", strtotime($data->start_time));

	$subject = "英文口說練習 {$start_date} {$data->title}";
    $filepath = "/home/lalala/source/{$series}/{$filename}";

    $message = "\n時間：{$start_time}\n地點：{$data->mode_data}\n美劇：{$data->title}\n角色：{$character}\n主持人：{$data->host}\n聯絡方式：{$data->host_email}";

    $content = file_get_contents($filepath);
    $content = chunk_split(base64_encode($content));

    // a random hash will be necessary to send mixed content
    $separator = md5(time());

    // carriage return type (RFC)
    $eol = "\r\n";

    // main header (multipart mandatory)
    $headers = "From: 英文口說練習 <info@lalala.acsite.org>" . $eol;
    $headers .= "MIME-Version: 1.0" . $eol;
    $headers .= "Content-Type: multipart/mixed; boundary=\"" . $separator . "\"" . $eol;
    $headers .= "Content-Transfer-Encoding: 7bit" . $eol;
    $headers .= "This is a MIME encoded message." . $eol;

    // message
    $body = "--" . $separator . $eol;
    $body .= "Content-Type: text/plain; charset=\"iso-8859-1\"" . $eol;
    $body .= "Content-Transfer-Encoding: 8bit" . $eol;
    $body .= $message . $eol;

    // attachment
    $body .= "--" . $separator . $eol;
    $body .= "Content-Type: application/octet-stream; name=\"" . $filename . "\"" . $eol;
    $body .= "Content-Transfer-Encoding: base64" . $eol;
    $body .= "Content-Disposition: attachment" . $eol;
    $body .= $content . $eol;
    $body .= "--" . $separator . "--";

    //SEND Mail
    if (mail($mailto, $subject, $body, $headers)) {
        // echo "mail send ... OK"; // or use booleans here
    } else {
        //echo "mail send ... ERROR!";
    }