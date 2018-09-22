<?php session_start(); ?>
<?php include_once 'php/_header.php'; ?>
<?php include_once 'php/banner.php'; ?>
<?php include_once 'php/menu.php'; ?>

<div class="conteudo formulario">
    <?php
        
        if(isset($_GET["confirm"])){    //Se já tiver o GET[confirm], executa o procedimento de deleção
            if($_GET["confirm"] === 'true'){
                //"Deleta" o personagem do DB (desatribui o dono):
                include 'php/_dbconnect.php';
                $personagemId = $_SESSION["deletaPersonagemId"];
                $personagemMundoId = $_SESSION["deletaPersonagemMundo"];
                $sql = "UPDATE tbPersonagens SET stDono='none' WHERE stId='$personagemId'";
                $query = $con->query($sql);
                if(!$query){
                    echo 'Erro no query:'.mysqli_error($con);
                    mysqli_close($con);
                    die();
                }
                //OFF: Deixa a pasta do personagem no modo OFF:
                /*$oldPath = "mundos/$personagemMundoId/personagens/$personagemId";
                $newPath = "mundos/$personagemMundoId/personagens/OFF$personagemId";
                rename($oldPath, $newPath);
                mysqli_close($con);*/
                header("location: meusPersonagens.php");
            }
        }else{                  //Se não tiver, espera a confirmação do usuário
            $_SESSION["deletaPersonagemId"] = $_POST["id"];      //Armazena a id em $_SESSION pra pegar depois do refresh
            $_SESSION["deletaPersonagemMundo"] = $_POST["mundo"];    //Armazena o mudno em $_SESSION pra pegar no refresh
            $personagemId = $_POST["id"];
            $personagemMundoId = $_POST["mundo"];
            $usuarioEmail = $_SESSION["usuarioEmail"];
            include 'php/_dbconnect.php';
            $sql = "SELECT * FROM tbPersonagens WHERE stId='$personagemId' && "
                    . "stMundo='$personagemMundoId' && stDono='$usuarioEmail'";
            $query = $con->query($sql);
            while($dados = $query->fetch_array(MYSQLI_ASSOC)){
                $personagemNome = $dados["stNome"];
            }
            echo "Deseja mesmo deletar o(a) personagem $personagemNome?<br>";
        }
    ?>
    <button onclick="window.location='deletaPersonagem.php?confirm=true'">Deletar Personagem</button>
    <button onclick="window.location='meusPersonagens.php'">Cancelar</button>
</div>

<?php include_once 'php/_footer.php'; ?>