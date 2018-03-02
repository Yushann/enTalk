<?php

  define('AVERAGE', 0.7);

  $file_info = new finfo(FILEINFO_MIME);
  $files = array_slice(scandir('.'), 2);
  $scripts = Array();

  foreach($files as $file){
    $mime_type = $file_info->buffer(file_get_contents($file));
    if($mime_type == 'text/plain; charset=utf-8') {

      if(!preg_match('/S([0-9]+)E([0-9]+)/', $file, $filename_matches)){
        echo "fail {$file}<br>";
        return;
      }
      
      echo "parsing {$file}<br>";
      $result = parseFile($file);
      $scripts[$filename_matches[1]][$filename_matches[2]] = $result;
    }
  }

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
    while(list($p, $w) = each($words)){
      $p = trim($p);
      $data["total_charactors"][$p] = $w;
      $newMapping[$i][] = $p;
      $total += $w;
      if($total > $average/AVERAGE) {
        $newCount[$i] = $total;
        $total = 0;
        $i++;
      }
    }

    $data["recommend_num"] = count($newMapping);
    while(list($k, $p) = each($newMapping)) {
      $data["recommend_group"][join(', ',$p)] = "";
    }

    echo "{$file} ".count($data["recommend_group"])."<br>";

    return $data;
  }
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
        script: <?=json_encode($scripts)?>,
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
