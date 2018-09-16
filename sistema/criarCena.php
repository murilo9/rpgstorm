<?php session_start(); ?>
<?php include_once 'php/_header.php'; ?>
<?php include_once 'php/banner.php'; ?>
<?php include_once 'php/menu.php'; ?>


<?php //Pega as informações básicas do mundo:
    $mundoId = $_GET["mundo"];
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
    <h2>Criar Cena em <?php echo $mundoNome;?></h2>
    <?php echo "<form action='criarCena.php?mundo=$mundoId' method='post' class='formulario'>";?>
        <input type="hidden" name="publish" value="true">
        <input type="hidden" name="cenaId" id="cenaId">
        <input type="hidden" name="acaoId" id="acaoId">
        Nome da Cena: <input type="text" name="inputNomeCena"><br><br>
        <?php   //Verifica se o usuário possui um personagem neste mundo, e exibe a comboBox
            include 'php/_dbconnect.php';
            $sql = "SELECT * FROM tbPersonagens WHERE stDono = '$usuarioEmail' && stMundo = '$mundoId'";
            $query = $con->query($sql);
            if($query->num_rows>0){
                echo "Personagem: <select name='personagem'>";
                while($dados = $query->fetch_array(MYSQLI_ASSOC)){
                    $personagemNome = $dados["stNome"];
                    $personagemId = $dados["stId"];
                    echo "<option value='$personagemId'>$personagemNome</option>";
                }
                echo '</select><br><br>';
            }else{
                echo 'Você precisa ter um personagem neste mundo para criar uma cena.';
            }
            mysqli_close($con);
        ?>
        <textarea name="descricao" cols="50" rows="7">Descrição da cena</textarea><br><br>
        <textarea name="acao" cols='80' rows='20'>Ação inicial</textarea><br><br>
        <input type="submit" value="Publicar">
    </form>
</div>

<script>    //Atribui um id aleatório único à cena:
    document.getElementById("cenaId").value = '_'+Math.floor(Math.random()*9999);
    document.getElementById("acaoId").value = '_'+Math.floor(Math.random()*99999);
</script>

<?php   //Processa a publicação da cena:
    if(isset($_POST["publish"])){
        $cenaNome = $_POST["inputNomeCena"];
        $cenaId = $_POST["cenaId"];
        $acaoId = $_POST["acaoId"];
        $personagemCena = $_POST["personagem"];
        $descricaoCena = $_POST["descricao"];
        $acaoInicial = $_POST["acao"];
        //Validação do formato dos dados:
        if($cenaNome===''){
            echo 'Insira um nome para a cena';
            die();
        }
        if(strlen($cenaNome)>80){
            echo 'O nome da cena pode conter até 80 caracteres';
            die();
        }
        if($descricaoCena===''){
            echo 'Insira uma descrição para a cena';
            die();
        }
        if(strlen($descricaoCena)>200){
            echo 'A descrição pode conter até 200 caracteres';
            die();
        }
        if($acaoInicial===''){
            echo 'Insira uma ação inicial para a cena';
            die();
        }
        //Verifica se o Id gerado pra cena  é único:
        include 'php/_dbconnect.php';
        $sql = "SELECT * FROM tbCenas WHERE stId='$cenaId'";
        $query = $con->query($sql);
        if($query->num_rows>0){
            echo 'Erro interno. Tente novamente.';
            mysqli_close($con);
            die();
        }
        //Registra a cena no BD:
        $sql = "INSERT INTO tbCenas(stId,stMundo,stCreator,stNome) VALUES "
                . "('$cenaId','$mundoId','$personagemCena','$cenaNome')";
        $query = $con->query($sql);
        if(!$query){
            echo "Erro no query (publicar cena): ".mysqli_error($con);
            mysqli_close($con);
            die;
        }
        //Se chegou até aqui, pode criar a pasta da cena no servidor:
        $cenaPath = "mundos/$mundoId/cenas/$cenaId";
        mkdir($cenaPath);
        //Cria a primeira ação da cena:
        $sql = "INSERT INTO tbAcoes VALUES ('$acaoId','$cenaId','$mundoId','$personagemCena')";
        $query = $con->query($sql);
        if(!$query){
            echo "Erro no query (publicar ação): ".mysqli_error($con);
            mysqli_close($con);
            die;
        }
        //Cria o arquivo com a div de ação na pasta da cena:
        $arquivoAberto = fopen($cenaPath."/$acaoId.php", 'w');
        fwrite($arquivoAberto, "<text>$acaoInicial</text>");
        mysqli_close($con);
        //Se chegou até aqui, deu tudo certo então redireciona o usuário:
        header("location: escolhaCena.php?mundo=$mundoId");
    }
?>

<?php include_once 'php/_footer.php'; ?>
