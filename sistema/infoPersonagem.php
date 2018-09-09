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
        //Verifica se possui foto:
        include 'php/_dbconnect.php';
        $sql = "SELECT stFoto FROM tbPersonagens WHERE stId = '$personagemId'";
        $query = $con->query($sql);
        while($dados = $query->fetch_array(MYSQLI_ASSOC)){
            $personagemFoto = $dados["stFoto"];
        }
        mysqli_close($con);
        if($personagemFoto === 'none'){
            echo "<img src='img/foto-none.jpg' width='40%'></img>";
        }else{
            echo "<img src='mundos/$personagemMundoId/personagens/$personagemId/$personagemFoto' width='30%'></img>";
        }
        echo "<form action='php/mudaFoto.php?id=$personagemId' method='post' enctype='multipart/form-data'>"
        . "<input name='arquivo' type='file' size='20'><input name='atualiza' type='hidden' value='true'>"
                . "<input type='submit' value='Atualizar Retrato'></form>";
        //Exibe todos os dados:
        echo "<h2>$personagemNome</h2>Mundo: $personagemMundo<br>"
                . "<a href='deletaPersonagem.php?id=$personagemId&mundo=$personagemMundoId'>Deletar Personagem</a>"
                . "<br><br><h3>Ficha<h3>";
        echo "<div class='formulario'>$personagemFicha</div>";
    ?>
</div>

<?php include_once 'php/_footer.php'; ?>