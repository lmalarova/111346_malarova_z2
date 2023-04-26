<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require_once('config.php');

$db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$query = "SELECT * FROM html ORDER BY id desc LIMIT 3";
$stmt = $db->query($query);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
$dates = array();

foreach($results as $result) {
  ////////////// FIIT FOOD ////////////////////
  if($result['name'] === "fiitfood") {

    $name1 = 'FIIT Food';
    $query = "SELECT * from meal WHERE place = \"$name1\";";
    $stmt = $db->query($query);
    $meals = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if(!empty($meals)) {
      $stmt = $db->prepare('DELETE FROM meal WHERE place = :name;');
      $stmt->bindParam(':name', $name);
      $stmt->execute();
      break;
    }

    $pageHtml = $result['html'];
    $pageId = $result['id'];

    $dom = new DOMDocument();
    $dom->loadHTML($pageHtml);

    $names = array();
    $prices = array();
    $dates = array();

    $freefoodHtml = $dom->getElementById("fiit-food");
    // get divs
    $divs = $freefoodHtml->getElementsByTagName("div");
    // remove second div cause its 'Stala ponuka'
    $divs[1]->parentNode->removeChild($divs[1]);

    // get all uls
    $uls = $freefoodHtml->getElementsByTagName("ul");
    // get all spans
    $spans = $freefoodHtml->getElementsByTagName("span");

    foreach ($uls as $ul) {
      // from uls get those with class day-offer
      if($ul->getAttribute('class') == "day-offer") {
        // in uls with class day-offer get lis
        $lis = $ul->getElementsByTagName('li');
        foreach($lis as $li) {
          // push to array just those nodes who doesnt have childNode
          foreach ($li->childNodes as $node) {
              // If the child node is a text node, append its value to the array
              if ($node->nodeType === XML_TEXT_NODE) {
                  $names[] = $node->nodeValue;
              }
          }
        }
      };
    }

    $dates = array();
    foreach ($spans as $li) {
      // get prices by finding class brand price in spans 
      if($li->getAttribute('class') == "brand price") {
        array_push($prices, $li->textContent);
      };
      if($li->getAttribute('class') == "day-title") {
        array_push($GLOBALS['dates'], $li->textContent);
      };
    }

    $count_meal = 0;
    $day = 1;
    $place = "FIIT Food";

    for($i=0; $i<sizeof($names); $i++){
      $stmt = $db->prepare('SELECT COUNT(*) FROM meal WHERE name = :name AND day = :day AND place = :place');
      $stmt->bindParam(':name', $names[$i]);
      $stmt->bindParam(':day', $day);
      $stmt->bindParam(':place', $place);
      // $stmt->bindParam(':date', $dates_final[$i]);
      $stmt->execute();
      $count = $stmt->fetchColumn();

      if($count === 0) {
        $sql = "INSERT INTO meal (name, price, place, day, html_id) VALUES (?,?,?,?,?)";
        $stmt = $db->prepare($sql);
        $success = $stmt->execute([$names[$i], $prices[$i], $place, $day, $pageId]);
      }

      $count_meal++;
      if($count_meal == 4) {
        $day++;
        $count_meal = 0;
      }
    }
  }

  ////////////// Eat and Meet ////////////////////
  if($result['name'] === "eat&meet") {

    $name2 = 'Eat and Meet';
    $query = "SELECT * from meal WHERE place = \"$name2\";";
    $stmt = $db->query($query);
    $meals = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if(!empty($meals)) {
      $stmt = $db->prepare('DELETE FROM meal WHERE place = :name;');
      $stmt->bindParam(':name', $name);
      $stmt->execute();
      break;
    }

    $pageHtml = $result['html'];
    $pageId = $result['id'];

    $dom = new DOMDocument();
    $dom->loadHTML($pageHtml);

    for($j=1; $j <= 7; $j++){
      $id = "day-". $j;
      $parsedHtml = $dom->getElementById($id);
      $names = $parsedHtml->getElementsByTagName('p');
      // $prices = $parsedHtml->getElementsByTagName('span');
      $images = $parsedHtml->getElementsByTagName('img');

      $xpath = new DOMXPath($dom);
      $prices = $xpath->query('//div[starts-with(@id, "' . $id . '")]//span[@class="price"]');

      $place = "Eat and Meet";
      for($i = 0; $i < sizeof($names); $i++) {
        $stmt = $db->prepare('SELECT COUNT(*) FROM meal WHERE name = :name AND day = :day AND place = :place');
        $stmt->bindParam(':name', $names[$i]->nodeValue);
        $stmt->bindParam(':day', $j);
        $stmt->bindParam(':place', $place);
        // $stmt->bindParam(':date', $dates_final[$i]);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        if($count === 0) {
          $sql = "INSERT INTO meal (name, price, place, day, picture, html_id) VALUES (?,?,?,?,?,?)";
          $stmt = $db->prepare($sql);
          $success = $stmt->execute([$names[$i]->nodeValue, $prices[$i]->nodeValue, $place, $j, $images[$i]->getAttribute('src'), $pageId]);
        }
      }
    }
  }

  ////////////// VENZA ////////////////////
  if($result['name'] === "venza") {

    $name3 = 'Venza';
    $query = "SELECT * from meal WHERE place = \"$name3\";";
    $stmt = $db->query($query);
    $meals = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if(!empty($meals)) {
      $stmt = $db->prepare('DELETE FROM meal WHERE place = :name;');
      $stmt->bindParam(':name', $name);
      $stmt->execute();
      break;
    }

    $pageHtml = $result['html'];
    $pageId = $result['id'];

    $dom = new DOMDocument();
    $dom->loadHTML($pageHtml);

    for($j=1; $j <= 7; $j++){
      $id = "day_". $j;
      $parsedHtml = $dom->getElementById($id);
      $parsed = $parsedHtml->getElementsByTagName('h5');
      $names = array();
      $prices = array();

      $unwanted = ["Polievka", "Múčne", "Vege", "Šalát", "Jedlo týždňa", "Menu 1", "Menu 2", "Menu 3", "Menu 4"];
      foreach ($parsed as $item) {
        if (in_array($item->nodeValue, $unwanted)) {
          $item->parentNode->removeChild($item);
        }
      }

      for($i=0; $i<sizeof($parsed); $i++){
        if($i%2 === 0) {
          array_push($names, $parsed[$i]->nodeValue);
        }else{
          array_push($prices, $parsed[$i]->nodeValue);
        }
      }

      $place = "Venza";
      for($i = 0; $i < sizeof($names); $i++) {
        $stmt = $db->prepare('SELECT COUNT(*) FROM meal WHERE name = :name AND day = :day AND place = :place');
        $stmt->bindParam(':name', $names[$i]);
        $stmt->bindParam(':day', $j);
        $stmt->bindParam(':place', $place);
        // $stmt->bindParam(':date', $dates_final[$i]);

        $stmt->execute();
        $count = $stmt->fetchColumn();

        if($count === 0) {
          $sql = "INSERT INTO meal (name, price, place, day, html_id) VALUES (?,?,?,?,?)";
          $stmt = $db->prepare($sql);
          $success = $stmt->execute([$names[$i], $prices[$i], "Venza", $j, $pageId]);
        }
      }
    }
  }
}

header("Location: verification.php");

?>
