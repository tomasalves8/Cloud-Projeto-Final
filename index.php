<?php
$db_server_ip = getenv('DB_SERVER_IP');
$conn = new mysqli($db_server_ip, "root", "");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "CREATE DATABASE IF NOT EXISTS projeto";
if ($conn->query($sql) === False) {
    echo "Erro a criar base de dados: " . $conn->error;
}

$sql = "CREATE TABLE IF NOT EXISTS projeto.motas (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    marca VARCHAR(30) NOT NULL,
    modelo VARCHAR(30) NOT NULL,
    preco DECIMAL(10, 2) NOT NULL,
    ano INT(4) NOT NULL,
    cilindrada INT(4) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === False) {
    echo "Erro a criar tabela: " . $conn->error;
}

$conn->select_db("projeto");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "POST";
    if (isset($_POST['edit_id'])) {
        $id = $_POST['edit_id'];
        $marca = $conn->real_escape_string($_POST['edit_marca']);
        $modelo = $conn->real_escape_string($_POST['edit_modelo']);
        $preco = $conn->real_escape_string($_POST['edit_preco']);
        $ano = $conn->real_escape_string($_POST['edit_ano']);
        $cilindrada = $conn->real_escape_string($_POST['edit_cilindrada']);

        $sql = "UPDATE motas SET marca='$marca', modelo='$modelo', preco=$preco, ano=$ano, cilindrada=$cilindrada WHERE id=$id";
        if ($conn->query($sql) === False) {
            echo "Erro ao atualizar: " . $conn->error;
        }
        header('Location: index.php');
    } else if (isset($_GET['delete_id'])) {
        $id = $_GET['delete_id'];
        $sql = "DELETE FROM motas WHERE id = $id";
        if ($conn->query($sql) === TRUE) {
            echo "Mota apagada com sucesso";
        } else {
            echo "Erro ao apagar mota: " . $conn->error;
        }
        header('Location: index.php');
    } else {
        $marca = $_POST['marca'];
        $modelo = $_POST['modelo'];
        $preco = $_POST['preco'];
        $ano = $_POST['ano'];
        $cilindrada = $_POST['cilindrada'];
        if ($marca !== '' && $modelo !== '' && $preco !== '' && $ano !== '' && $cilindrada !== '') {
            $marca = $conn->real_escape_string($marca);
            $modelo = $conn->real_escape_string($modelo);
            $preco = $conn->real_escape_string($preco);
            $ano = $conn->real_escape_string($ano);
            $cilindrada = $conn->real_escape_string($cilindrada);
            $sql = "INSERT INTO motas (marca, modelo, preco, ano, cilindrada) VALUES ('$marca', '$modelo', $preco, $ano, $cilindrada)";
            if ($conn->query($sql) === False) {
                echo "Erro ao Adicionar linha: " . $conn->error;
            }
            header('Location: index.php');
        }
    }
}
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql = "DELETE FROM motas WHERE id = $delete_id";
    if ($conn->query($sql) === TRUE) {
        echo "Mota apagada com sucesso";
    } else {
        echo "Erro ao apagar mota: " . $conn->error;
    }
    header('Location: index.php');
}

if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $sql = "SELECT * FROM motas WHERE id=$edit_id";
    $result = $conn->query($sql);
    $edit_mota = $result->fetch_assoc();
} else {
    $edit_mota = null;
}

if (isset($_GET['adicionar_mota'])) {
    $adicionar_mota = $_GET['adicionar_mota'];
} else {
    $adicionar_mota = null;
}

$sql = "SELECT * FROM motas";
$result = $conn->query($sql);
$motas = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $motas[] = $row;
    }
}
$conn->close();
?>
<html>

<head>
    <title>Projeto - Tomás Alves</title>
</head>

<body>
    <h2>Lista de Motas</h2>
    <table border="1">
        <tr>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Preço</th>
            <th>Ano</th>
            <th>Cilindrada</th>
            <th>Data de Colocação</th>
            <th>Ações</th>
        </tr>
        <?php foreach ($motas as $mota) : ?>
            <tr>
                <td><?php echo $mota['marca']; ?></td>
                <td><?php echo $mota['modelo']; ?></td>
                <td><?php echo $mota['preco']; ?></td>
                <td><?php echo $mota['ano']; ?></td>
                <td><?php echo $mota['cilindrada']; ?></td>
                <td><?php echo $mota['created_at']; ?></td>
                <td>
                    <a href="?edit_id=<?php echo $mota['id']; ?>">Editar</a>
                    <a href="?delete_id=<?php echo $mota['id']; ?>">Apagar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <form action="" method="get">
        <br>
        <button type="submit" name="adicionar_mota" value="true">Adicionar Mota</button>
    </form>
    <h2><?php if ($edit_mota) : ?>Editar Mota<?php elseif ($adicionar_mota) : ?>Adicionar Mota<?php endif; ?></h2>
    <form action="" method="post">
        <?php if ($edit_mota) : ?>
            <input type="hidden" name="edit_id" value="<?php echo $edit_mota['id']; ?>">
            <label for="edit_marca">Marca:</label>
            <input type="text" id="edit_marca" name="edit_marca" value="<?php echo $edit_mota['marca']; ?>">
            <br>
            <label for="edit_modelo">Modelo:</label>
            <input type="text" id="edit_modelo" name="edit_modelo" value="<?php echo $edit_mota['modelo']; ?>">
            <br>
            <label for="edit_preco">Preço:</label>
            <input type="number" id="edit_preco" name="edit_preco" value="<?php echo $edit_mota['preco']; ?>">
            <br>
            <label for="edit_ano">Ano:</label>
            <input type="number" id="edit_ano" name="edit_ano" value="<?php echo $edit_mota['ano']; ?>">
            <br>
            <label for="edit_cilindrada">Cilindrada:</label>
            <input type="number" id="edit_cilindrada" name="edit_cilindrada" value="<?php echo $edit_mota['cilindrada']; ?>">
            <br>
            <br><br>
            <input type="submit" value="Atualizar">
            <input type="submit" value="Cancelar" formaction="index.php">
        <?php elseif ($adicionar_mota) : ?>
            <label for="marca">Marca:</label>
            <input type="text" id="marca" name="marca">
            <br>
            <label for="modelo">Modelo:</label>
            <input type="text" id="modelo" name="modelo">
            <br>
            <label for="preco">Preço:</label>
            <input type="number" id="preco" name="preco">
            <br>
            <label for="ano">Ano:</label>
            <input type="number" id="ano" name="ano">
            <br>
            <label for="cilindrada">Cilindrada:</label>
            <input type="number" id="cilindrada" name="cilindrada">
            <br><br>
            <input type="submit" value="Adicionar">
            <input type="submit" value="Cancelar" formaction="index.php">
        <?php endif; ?>
    </form>
</body>

</html>