<?php session_start(); ?>
<?php include_once 'php/_header.php'; ?>
<?php include_once 'php/banner.php'; ?>
<?php include_once 'php/menu.php'; ?>

<div class="conteudo">
    <?php
        $personagemId = $_GET["id"];
        include 'php/_dbconnect.php';
        $usuarioEmail = $_SESSION["usuarioEmail"];
        $sql = "SELECT stId FROM tbPersonagens WHERE stId='$personagemId' && stDono = '$usuarioEmail'";
        $query = $con->query($sql);
        if($query->num_rows==0){
            mysqli_close($con);
            die('Erro: este personagem não é seu.');
        }
        //Se chegou até aqui, pode pegar as informações do personagem no BD:
        $sql = "SELECT P.stNome AS pNome, P.stDono AS pDono, P.stMundo AS pMundoId, M.stNome AS mNome "
                . "FROM tbPersonagens P INNER JOIN tbMundos M "
                . "ON P.stMundo = M.stId "
                . "WHERE P.stId='$personagemId'";
        $query = $con->query($sql);
        while($dados = $query->fetch_array(MYSQLI_ASSOC)){
            $personagemNome = $dados["pNome"];
            $personagemDono = $dados["pDono"];
            $personagemMundo = $dados["mNome"];
            $personagemMundoId = $dados["pMundoId"];
        }
        mysqli_close($con);
        //Pega o arquivo de ficha:
        $arquivoAberto = fopen("mundos/$personagemMundoId/personagens/$personagemId/ficha.php", 'r');
        if(!$arquivoAberto){
            die('Erro no fopen.');
        }
        $personagemFicha = '';
        while(!feof($arquivoAberto)){
            $personagemFicha .= fgets($arquivoAberto);
        }
        fclose($arquivoAberto);
        //Exibe todos os dados:
        echo "<h2>$personagemNome</h2>Mundo: $personagemMundo<br><br><h3>Ficha<h3>";
        echo "<div class='formulario'>$personagemFicha</div>";
    ?>
</div>

<?php include_once 'php/_footer.php'; ?>