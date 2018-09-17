<?php session_start(); ?>
<?php include_once 'php/_header.php'; ?>
<?php include_once 'php/banner.php'; ?>
<?php include_once 'php/menu.php'; ?>

<?php //Pega as informações básicas do mundo:
    $mundoId = $_GET["mundo"];
    $cenaId = $_GET["id"];
    $usuarioEmail = $_SESSION["usuarioEmail"];
    include 'php/_dbconnect.php';
    $sql = "SELECT stUsuario FROM tbMundoUsuarios WHERE stUsuario = '$usuarioEmail' && stMundo = '$mundoId'";
    $query = $con->query($sql);
    if($query->num_rows==0){     //Caso o usuário não esteja neste mundo, redireciona:
        header("location: erroMundo.php");
        mysqli_close($con);
        die();
    }
    //Pega o nome do mundo:
    $sql = "SELECT * FROM tbMundos WHERE stId = '$mundoId'";
    $query = $con->query($sql);
    while($dados = $query->fetch_array(MYSQLI_ASSOC)){
        $mundoNome = $dados["stNome"];
    }
    mysqli_close($con);
?>

<div class="conteudo">
    <?php   //Exibe a descrição da cena e as ações:
        include 'php/_dbconnect.php';
        //Pega informações básicas da cena no BD:
        $sql = "SELECT C.stId AS cId, C.stNome AS cNome, C.stCreator, C.blEstado AS cEstado, "
                    . "C.dtData cData, C.stImagem AS cImagem, P.stNome AS pNome "
                    . "FROM tbCenas C INNER JOIN tbPersonagens P "
                    . "ON C.stCreator = P.stId WHERE C.stMundo='$mundoId' && C.stId='$cenaId'"
                    . "ORDER BY dtData";
        $query = $con->query($sql);
        while($dados = $query->fetch_array(MYSQLI_ASSOC)){
            $cenaId = $dados["cId"];
            $cenaNome = $dados["cNome"];
            $cenaCreator = $dados["pNome"];
            $cenaEstado = $dados["cEstado"];
            $cenaData = $dados["cData"];
            $cenaImagem = $dados["cImagem"];
            //TODO verificar se a cena possui imagem
            echo "<div class='cenaBox'><h3>$cenaNome</h3>$cenaData criada por $cenaCreator<br>";
        }
        //Pega o arquivo com a descrição da cena:
        $cenaDescricao = '';
        $arquivoAberto = fopen("mundos/$mundoId/cenas/$cenaId/descricao.php", 'r');
        while(!feof($arquivoAberto)){
            $cenaDescricao .= fgets($arquivoAberto);
        }
        fclose($arquivoAberto);
        echo "$cenaDescricao</div>";      //Fecha a div de título de cena
        //Exibe todas as ações desta cena:
        $sql = "SELECT A.stId AS aId, A.dtData AS aDataHora, P.stNome AS pNome "
                . "FROM tbAcoes A INNER JOIN tbPersonagens P "
                . "ON A.stPersonagem = P.stId "
                . "WHERE A.stCena='$cenaId' && A.stMundo='$mundoId'"
                . "ORDER BY A.dtData";
        $query = $con->query($sql);
        while($dados = $query->fetch_array(MYSQLI_ASSOC)){
            $acaoId = $dados["aId"];
            $acaoDataHora = $dados["aDataHora"];
            $personagemNome = $dados["pNome"];
            $acaoTexto = '';
            //Pega o arquivo com o texto da ação:
            $arquivoAberto = fopen("mundos/$mundoId/cenas/$cenaId/$acaoId.php", 'r');
            while(!feof($arquivoAberto)){
                $acaoTexto .= fgets($arquivoAberto);
            }
            fclose($arquivoAberto);
            //Exibe a div de ação:
            echo "<div class='acaoBox'><h3>$personagemNome</h3><h4>$acaoDataHora</h4>"
                    . "$acaoTexto</div>";
        }
        mysqli_close($con);
    ?>
</div>

<?php include_once 'php/_footer.php'; ?>