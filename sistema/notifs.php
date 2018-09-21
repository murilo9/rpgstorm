<?php session_start(); ?>
<?php include_once 'php/_header.php'; ?>
<?php include_once 'php/banner.php'; ?>
<?php include_once 'php/menu.php'; ?>

<div class="conteudo">
    <h2>Notificações</h2>
    <?php
        $usuarioEmail = $_SESSION["usuarioEmail"];
        include 'php/_dbconnect.php';
        $sql = "SELECT * FROM tbNotifs WHERE stUsuario='$usuarioEmail'";
        $query = $con->query($sql);
        if($query->num_rows>0){
            while($dados = $query->fetch_array(MYSQLI_ASSOC)){
                $notifId = $dados["stId"];
                $notifConteudo = $dados["stConteudo"];
                $notifLink = $dados["stLink"];
                $notifData = $dados["dtData"];
                $notifTipo = $dados["stTipo"];
                switch($notifTipo){
                    case 'SM':      //Exibe notificação com pergunta:
                        echo "<div class='notifBox'>"
                            . "$notifConteudo<br><form method='post'>"
                            . "<input name='resposta' type='hidden' value='true'>"
                            . "<input name='notifId' type='hidden' value='$notifId'>"
                            . "<input type='submit' value='Aceitar'></form>"
                            . "<form method='post'>"
                            . "<input name='resposta' type='hidden' value='false'>"
                            . "<input name='notifId' type='hidden' value='$notifId'>"
                            . "<input type='submit' value='Rejeitar'></form><br>"
                            . "$notifData</div>";
                        break;
                    default:
                        echo 'no type';
                        break;
                }
                echo "<div class='notifBox></div>'";
            }
        }else{
            echo 'Não há notificações recentes.';
        }
        mysqli_close($con);
    ?>
</div>

<?php   //Processa o resultado das forms das notificações:
    if(isset($_POST["resposta"])){
        $resposta = $_POST["resposta"];
        $notifId = $_POST["notifId"];
        echo $resposta;
        include 'php/_dbconnect.php';
        //Pega o etc1(usuarioEmail) e etc2(mundoId) da tupla de notificação:
        $sql = "SELECT etc1, etc2 FROM tbNotifs WHERE stId=$notifId";
        $query = $con->query($sql);
        while($dados = $query->fetch_array(MYSQLI_ASSOC)){
            $notifUsuario = $dados["etc1"];
            $notifMundo = $dados["etc2"];
        }
        if($resposta == 'true'){      //Aceita o usuário no mundo, registra no BD:
            
            //Atualzia a tabela MundoUsuarios (deixa status=1 e aproved by usuário atual):
            $sql = "UPDATE tbMundoUsuarios SET blStatus=true, stAprovedBy='$usuarioEmail' "
                    . "WHERE stMundo='$notifMundo' && stUsuario='$notifUsuario'";
            $query = $con->query($sql);
            if(!$query){
                echo 'Erro no query(atualizar tbMundoUsuarios): '.mysqli_error($con);
                mysqli_close($con);
                die();
            }
            echo "Update MundoUsuarios pra true, aproved by $usuarioEmail";
        }else{      //Rejeita o usuário no mundo, elimina do BD:
            $sql = "DELETE FROM tbMundoUsuarios WHERE stMundo='$notifMundo' && stUsuario='$notifUsuario'";
            $query = $con->query($sql);
            if(!$query){
                echo 'Erro no query(atualizar tbMundoUsuarios): '.mysqli_error($con);
                mysqli_close($con);
                die();
            }
            echo "Delete tbMundoUsuarios pra mundo=$notifMundo && usuario=$notifUsuario";
        }
        //Apaga a notificação:
        $sql = "DELETE FROM tbNotifs WHERE stId=$notifId";
        $query = $con->query($sql);
        mysqli_close($con);
        header("location: notifs.php");
    }
?>

<?php include_once 'php/_footer.php'; ?>