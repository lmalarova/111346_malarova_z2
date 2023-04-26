<?php

  header('Content-Type: application/json');
  header('Access-Control-Allow-Origin: *');

  require_once('../config.php');

  try {
      $db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
  }
  catch(PDOException $e){
      ECHO $e->getMessage();
  }

  switch($_SERVER['REQUEST_METHOD']) {
      case 'GET':
          $day = $_GET['day'];
          $id = $_GET['id'];
          read_meals($db, $day, $id);
          break;
      case 'POST':
          $data = json_decode(file_get_contents('php://input'), true);
          create_meals($db, $data);
          break;
      case 'PUT':
          $data = file_get_contents('php://input');
          // Parse the data into an array
          $data = json_decode($data, true);
          update_meals($db, $_GET['id'], $data);
          break;
      case 'DELETE':
          $name = $_GET['name'];
          delete_meals($db, $name);
          break;
  }

  function read_meals($db, $day, $id)
  {
    if($id) {
      $stmt = $db->query("SELECT * from meal WHERE id=$id");
      $meals = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else if ($day) {
      $stmt = $db->query("SELECT * from meal WHERE day=$day");
      $meals = $stmt->fetchAll(PDO::FETCH_ASSOC);
      if(!isEmpty($meals)) {
        echo json_encode(array('error' => 'Not Found'));
        http_response_code(404);
        return;
      }
    } else {
      $stmt = $db->query('SELECT * from meal');
      $meals = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    echo json_encode($meals);
  }

  function create_meals($db, $data) {
    $stmt = $db->prepare('INSERT INTO meal (name, price, place, day) VALUES (:name, :price, :place, :day);');
    $stmt->bindParam(':name', $data['name']);
    $stmt->bindParam(':price', $data['price']);
    $stmt->bindParam(':place', $data['place']);
    $stmt->bindParam(':day', $data['day']);

    if(!isset($data['name']) || !is_string($data['name'])) {
      echo json_encode(array('error' => 'Bad Request'));
      http_response_code(400);
      return;
    }

    if(!isset($data['price']) || !is_string($data['price'])) {
      echo json_encode(array('error' => 'Bad Request'));
      http_response_code(400);
      return;
    }

    if(!isset($data['place']) || !is_string($data['place'])) {
      echo json_encode(array('error' => 'Bad Request'));
      http_response_code(400);
      return;
    }

    if(!isset($data['day']) || !is_string($data['day'])) {
      echo json_encode(array('error' => 'Bad Request'));
      http_response_code(400);
      return;
    }

    $stmt->execute();
    echo json_encode(array('success' => 'Data created successfully'));
  }

  function update_meals($db, $id, $data)
  {
      $stmt = $db->query("SELECT * from meal WHERE id=$id");
      $meals = $stmt->fetchAll(PDO::FETCH_ASSOC);

      if(!isEmpty($meals)) {
        echo json_encode(array('error' => 'Not Found'));
        http_response_code(404);
        return;
      }
      $stmt = $db->prepare('UPDATE meal SET price = :price WHERE id = :id;');
      $stmt->bindParam(':id', $id);
      $stmt->bindParam(':price', $data['price']);
      $stmt->execute();
      echo json_encode(array('success' => 'Data updated successfully'));
  }

  function delete_meals($db, $name) {
      $stmt = $db->query("SELECT * from meal WHERE place=\"$name\"");
      var_dump($stmt);
      $meals = $stmt->fetchAll(PDO::FETCH_ASSOC);

      if(!isEmpty($meals)) {
        echo json_encode(array('error' => 'Not Found'));
        http_response_code(404);
        return;
      }

      if(!isEmpty($name)) {
          echo json_encode(array('error' => 'Bad Request'));
          http_response_code(400);
          return;
      } else {
          $stmt = $db->prepare('DELETE FROM meal WHERE place = :name');
          $stmt->bindParam(':name', $name);
          $stmt->execute();
          echo json_encode(array('success' => 'Data deleted successfully'));
      }
  }

  function isEmpty($param) {

      if(empty($param)) {
          $isOk = false;
      } else {
          $isOk = true;
      }

      return $isOk;
  }
?>
