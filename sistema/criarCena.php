<?php session_start(); ?>
<?php include_once 'php/_header.php'; ?>
<?php include_once 'php/banner.php'; ?>
<?php include_once 'php/menu.php'; ?>

<?php //Pega as informações básicas do mundo:
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
?>
<div class="conteudo">
    <h2>Criar Cena em <?php echo $mundoNome;?></h2>
    <form action="criarCena.php" metho="post" class="formulario">
        Nome da Cena: <input type="text" name="inputNomeCena"><br><br>
        <?php
            include 'php/_dbconnect.php';
            $sql = "SELECT * FROM tbPersonagens WHERE stDono = '$usuarioEmail' && stMundo = '$mundoId'";
            $query = $con->query($sql);
            if($query->num_rows>0){
                echo "<select name='personagem'>";
                while($dados = $query->fetch_array(MYSQLI_ASSOC)){
                    $personagemNome = $dados["stNome"];
                    $personagemId = $dados["stId"];
                    echo "<option value='$personagemId'>$personagemNome</option>";
                }
                echo '</select>';
            }else{
                echo 'Você precisa ter um personagem neste mundo para criar uma cena.';
            }
            mysqli_close($con);
        ?>
        
    </form>
</div>

<?php include_once 'php/_footer.php'; ?>
