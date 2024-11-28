<?php
$localhost = "localhost";
$user = "root";
$passw = "";
$banco = "FitTrack";

// Criar conexão
$conn = new mysqli($localhost, $user, $passw, $banco);

// Verificar conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Obter filtros e busca
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Consulta SQL
$sql = "SELECT id, name, price, market, address, image_path FROM foods WHERE name LIKE '%$search%'";
if ($filter == 'low') {
    $sql .= " ORDER BY price ASC";
}

$result = $conn->query($sql);

$products = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Adicionar caminho completo da imagem ou definir placeholder
        $row['image_path'] = !empty($row['image_path']) ? 'php/' . $row['image_path'] : '';
        $products[] = $row;
    }
}

echo json_encode($products);

$conn->close();
?>
