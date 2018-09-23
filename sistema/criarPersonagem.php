<?php session_start(); ?>
<?php 
    include_once 'php/sessionVerif.php';
    //Pega as informações básicas do mundo:
    $mundoId = $_GET["mundo"];
    $usuarioEmail = $_SESSION["usuarioEmail"];
    include 'php/_dbconnect.php';
    $sql = "SELECT stUsuario FROM tbMundoUsuarios WHERE stUsuario = '$usuarioEmail' && stMundo = '$mundoId'";
    $query = $con->query($sql);
    if($query->num_rows==0){     //Caso o usuário não esteja neste mundo, redireciona:
        header("location: erroMundo.php");
        mysqli_close($con);
        die();
    }
    //Pega o nome do mundo:
    $sql = "SELECT * FROM tbMundos WHERE stId = '$mundoId'";
    $query = $con->query($sql);
    while($dados = $query->fetch_array(MYSQLI_ASSOC)){
        $mundoNome = $dados["stNome"];
    }
    mysqli_close($con);
    //Cria div oculta com nome do mundo (pra ser pego pelo javascript):
    echo "<div id='mundoId' style='display: none;'>$mundoId</div>";
    //Cria div oculta com nome de usuário (pra ser pego pelo javascript):
    echo "<div id='usuarioEmail' style='display: none;'>$usuarioEmail</div>";
?>
<?php include_once 'php/_header.php'; ?>
<?php include_once 'php/banner.php'; ?>
<?php include_once 'php/menu.php'; ?>

<script src="js/funcoesCriarPersonagem.js"></script>

<div class="conteudo">
    <h2>Criar Personagem em <?php echo $mundoNome;?></h2>
    <?php   //pega modelo de ficha
        $arquivoAberto = fopen("mundos/$mundoId/ficha.php", 'r');
        $modeloFicha = '';
        while(!feof($arquivoAberto)){
            $modeloFicha .= fgets($arquivoAberto);
        }
        fclose($arquivoAberto);
        //Exibe a ficha para o usuário preencher:
        echo "<div class='formulario' id='modeloFicha'>$modeloFicha</div>";
    ?>
    <button type="button" onclick="criarPersonagem()">Criar</button>
</div>

<?php include_once 'php/_footer.php'; ?>