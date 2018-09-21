<?php session_start(); //Inicia a session
    //Pega os dados necessários para esta página:
    $usuarioEmail = $_SESSION["usuarioEmail"];
?>
<?php include_once 'php/_header.php'; ?>
<?php include_once 'php/banner.php'; ?>
<?php include_once 'php/menu.php'; ?>


<div class="formulario" style="text-align: center;">
    <br><h2>Deletar Mundo</h2><br>
    <?php
        if(isset($_GET["confirm"])){        //Se já tiver o GET[confirm], executa o procedimento de deleção
            if($_GET["confirm"] === 'true'){
                $mundoId = $_SESSION["deletaMundoId"];
                include_once 'php/_dbconnect.php';
                //Delta as ações:
                $sql = "DELETE FROM tbAcoes WHERE stMundo = '$mundoId'";
                $query = $con->query($sql);
                if(!$query){
                    echo "Erro no query(deletar ações): " . mysqli_error($con);
                    mysqli_close($con);
                    die();
                }
                //Deleta as cenas:
                $sql = "DELETE FROM tbCenas WHERE stMundo = '$mundoId'";
                $query = $con->query($sql);
                if(!$query){
                    echo "Erro no query(deletar cenas): " . mysqli_error($con);
                    mysqli_close($con);
                    die();
                }
                //Deleta os usuários:
                $sql = "DELETE FROM tbMundoUsuarios WHERE stMundo = '$mundoId'";
                $query = $con->query($sql);
                if(!$query){
                    echo "Erro no query(deletar usuários): " . mysqli_error($con);
                    mysqli_close($con);
                    die();
                }
                //Deleta os personagens:
                $sql = "DELETE FROM tbPersonagens WHERE stMundo = '$mundoId'";
                $query = $con->query($sql);
                if(!$query){
                    echo "Erro no query(deletar personagens): " . mysqli_error($con);
                    mysqli_close($con);
                    die();
                }
                //Deleta os staffs:
                $sql = "DELETE FROM tbStaffs WHERE stMundo = '$mundoId'";
                $query = $con->query($sql);
                if(!$query){
                    echo "Erro no query (deletar staffs): " . mysqli_error($con);
                    mysqli_close($con);
                    die();
                }
                //Deleta o mundo:
                $sql = "DELETE FROM tbMundos WHERE stId = '$mundoId'";
                $query = $con->query($sql);
                if(!$query){
                    echo "Erro no query (deletar mundo): " . mysqli_error($con);
                    mysqli_close($con);
                    die();
                }
                //Desativa a pasta do mundo no servidor:
                rename("mundos/$mundoId", "mundos/OFF$mundoId");
                //Fim do procedimento, redireciona o usuário:
                unset($_SESSION["deletaMundoId"]);      //Retira a Id do mundo da session
                mysqli_close($con);
                header("location: meusMundos.php");
            }
        }else{                              //Se não tiver, espera a confirmação do usuário
            $mundoId = $_POST["inputId"];
            $_SESSION["deletaMundoId"] = $mundoId;  //Guarda a Id do mundo na session, pra ser pego na hora de deletar
            include_once 'php/_dbconnect.php';
            $sql = "SELECT * FROM tbMundos WHERE stId = '$mundoId'";
            $query = $con->query($sql);
            while($dados = $query->fetch_array(MYSQLI_ASSOC)){
                $mundoNome = $dados["stNome"];
                if($dados["blPublic"]){
                    $mundoTipo = 'público';
                }else{
                    $mundoTipo = 'privado';
                }
            }
            mysqli_close($con);
            echo "Tem certeza que deseja deletar o mundo $mundoTipo $mundoNome?<br>"
                    . "Isso removerá todas as cenas e personagens para sempre.<br><br>";
        }
    ?>
    <button onclick="window.location = 'deletaMundo.php?confirm=true'">Deletar Mundo</button>
    <button onclick="window.location = 'meusMundos.php'">Cancelar</button>
</div>

<?php
    
?>

<?php include_once 'php/_footer.php'; ?>
