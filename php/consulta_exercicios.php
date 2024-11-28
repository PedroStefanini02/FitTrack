<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "FitTrack";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Obter exercícios por categoria
$categorias = ['Peito', 'Costas', 'Pernas', 'Ombros', 'Bíceps', 'Tríceps', 'Cardio', 'Abdômen', 'Antebraço', 'Glúteos'];

$exerciciosPorGrupo = [];
foreach ($categorias as $categoria) {
    $sql = "SELECT nome FROM exercicios WHERE categoria = '$categoria' ORDER BY RAND() LIMIT 4";
    $result = $conn->query($sql);
    $exercicios = [];
    while ($row = $result->fetch_assoc()) {
        $exercicios[] = $row['nome'];
    }
    $exerciciosPorGrupo[$categoria] = $exercicios;
}

$conn->close();
header('Content-Type: application/json');
echo json_encode($exerciciosPorGrupo);
