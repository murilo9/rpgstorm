<?php session_start(); ?>
<?php include_once 'php/_header.php'; ?>
<?php include_once 'php/banner.php'; ?>
<?php include_once 'php/menu.php'; ?>

<script>
    function deletarAcao(acaoId, cenaId, mundoId) {
        if(confirm("Deseja mesmo deletar esta ação?")){
            document.getElementById("dfAcao").value = acaoId;
            document.getElementById("dfCena").value = cenaId;
            document.getElementById("dfMundo").value = mundoId;
            document.getElementById("formDeletaAcao").submit();
        }
    }
</script>

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
        $mundoCapa = $dados["stCapa"];
    }
    mysqli_close($con);
?>

<div class="conteudo">
    <?php   //Exibe a descrição da cena e as ações:
        include 'php/_dbconnect.php';
        //Pega informações básicas da cena no BD:
        $sql = "SELECT C.stId AS cId, C.stNome AS cNome, C.stCreator AS cCreator, C.blEstado AS cEstado, "
                    . "C.dtData cData, C.stImagem AS cImagem, P.stNome AS pNome "
                    . "FROM tbCenas C INNER JOIN tbPersonagens P "
                    . "ON C.stCreator = P.stId WHERE C.stMundo='$mundoId' && C.stId='$cenaId'"
                    . "ORDER BY dtData";
        $query = $con->query($sql);
        while($dados = $query->fetch_array(MYSQLI_ASSOC)){
            $cenaId = $dados["cId"];
            $cenaNome = $dados["cNome"];
            $cenaCreator = $dados["pNome"];
            $cenaCreatorId = $dados["cCreator"];
            $cenaEstado = $dados["cEstado"];
            $cenaData = $dados["cData"];
            $cenaImagem = $dados["cImagem"];
            echo "<div class='cenaBox'><div class='cenaImagem'>";
            if($cenaImagem == 'none'){  //Se a cena não tiver imagem, exibe a capa do mundo:
                echo "<img src='mundos/$mundoId/$mundoCapa'>";
            }else{      //Se a cena tiver imagem, mostra:
                echo "<img src='mundos/$mundoId/cenas/$cenaId/$cenaImagem'>";
            }
            echo "</div><div class='cenaDados'>";
            echo "<h3>$cenaNome</h3>$cenaData criada por $cenaCreator<br>";
        }
        //Pega o arquivo com a descrição da cena:
        $cenaDescricao = '';
        $arquivoAberto = fopen("mundos/$mundoId/cenas/$cenaId/descricao.php", 'r');
        while(!feof($arquivoAberto)){
            $cenaDescricao .= fgets($arquivoAberto);
        }
        fclose($arquivoAberto);
        echo "$cenaDescricao</div></div>";      //Fecha a div de título de cena
        //Exibe todas as ações desta cena:
        $sql = "SELECT A.stId AS aId, A.dtData AS aDataHora, P.stNome AS pNome, "
                . "P.stDono AS pDono, P.stFoto AS pFoto, P.stId AS pId "
                . "FROM tbAcoes A INNER JOIN tbPersonagens P "
                . "ON A.stPersonagem = P.stId "
                . "WHERE A.stCena='$cenaId' && A.stMundo='$mundoId'"
                . "ORDER BY A.dtData";
        $query = $con->query($sql);
        while($dados = $query->fetch_array(MYSQLI_ASSOC)){
            $acaoId = $dados["aId"];
            $acaoDataHora = $dados["aDataHora"];
            $personagemId = $dados["pId"];
            $personagemNome = $dados["pNome"];
            $personagemDono = $dados["pDono"];
            $personagemFoto = $dados["pFoto"];
            $acaoTexto = '';
            //Pega o arquivo com o texto da ação:
            $arquivoAberto = fopen("mundos/$mundoId/cenas/$cenaId/$acaoId.php", 'r');
            while(!feof($arquivoAberto)){
                $acaoTexto .= fgets($arquivoAberto);
            }
            fclose($arquivoAberto);
            //Exibe a div de ação:
            echo "<div class='acaoBox'><div class='personagemFoto'>";
            if($personagemFoto == 'none'){
                echo "<img src='img/foto-none.jpg'></div>";
            }else{
                echo "<img src='mundos/$mundoId/personagens/$personagemId/$personagemFoto'></div>";
            }
            echo "<div class='personagemDados'><h3>$personagemNome</h3><h4>$acaoDataHora</h4>";
            echo "$acaoTexto";
            if($personagemDono == $usuarioEmail){
                echo "<br><button onclick='deletarAcao(".'"'.$acaoId.'","'.$cenaId.'","'.$mundoId.'"'.')'."'>Deletar</button>";
                //echo "<br><button onclick='function(){deletarAcao($acaoId, $cenaId, $mundoId)}'>Deletar</button>";
            }
            echo '</div></div>';
        }
        mysqli_close($con);
        //Exibindo form oculta que será submetido ao se deletar uma ação
        echo "<form id='formDeletaAcao' action='cena.php?mundo=$mundoId&id=$cenaId' method='post'>"
            ."<input id='dfAcao' name='acao' type='hidden'>"
            . "<input id='dfCena' name='cena' type='hidden'>"
            . "<input id='dfMundo' name='mundo' type='hidden'>"
            . "<input name='deletar' type='hidden' value='true'></form>";
    ?>
    
    <?php   //Verifica se o usuário possui um personagem para poder postar ações:
        include 'php/_dbconnect.php';
        $sql = "SELECT * FROM tbPersonagens WHERE stDono='$usuarioEmail' && stMundo='$mundoId'";
        $query = $con->query($sql);
        if($query->num_rows>0){     //Se o usuário tiver personagens, exibe o form com a lista
            echo "<form method='post'><input name='postar' type='hidden' value='true'>"
            . "<input id='inputId' name='inputId' type='hidden'>"
            . "Postar ação com: <select name='personagem'>";
            while($dados = $query->fetch_array(MYSQLI_ASSOC)){  //Exibe as options
                $personagemNome = $dados["stNome"];
                $personagemId = $dados["stId"];
                echo "<option value='$personagemId'>$personagemNome</option>";
            }
            echo "</select><br><textarea name='inputAcao' cols='70' rows='10'></textarea>"
            . "<br><input type='submit' value='Postar'></form>";
        }else{      //Se o usuário não tiver personagens, não exibe o form:
            echo 'Voce precisar ter ao menos um personagem neste mundo para postar ações.';
        }
        mysqli_close($con);
    ?>
</div>


<script>
    //Gera um id aleatório único pra ação:
    document.getElementById("inputId").value = '_'+Math.floor(Math.random()*99999);
</script>

<?php   
    //Processa a postagem de ações:
    if(isset($_POST["postar"])){
        $acaoId = $_POST["inputId"];
        $personagemId = $_POST["personagem"];
        $acaoTexto = $_POST["inputAcao"];
        if($acaoTexto === ''){
            echo 'Digite algo para postar a ação.';
            die();
        }
        //Verifica se o id da ação é único no BD:
        include 'php/_dbconnect.php';
        $sql = "SELECT stId FROM tbAcoes WHERE stId='$acaoId'";
        $query = $con->query($sql);
        if($query->num_rows>0){     //Se o id já existir, avisa o erro:
            echo 'Erro interno. Tente novamente.';
            mysqli_close($con);
            die();
        }
        //Insere a ação no BD:
        $sql = "INSERT INTO tbAcoes(stId, stCena, stMundo, stPersonagem) "
                . "VALUES ('$acaoId','$cenaId','$mundoId','$personagemId')";
        $query = $con->query($sql);
        if(!$query){
            echo "Erro no query(inserir ação): ".mysqli_error($con);
            mysqli_close($con);
            die();
        }
        //Cria a notificação pro dono da cena (a menos que o próprio dono tenha postado):
        $sql = "SELECT U.stEmail AS uId FROM tbPersonagens P INNER JOIN tbUsuarios U "
                . "ON P.stDono = U.stEmail WHERE P.stId='$cenaCreatorId'";  //Pega o id do dono da cena
        $query = $con->query($sql);
        while($dados = $query->fetch_array(MYSQLI_ASSOC)){
            $donoCena = $dados["uId"];
        }
        if($donoCena != $usuarioEmail){     //Verifica se quem postou não é dono da cena
            $sql = "INSERT INTO tbNotifs(stUsuario, stTipo, stLink, stConteudo) "
                    . "VALUES ('$donoCena','ML','cena.php?id=$cenaId&mundo=$mundoId',"
                    . "'$personagemNome postou uma ação na sua cena $cenaNome em $mundoNome.')";    //Insere a notif no BD
            $query = $con->query($sql);
            if(!$query){
                echo "Erro no query(deletar ação): ". mysqli_error($con);
                mysqli_close($con);
                die();
            }
        }
        mysqli_close($con);
        //Cria o arquivo com o texto da ação:
        $arquivoAberto = fopen("mundos/$mundoId/cenas/$cenaId/$acaoId.php", 'w');
        fwrite($arquivoAberto, $acaoTexto);
        fclose($arquivoAberto);
        header("location: cena.php?mundo=$mundoId&id=$cenaId");
    }
    //Processa a deleção de ações:
    if(isset($_POST["deletar"])){
        $acaoId = $_POST["acao"];
        $cenaId = $_POST["cena"];
        $mundoId = $_POST["mundo"];
        include 'php/_dbconnect.php';
        $sql = "DELETE FROM tbAcoes WHERE stId='$acaoId' && stCena='$cenaId' && stMundo='$mundoId'";
        $query = $con->query($sql);
        if(!$query){
            echo "Erro no query(deletar ação): ". mysqli_error($con);
            mysqli_close($con);
            die();
        }
        //Se chegou até aqui, então deu tudo certo e pode deletar o arquivo com o texto da ação:
        if(!unlink("mundos/$mundoId/cenas/$cenaId/$acaoId.php")){
            echo "Erro ao deletar arquivo com texto da ação: "
            . "mundos/$mundoId/cenas/$cenaId/$acaoId$acaoId.php não encontrado.<br>"
                    . "A deleção do arquivo agora deve ser feita manualmente no servidor.";
            die();
        }
        header("location: cena.php?mundo=$mundoId&id=$cenaId");
        mysqli_close($con);
    }
?>

<?php include_once 'php/_footer.php'; ?>