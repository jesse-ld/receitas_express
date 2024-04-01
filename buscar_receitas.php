<?php
$servername = "localhost";
$username = "root";
$password = "usbw";
$dbname = "receitas_express";

// Recuperar os ingredientes selecionados no formulário
$ingredientes_selecionados = isset($_POST['ingredientes']) ? $_POST['ingredientes'] : array();

// Conectar ao banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

// Mapeamento entre o nome da receita e o destino do link
$destinos = array(
    "Coxinha" => "produto1.html",
    "Lasanha" => "produto3.html",
    "Carne ao Molho Madeira" => "produto2.html",
    // Adicione outros destinos conforme necessário
);

// Consulta SQL para obter as receitas que contêm os ingredientes selecionados
$sql = "SELECT r.id AS id_receita, r.nome AS nome_receita
        FROM Receitas r
        INNER JOIN Receita_Ingredientes ri ON r.id = ri.receita_id
        INNER JOIN Ingredientes i ON ri.ingrediente_id = i.id
        WHERE i.nome IN ('" . implode("','", $ingredientes_selecionados) . "')
        GROUP BY r.id, r.nome";

$result = $conn->query($sql);

if ($result) {
    if ($result->num_rows > 0) {
        // Exibir as receitas encontradas
        echo "<h2>Receitas que contêm os ingredientes selecionados:</h2>";
        while ($row = $result->fetch_assoc()) {
            $nome_receita = $row["nome_receita"];
            $id_receita = $row["id_receita"];

            // Verificar se a receita está mapeada para um destino
            if (array_key_exists($nome_receita, $destinos)) {
                // Adicionar link dinâmico
                echo "<p><a href='" . $destinos[$nome_receita] . "'>$nome_receita</a></p>";
            } else {
                echo "<p>$nome_receita (Link não disponível)</p>";
            }
        }
    } else {
        echo "Nenhuma receita encontrada com os ingredientes selecionados.";
    }
} else {
    echo "Erro na consulta SQL: " . $conn->error;
}

$conn->close();
?>
