<?php
  require 'private.php';

  define("LINE_CREATE", 1);
  define("LINE_VACANCY", 2);

  $data = json_decode($_POST['data']);
  $title = $data->title;
  $time = date("Y-m-d H:i", strtotime($data->start_time));
  $location = $data->mode_data;
  $mode = (int)$_POST['mode'];

  switch ($mode) {
    case LINE_CREATE:
      $eventName = 'new_meeting';
      break;
    case LINE_VACANCY:
      $eventName = 'someone_quit';
      break;
    default:
      exit();
      break;
  }

  $url = "https://maker.ifttt.com/trigger/{$eventName}/with/key/{$ifttt_key}";

  $payload = json_encode(array(
    'value1' => $title,
    'value2' => $time,
    'value3' => $location
  ));
   
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $output = curl_exec($ch); 
  curl_close($ch);
   
  error_log($output);
