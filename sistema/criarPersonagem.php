<?php session_start(); ?>
<?php 
    include_once 'php/sessionVerif.php';
    //Pega as informações básicas do mundo:
    if(isset($_GET["mundo"])){
        $mundoId = $_GET["mundo"];
    }else if(isset($_POST["mundoId"])){
        $mundoId = $_POST["mundoId"];
    }
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
    //Cria div oculta com nome do mundo (pra ser pego pelo javascript):
    echo "<div id='mundoId' style='display: none;'>$mundoId</div>";
    //Cria div oculta com nome de usuário (pra ser pego pelo javascript):
    echo "<div id='usuarioEmail' style='display: none;'>$usuarioEmail</div>";
?>

<?php include_once 'php/_header.php'; ?>
<?php include_once 'php/banner.php'; ?>
<?php include_once 'php/menu.php'; ?>

<script>
    function criarPersonagem(){     //Verifica se o personagem possui um nome antes de enviar o Ajax request
        var randomId = '_'+Math.floor(Math.random()*99999);   //Cria ID aleatório pro personagem
        document.getElementById("inputId").value = randomId;    //Atribui o random ID
        document.getElementById("ficha").submit();      //Dá submit o form
    }
</script>

<div class="conteudo">
    <h2>Criar Personagem em <?php echo $mundoNome;?></h2>
    <?php
        //Exibe a ficha para o usuário preencher:
        echo "<form class='formulario' id='ficha' action='criarPersonagem.php?mundo=$mundoId' method='post' "
                . "enctype='multipart/form-data'>"
                . "Foto: <input name='arquivo' size='20' type='file'><br>"
                . "Nome: <input name='inputNome' type='text'><br>"
                . "Gênero: <input name='inputGenero' type='text'><br>"
                . "Raça: <input name='inputRaca' type='text'><br>"
                . "Idade: <input name='inputIdade' type='text' size='4'><br>"
                . "Descrição<br><textarea name='inputDescricao' cols='50' rows='15'></textarea><br>"
                . "Habilidades <br><textarea name='inputHabilidades' cols='50' rows='15'></textarea>"
                . "<br><input name='inputId' id='inputId' type='hidden'>"
                . "<input name='mundoId' type='hidden' value='$mundoId'>"
                . "<input name='ownerId' type='hidden' value='$usuarioEmail'>"
                . "<input name='cadastrar' type='hidden' value='true'></form><br>";
    ?>
    <button onclick="criarPersonagem()">Criar</button>
</div>

<?php   //Processa o a instanciação do personagem:
    if(isset($_POST["cadastrar"])){
        $personagemNome = $_POST["inputNome"];
        $personagemGenero = $_POST["inputGenero"];
        $personagemRaca = $_POST["inputRaca"];
        $personagemIdade = $_POST["inputIdade"];
        $personagemDescricao = $_POST["inputDescricao"];
        $personagemHabilidades =$_POST["inputHabilidades"];
        $personagemId = $_POST["inputId"];
        $mundoId = $_POST["mundoId"];
        $usuarioEmail = $_POST["ownerId"];
        if(empty($_FILES["arquivo"]["name"])){
            $usarArquivo = false;
            $uploadFileName = 'none';
        }else{
            $usarArquivo = true;
            $uploadFileName = $_FILES["arquivo"]["name"];
            $uploadFile = $pastaPersonagem.$uploadFileName;
        }
        //validação do nome no BD:
        include 'php/_dbconnect.php';
        $sql = "SELECT stNome FROM tbPersonagens WHERE stNome = '$personagemNome' || stId = '$personagemId'";
        $query = $con->query($sql);
        if($query->num_rows>0){     //Se um personagem com este nome existir, retorna name_exists
            echo 'Este nome de personagem não está disponível neste mundo.';
            mysqli_close($con);
            die();
        }
        //Validação dos demais campos:
        if($personagemNome=='' || $personagemGenero=='' || $personagemIdade=='' || $personagemRaca==''
                || $personagemDescricao=='' || $personagemHabilidades==''){
            echo 'Preencha todos os campos.';
            die();
        }
        //Cria a pasta no servidor:
        $pastaPersonagem = "mundos/$mundoId/personagens/$personagemId/";
        mkdir($pastaPersonagem);
        //Faz upload da foto, caso tenha:
        if($usarArquivo){
            if(!move_uploaded_file($_FILES["arquivo"]["tmp_name"], $uploadFile)){
                    echo 'Erro ao fazer upload do arquivo';
                    die();
                }
        }
        //Cria arquivo com a div de ficha:
        $arquivoAberto = fopen("$pastaPersonagem/ficha.php", 'w');
        $dados = "<div id='nome'>$personagemNome</div><div id='genero'>$personagemGenero</div>"
                . "<div id='idade'>$personagemIdade</div><div id='raca'>$personagemRaca</div>"
                . "<div id='nome'>$personagemNome</div><div id='genero'>$personagemGenero</div>"
                . "<div id='descricao'>$personagemDescricao</div><div id='habilidades'>$personagemHabilidades</div>";
        fwrite($arquivoAberto, $dados);
        fclose($arquivoAberto);
        //Registra o personagem no DB:
        $sql = "INSERT INTO tbPersonagens VALUES ('$personagemId','$mundoId','$usuarioEmail',"
                . "'$personagemNome', '$uploadFileName')";
        $query = $con->query($sql);
        if(!$query){
            echo "Erro no query:".mysqli_error($con);
        }else{
            header("location: meusPersonagens.php");
        }
        mysqli_close($con);
    }
?>

<?php include_once 'php/_footer.php'; ?>