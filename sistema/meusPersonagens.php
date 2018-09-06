<?php session_start(); ?>
<?php include_once 'php/_header.php'; ?>
<?php include_once 'php/banner.php'; ?>
<?php include_once 'php/menu.php'; ?>

<div class="conteudo">
    <?php
        $usuarioEmail = $_SESSION["usuarioEmail"];
        $personagensList = '<h3>Persongens que você possui:</h3>';
        include 'php/_dbconnect.php';
        $sql = "SELECT M.stNome AS mNome, P.stNome AS pNome FROM tbPersonagens P INNER JOIN tbMundos M ON P.stMundo = M.stId "
                . "WHERE stDono='$usuarioEmail'";
        $query = $con->query($sql);
        if($query->num_rows>0){
            while($dados = $query->fetch_array(MYSQLI_ASSOC)){
                $personagemNome = $dados["pNome"];
                $personagemMundo = $dados["mNome"];
                $personagensList .= "$personagemNome no mundo $personagemMundo<br>";
            }
            echo $personagensList;
        }else{
            echo 'Você não possui personagens em mundo algum.';
        }
        mysqli_close($con);
    ?>
</div>

<?php include_once 'php/_footer.php'; ?>