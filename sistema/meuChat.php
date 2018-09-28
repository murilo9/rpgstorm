<?php session_start(); ?>
<?php include_once 'php/sessionVerif.php'; 
    $usuarioEmail = $_SESSION["usuarioEmail"];
?>
<?php include_once 'php/_header.php'; ?>
<?php include_once 'php/banner.php'; ?>
<?php include_once 'php/menu.php'; ?>

<div class="conteudo">
    <h1>Minhas Conversas</h1>
    <?php
        include 'php/_dbconnect.php';
        //Verifica se há conversas para este usuário:
        $sql = "SELECT C.stUser1 AS cUser1, C.stUser2 AS cUser2, U.stNickname AS uNome "
                . "FROM tbChat C INNER JOIN tbUsuarios U "
                . "ON (C.stUser1 = U.stEmail && C.stUser1 != '$usuarioEmail') || "
                . "(C.stUser2 = U.stEmail && C.stUser2 != '$usuarioEmail') "
                . "WHERE stUser1='$usuarioEmail' || stUser2='$usuarioEmail'";
        $query = $con->query($sql);
        if(!$query){
            echo 'Erro no query(verificar se há chats): '.mysqli_error($con);
            mysqli_close($con);
            die();
        }
        if($query->num_rows==0)     //Se não há conversas, exibe o aviso:
            echo '<h3>Não há conversas de chat entre você e outros usuários.</h3>';
        else{       //Se há conversas, exibe um link pro perfil do usuário:
            echo '<h3>Conversas</h3>';
            while($dados = $query->fetch_array(MYSQLI_ASSOC)){
                $userNome = $dados["uNome"];
                if($dados["cUser1"] == $usuarioEmail)
                    $userId = $dados["cUser2"];
                else if($dados["cUser2"] == $usuarioEmail)
                    $userId = $dados["cUser1"];
                echo "<form action='perfil.php' method='post'>"
                . "<input name='id' type='hidden' value='$userId'>"
                . "<input type='submit' value='$userNome'></form>";
            }
        }
        mysqli_close($con);
    ?>
</div>

<?php include_once 'php/_footer.php'; ?>