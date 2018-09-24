<?php session_start(); ?>
<?php include_once 'php/sessionVerif.php'; 
    $perfilId = $_POST["id"];
    include_once 'php/_dbconnect.php';
    $sql = "SELECT * FROM tbUsuarios WHERE stEmail='$perfilId'";
    $query = $con->query($sql);
    if($query->num_rows>0){
        $user = true;
        while($dados = $query->fetch_array(MYSQLI_ASSOC)){
            $usuarioNome = $dados["stNickname"];
        }
    }else{
        $user = false;
    }
    mysqli_close($con);
?>
<?php include_once 'php/_header.php'; ?>
<?php include_once 'php/banner.php'; ?>
<?php include_once 'php/menu.php'; ?>

<div class="conteudo">
    <?php
        if($user){      //Se o usuário existe, mostra a página:
            echo $usuarioNome;
        }else{      //Se o usuário não existe, exibe mensagem de erro:
            echo 'Usuário inexistente.';
        }
    ?>
</div>

<?php include_once 'php/_footer.php'; ?>