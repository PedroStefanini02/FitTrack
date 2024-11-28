<?php

$localhost = "localhost";
$user = "root";
$passw = "";
$banco = "FitTrack";

try {
    $pdo = new PDO("mysql:dbname=".$banco.";host=".$localhost, $user, $passw);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    $stmt = $pdo->prepare("SELECT * FROM USERS WHERE email = :email AND password = :password");
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);

 
    $email = 'test@example.com';
    $password = 'password123';

    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

 
    print_r($results);

} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}

?>
