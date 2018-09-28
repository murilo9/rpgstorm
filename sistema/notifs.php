<?php session_start(); ?>
<?php include_once 'php/sessionVerif.php'; ?>
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
                    case 'SM':      //Exibe notificação de entrar no mundo com a pergunta:
                        echo "<div class='notifBox'>"
                            . "$notifConteudo<br><form method='post'>"
                            . "<input name='resposta' type='hidden' value='true'>"
                            . "<input name='tipo' type='hidden' value='SM'>"
                            . "<input name='notifId' type='hidden' value='$notifId'>"
                            . "<input type='submit' value='Aceitar'></form>"
                            . "<form method='post'>"
                            . "<input name='resposta' type='hidden' value='false'>"
                            . "<input name='tipo' type='hidden' value='SM'>"
                            . "<input name='notifId' type='hidden' value='$notifId'>"
                            . "<input type='submit' value='Rejeitar'></form><br>"
                            . "$notifData</div>";
                        break;
                    case 'ML':  //Exibe o divDelete[x] e o conteúdo:
                        echo "<div class='notifDelete'>"
                            . "<form method='post'><input name='id' type='hidden' value='$notifId'>"
                            . "<input type='hidden' name='notifDelete' value='true'>"
                            . "<input type='submit' value='X'></form></div>"
                            . "<div class='notifBorder'><a href='$notifLink' class='notif'>"
                        . "<div class='notifBox'>$notifConteudo<br>$notifData</div></a></div><br>";
                        break;
                    case 'SS':  //Exibe notificação de staff com a pergunta:
                        echo "<div class='notifBox'>$notifConteudo<br><form method='post'>"
                            . "<input name='resposta' type='hidden' value='true'>"
                            . "<input name='tipo' type='hidden' value='SS'>"
                            . "<input name='notifId' type='hidden' value='$notifId'>"
                            . "<input type='submit' value='Aceitar'></form>"
                            . "<form method='post'>"
                            . "<input name='resposta' type='hidden' value='false'>"
                            . "<input name='tipo' type='hidden' value='SS'>"
                            . "<input name='notifId' type='hidden' value='$notifId'>"
                            . "<input type='submit' value='Rejeitar'></form><br>"
                            . "$notifData</div>";
                        break;
                    default:
                        echo 'no type';
                        break;
                }
            }
        }else{
            echo 'Não há notificações recentes.';
        }
        mysqli_close($con);
    ?>
</div>

<?php   //Processa o resultado das forms das notificações:
    if(isset($_POST["resposta"])){      //Se for pra processar resposta de SM:
        $resposta = $_POST["resposta"];
        $tipo = $_POST["tipo"];
        $notifId = $_POST["notifId"];
        include 'php/_dbconnect.php';
        
        if($tipo == 'SM'){      //Se for uma notificação de entrar no mundo:
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
                //Gera notificação para o usuário que foi aceito:
                $sql = "INSERT INTO tbNotifs(stUsuario, stTipo, stLink, stConteudo) "
                        . "VALUES ('$notifUsuario','ML','escolhaCena.php?mundo=$notifMundo',"
                        . "'Sua solicitação para entrar no mundo $notifMundo foi aceita.')";
                $query = $con->query($sql);
                if(!$query){
                    echo 'Erro no query(criar notificação de resposta): '.mysqli_error($con);
                    mysqli_close($con);
                    die();
                }
            }else{      //Rejeita o usuário no mundo, elimina do BD:
                $sql = "DELETE FROM tbMundoUsuarios WHERE stMundo='$notifMundo' && stUsuario='$notifUsuario'";
                $query = $con->query($sql);
                if(!$query){
                    echo 'Erro no query(atualizar tbMundoUsuarios): '.mysqli_error($con);
                    mysqli_close($con);
                    die();
                }
            }
        }
        if($tipo == 'SS'){      //Se for uma notificação de staff:
            $sql = "SELECT N.etc1 AS nEtc, M.stNome AS mNome "
                    . "FROM tbNotifs N INNER JOIN tbMundos M "
                    . "ON N.etc1 = M.stId "
                    . "WHERE N.stId=$notifId";
            $query = $con->query($sql);
            if(!$query){
                echo 'Erro no query(pegar dados da notif): '.mysqli_error($con);
                mysqli_close($con);
                die();
            }
            while($dados = $query->fetch_array(MYSQLI_ASSOC)){
                $notifMundo = $dados["nEtc"];
                $notifMundoNome = $dados["mNome"];
            }
            if($resposta == 'true'){    //Registra usuario como staff do mundo no BD:
                $sql = "INSERT INTO tbStaffs VALUES ('$usuarioEmail','$notifMundo')";
                $query = $con->query($sql);
                if(!$query){
                    echo 'Erro no query(inserir em tbStaffs): '.mysqli_error($con);
                    mysqli_close($con);
                    die();
                }
                //Cria notificação avisando que o usuário agora é staff:
                $sql = "INSERT INTO tbNotifs(stUsuario,stTipo,stLink,stConteudo) "
                        . "VALUES ('$usuarioEmail','ML','escolhaCena.php?mundo=$notifMundo',"
                        . "'Você agora é staff do mundo $notifMundoNome')";
                $query = $con->query($sql);
                if(!$query){
                    echo 'Erro no query(inserir em tbNotifs): '.mysqli_error($con);
                    mysqli_close($con);
                    die();
                }
            }
        }
        //Apaga a notificação:
        $sql = "DELETE FROM tbNotifs WHERE stId=$notifId";
        $query = $con->query($sql);
        mysqli_close($con);
        header("location: notifs.php");
    }
    else if(isset ($_POST["notifDelete"])){    //Se for pra deletar notificação:
        $notifId = $_POST["id"];
        include 'php/_dbconnect.php';
        $sql = "DELETE FROM tbNotifs WHERE stId='$notifId'";
        $query = $con->query($sql);
        if(!$query){
            echo 'Erro no query(deletar notificação): '.mysqli_error($con);
            mysqli_close($con);
            die();
        }else{
            header("location: notifs.php");
        }
        mysqli_close($con);
    }
?>

<?php include_once 'php/_footer.php'; ?>