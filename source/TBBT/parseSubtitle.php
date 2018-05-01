<?php
  $file_info = new finfo(FILEINFO_MIME);
  $files = array_slice(scandir('.'), 2);
  $scripts = Array();
  $season_num = 0;
  $episode_mapping = Array();
/*
  foreach($files as $file){
    $mime_type = $file_info->buffer(file_get_contents($file));
    if($mime_type == 'text/plain; charset=utf-8') {

      if(!preg_match('/(S([0-9]+)E([0-9]+))/', $file, $filename_matches)){
        continue;
      }
      
      echo "parsing {$file}<br>";
      $result = parseFile($file);

      $season_num = max($filename_matches[2], $season_num);
      $episode_mapping[$filename_matches[2]] = max($filename_matches[3], $episode_mapping[$filename_matches[2]]);

      $scripts[$filename_matches[1]] = $result;
    }
  }
*/

  $result = parseFile($argv[1]);

  function parseFile($file) {
    exec('grep \'[a-zA-Z]:\' '.$file.' | awk -F":" \'{print $1}\' | sort | uniq', $output);

    $words = array();
    foreach($output as $people) {
      $lineCount = array();
      exec("grep -E '({$people}:)' {$file} | wc -lw", $lineCount);
      $tmp = preg_split('/[\s]+/', $lineCount[0]);
      $words[$people] = (int)$tmp[2] - (int)$tmp[1];
    }


    asort($words); 

    $newMapping = Array();
    $newCount = Array();
    $data = Array();
    $i = 0;
    $total = 0;
    $names = array_keys($words);
    $average = array_sum(array_values($words)) / count($words);

    $bestRatio = getBestRatio($words, $average);

    while(list($p, $w) = each($words)){
      $p = trim($p);
      $data["total_charactors"][$p] = $w;
      $newMapping[$i][] = $p;
      $total += $w;
      if($total > $average/$bestRatio) {
        $newCount[$i] = $total;
        $total = 0;
        $i++;
      }
    }

    while(list($k, $p) = each($newMapping)) {
      $data["recommend_group"][join(', ',$p)] = "";
    }
    $data["recommend_num"] = count($data["recommend_group"]);

    echo print_r($newCount);
    echo print_r($data["recommend_group"]);
    echo print_r($data["total_charactors"]);

    return $data;
  }

  function getBestRatio($words, $average){
    $ratios = array();
    for($i=3; $i<10; $i++){
      $ratios[$i] = getDiffNumberByRatio($words, $average, $i/10);
    }

    return array_search(min($ratios), $ratios)/10;
  }

  function getDiffNumberByRatio($words, $average, $ratio){
    $perPerson = (int)$average/$ratio;
    while(list($p, $w) = each($words)){
      $p = trim($p);
      $newMapping[$i][] = $p;
      $total += $w;
      if($total > $perPerson) {
        $newCount[$i] = $total;
        $total = 0;
        $i++;
      }
    }

    $diff = 0;
    $perPerson = array_sum(array_values($words))/count($newCount);
    foreach($newCount as $count) {
      $diff += (int)abs($perPerson - $count);
    }
  
    //人數小於三人的不要
    return count($newCount) < 3 ? 9999 : $diff;
  }
/*
?>
<html>
  <head>
  <script src="https://www.gstatic.com/firebasejs/4.9.1/firebase.js"></script>
  <script src="https://www.gstatic.com/firebasejs/4.9.0/firebase-firestore.js"></script>
  <script type="text/javascript">
    var config = {
      apiKey: "AIzaSyCXdOO9r6oBOtlZoahlB7XXRe56EWSuqFQ",
      authDomain: "engspeak-1990a.firebaseapp.com",
      databaseURL: "https://engspeak-1990a.firebaseio.com",
      projectId: "engspeak-1990a",
      storageBucket: "engspeak-1990a.appspot.com",
      messagingSenderId: "1086233938278"
    };

    firebase.initializeApp(config);
    const db = firebase.firestore();

    db.collection("sources").doc("TBBT").update({
      title: "生活大爆炸",
      season_num: <?=$season_num?>,
      episode_mapping: <?=json_encode($episode_mapping)?>,
      <?php
        foreach($scripts as $key => $value){
          $value = json_encode($value);
          echo "{$key}: {$value},\n";
        }
      ?>
    })
    .then(function() {
        console.log("Document successfully written!");
    })
    .catch(function(error) {
        console.error("Error writing document: ", error);
    });
  </script>
  </head>
  <body>
    
  </body>
</html>
<?
*/
