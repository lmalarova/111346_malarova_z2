<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('config.php');

$db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$ch1 = curl_init();

curl_setopt($ch1, CURLOPT_URL, 'http://eatandmeet.sk/tyzdenne-menu');
curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);

$server_response1 = curl_exec($ch1);

curl_close($ch1);

$ch2 = curl_init();

curl_setopt($ch2, CURLOPT_URL, 'https://www.novavenza.sk/tyzdenne-menu');
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);

$server_response2 = curl_exec($ch2);

curl_close($ch2);

$ch3 = curl_init();

curl_setopt($ch3, CURLOPT_URL, 'http://www.freefood.sk/menu/#fiit-food');
curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);

$server_response3 = curl_exec($ch3);

curl_close($ch3);

$sql = "INSERT INTO html (name, created_at, html) VALUES (?,?,?), (?,?,?), (?,?,?)";
$stmt = $db->prepare($sql);
$success = $stmt->execute(["fiitfood", date("Y.m.d"), $server_response3, "venza", date("Y.m.d"), $server_response2, "eat&meet", date("Y.m.d"), $server_response1]);

header("Location: verification.php");
?>
