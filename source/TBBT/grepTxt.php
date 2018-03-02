<?php
  //https://bigbangtrans.wordpress.com/series-1-episode-1-pilot-episode/

  require_once('../simple_html_dom.php');

  $html = file_get_html('https://bigbangtrans.wordpress.com/');

  $links = Array();
  foreach($html->find('li a') as $element) {
    if(preg_match('/series-([0-9]+)-episode-([0-9]+)/', $element->href, $matches)){
      $epsoide = (int)$matches[2];
      $serie = (int)$matches[1];
      $links["S{$serie}E{$epsoide}"] = $element->href;
    }
  }
  
  foreach($links as $filename => $link) {
    $html = file_get_html($link);
    $fp = fopen($filename.'.txt', 'w');
    foreach($html->find('div[class=entrytext] p') as $element) {
      $line = html_entity_decode($element->innertext, ENT_COMPAT, 'utf-8');
      fwrite($fp, strip_tags($line)."\r\n");
    }
    fclose($fp);
    echo "{$filename} done\n";
  }

