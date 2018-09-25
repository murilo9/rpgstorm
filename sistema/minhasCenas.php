<?php session_start(); ?>
<?php include_once 'php/sessionVerif.php'; ?>
<?php include_once 'php/_header.php'; ?>
<?php include_once 'php/banner.php'; ?>
<?php include_once 'php/menu.php'; ?>

<div class="conteudo">
    <h1>Minhas Cenas</h1>
    <?php
        $usuarioEmail = $_SESSION["usuarioEmail"];
        include 'php/_dbconnect.php';
        $sql = "SELECT C.stNome AS cNome, C.stMundo AS cMundoId, C.stId AS cId, "
                . "C.dtData AS cData, M.stNome AS mNome, P.stNome AS pNome "
                . "FROM tbCenas C INNER JOIN tbMundos M INNER JOIN tbPersonagens P "
                . "ON C.stMundo = M.stId && C.stCreator = P.stId "
                . "WHERE P.stDono='$usuarioEmail' ORDER BY C.dtData";
        $query = $con->query($sql);
        if(!$query){
            echo "Erro no query: ". mysqli_error($con);
            mysqli_close($con);
            die();
        }
        if($query->num_rows>0){
            while($dados = $query->fetch_array(MYSQLI_ASSOC)){
                $cenaId = $dados["cId"];
                $cenaMundoId = $dados["cMundoId"];
                $cenaNome = $dados["cNome"];
                $cenaMundo = $dados["mNome"];
                $cenaData = $dados["cData"];
                $cenaPersonagem = $dados["pNome"];
                echo "<div class='cenaBox'><a href='cena.php?mundo=$cenaMundoId&id=$cenaId'>"
                        . "<h2>$cenaNome</h2></a>"
                . "<h3>Em <a href='escolhaCena.php?mundo=$cenaMundoId'>$cenaMundo</a>"
                        . " criada em $cenaData por $cenaPersonagem</h3></div>";
            }
        }else{
            echo 'Você não possui cenas em mundo algum.';
        }
        mysqli_close($con);
    ?>
</div>

<?php include_once 'php/_footer.php'; ?>