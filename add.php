<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Max-Age: 3600');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    return 0;
}

$input = json_decode(file_get_contents('php://input'));
$description = filter_var($input->description, FILTER_SANITIZE_SPECIAL_CHARS);
$amount = filter_var($input->amount, FILTER_SANITIZE_SPECIAL_CHARS);

try {
    $db = new PDO('mysql: host=localhost;dbname=shoppinglist;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $query = $db->prepare('insert into item (description, amount) values (:description, :amount)');
    $query->bindValue(':description', $description, PDO::PARAM_STR);
    $query->bindValue(':amount', $amount, PDO::PARAM_STR);
    $query->execute();
    header('HTTP/1.1 200 OK');
    $data = array('id' => $db->lastInsertId(), 'description' => $description, 'amount' => $amount);
    print json_encode($data);
} catch (PDOException $pdoex) {
    header('HTTP/1.1 500 Internal Server Error');
    $error = array('error' => $pdoex->getMessage());
    print json_encode($error);
}
