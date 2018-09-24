<?php session_start(); ?>
<?php include_once 'php/sessionVerif.php'; ?>
<?php include_once 'php/_header.php'; ?>
<?php include_once 'php/banner.php'; ?>
<?php include_once 'php/menu.php'; ?>

<div class="conteudo">
    <?php
        $usuarioEmail = $_SESSION["usuarioEmail"];
        $personagensList = '<h3>Persongens que você possui:</h3>';
        include 'php/_dbconnect.php';
        $sql = "SELECT M.stNome AS mNome, P.stNome AS pNome, P.stId AS pId, P.stFoto AS pFoto, P.stMundo AS pMundoId "
                . "FROM tbPersonagens P INNER JOIN tbMundos M "
                . "ON P.stMundo = M.stId WHERE stDono='$usuarioEmail'";
        $query = $con->query($sql);
        if($query->num_rows>0){
            while($dados = $query->fetch_array(MYSQLI_ASSOC)){
                $personagemNome = $dados["pNome"];
                $personagemMundo = $dados["mNome"];
                $personagemId = $dados["pId"];
                $personagemFoto = $dados["pFoto"];
                $personagemMundoId = $dados["pMundoId"];
                if($personagemFoto === 'none'){     //Se o personagem não tiver foto, usa a default
                    $img = 'img/foto-none.jpg';
                }else{          //Caso contrário, usa a que está no BD
                    $img = "mundos/$personagemMundoId/personagens/$personagemId/$personagemFoto";
                }
                $personagensList .= "<div class='personagemBox'>"
                        . "<img src='$img' width='80%'></img><br>"
                        . "<a href='infoPersonagem.php?id=$personagemId&mundo=$personagemMundoId'>$personagemNome</a><br>"
                        . "Mundo: $personagemMundo<br></div>";
            }
            echo $personagensList;
        }else{
            echo 'Você não possui personagens em mundo algum.';
        }
        mysqli_close($con);
    ?>
</div>

<?php include_once 'php/_footer.php'; ?>