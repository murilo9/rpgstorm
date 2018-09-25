<?php session_start(); ?>
<?php include_once 'php/sessionVerif.php'; ?>
<?php include_once 'php/_header.php'; ?>
<?php include_once 'php/banner.php'; ?>
<?php include_once 'php/menu.php'; ?>

<div class="conteudo formulario">
    <?php
        if(isset($_GET["confirm"])){   //Se já tiver o GET[confirm], executa o procedimento de deleção
            if($_GET["confirm"] === 'true'){
                $cenaMundoId = $_SESSION["deletaCenaMundo"];
                $cenaId = $_SESSION["deletaCenaId"];
                //Deleta todas as ações desta cena no BD:
                include 'php/_dbconnect.php';
                $sql = "DELETE FROM tbAcoes WHERE stCena='$cenaId' && stMundo='$cenaMundoId'";
                $query = $con->query($sql);
                if(!$query){
                    echo 'Erro no query (deletar ações):'.mysqli_error($con);
                    mysqli_close($con);
                    die();
                }
                //Deleta os participantes da cena do BD:
                $sql = "DELETE FROM tbCenasUsuarios WHERE stId='$cenaId' && stMundo='$mundoId'";
                $query = $con->query($sql);
                if(!$query){
                    echo 'Erro no query (deletar participantes da cena):'.mysqli_error($con);
                    mysqli_close($con);
                    die();
                }
                //Deleta a cena do BD:
                $sql = "DELETE FROM tbCenas WHERE stId='$cenaId' && stMundo='$cenaMundoId'";
                $query = $con->query($sql);
                if(!$query){
                    echo 'Erro no query (deletar cena):'.mysqli_error($con);
                    mysqli_close($con);
                    die();
                }
                //Deixa a pasta da cena em modo OFF:
                $oldPath = "mundos/$cenaMundoId/cenas/$cenaId";
                $newPath = "mundos/$cenaMundoId/cenas/OFF$cenaId";
                rename($oldPath, $newPath);
                mysqli_close($con);
                header("location: escolhaCena.php?mundo=$cenaMundoId");
            }
        }else{      //Se não tiver o GET[confirm], espera a confirmação do usuário
            $_SESSION["deletaCenaId"] = $_POST["id"];      //Armazena a id em $_SESSION pra pegar depois do refresh
            $_SESSION["deletaCenaMundo"] = $_POST["mundo"];    //Armazena o mundo em $_SESSION pra pegar no refresh
            $cenaId = $_POST["id"];
            $cenaMundoId = $_POST["mundo"];
            $usuarioEmail = $_SESSION["usuarioEmail"];
            include 'php/_dbconnect.php';
            $sql = "SELECT * FROM tbCenas WHERE stId='$cenaId' && "
                    . "stMundo='$cenaMundoId'";
            $query = $con->query($sql);
            while($dados = $query->fetch_array(MYSQLI_ASSOC)){
                $cenaNome = $dados["stNome"];
            }
            echo "Deseja mesmo deletar a cena $cenaNome deste mundo?<br>";
        }
    ?>
    <button onclick="window.location='deletaCena.php?confirm=true'">Deletar Cena</button>
    <button onclick="window.location='selecionaMundo.php'">Cancelar</button>
</div>

<?php include_once 'php/_footer.php'; ?>