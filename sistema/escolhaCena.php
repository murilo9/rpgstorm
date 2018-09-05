<?php session_start(); ?>
<?php include_once 'php/_header.php'; ?>
<?php include_once 'php/banner.php'; ?>
<?php include_once 'php/menu.php'; ?>

<?php   //Pega as informações básicas do mundo e vê se o usuário está neste mundo:
    $mundoId = $_GET["mundo"];
    $usuarioEmail = $_SESSION["usuarioEmail"];
    include 'php/_dbconnect.php';
    $sql = "SELECT stUsuario FROM tbMundoUsuarios WHERE stUsuario = '$usuarioEmail' && stMundo = '$mundoId'";
    $query = $con->query($sql);
    if($query->num_rows>0){     //Caso o usuário esteja neste mundo, deixa ele entrar:
        $mayEnter = true;
    }else{                      //Caso contrário, não:
        $mayEnter = false;
    }
    //Pega as informações do mundo que estão no DB:
    $sql = "SELECT * FROM tbMundos WHERE stId = '$mundoId'";
    $query = $con->query($sql);
    while($dados = $query->fetch_array(MYSQLI_ASSOC)){
        $mundoNome = $dados["stNome"];
        $mundoCreator = $dados["stCreator"];
        if($dados["blPublic"]){
            $mundoTipo = 'público';
        }else{
            $mundoTipo = 'privado';
        }
        $capaArquivo = $dados["stCapa"];
        //Cria div oculta com nome da imagem de capa, pra ser 'lida' pelo html:
        echo "<div id='nomeCapa' style='display: none;'>$capaArquivo</div>";
    }
    $sql = "SELECT stNickname FROM tbUsuarios WHERE stEmail = '$mundoCreator'";
    $query = $con->query($sql);
    if(!$query){
        die("Erro no query: ". mysqli_error($con));
    }
    while($dados = $query->fetch_array(MYSQLI_ASSOC)){
        $mundoCreatorNome = $dados["stNickname"];
    }    
    mysqli_close($con);
    //Pega a descrição info.php:
    $arquivoAberto = fopen("mundos/$mundoId/info.php", 'r');
    $mundoDescricao = '';
    while(!feof($arquivoAberto)){
        $mundoDescricao .= fgets($arquivoAberto);
    }
    fclose($arquivoAberto);
?>

<div class="conteudo">
    <?php
        if(!$mayEnter){     //Se o usuário não puder entrar, exibe apenas as informações:
            echo "<h2>$mundoNome</h2>Mundo $mundoTipo<br>Criado por $mundoCreatorNome<br><br>"
                    . "<img class='mundoCapa' src='mundos/$mundoId/$capaArquivo'><br><br>$mundoDescricao<br><br>";
            //--TODO exibir lista de staffs
            //--TODO exibir modelo de ficha
        }else{              //Se o usuário puder entrar, exibe os personagens e cenas:
            include 'php/_dbconnect.php';
            echo "<h2>$mundoNome</h2>Mundo $mundoTipo<br>Criado por $mundoCreatorNome<br><br>";
            //--TODO exibir lista de staffs
            //--TODO botão para exibir/ocultar modelo de ficha do mundo
            echo "<a href='criarPersonagem.php?mundo=$mundoId'>Criar Personagem</a><br>";
            //Pega lista de personagens que o usuário possui neste mundo:
            $sql = "SELECT * FROM tbPersonagens WHERE stMundo='$mundoId' && stDono='$usuarioEmail'";
            $query = $con->query($sql);
            if($query->num_rows>0){
                $personagensList = 'Personagens seus neste mundo:';
                while($dados = $query->fetch_array(MYSQLI_ASSOC)){
                    $personagensList .= '<br>'.$dados["stNome"];
                }
                echo $personagensList;
            }else{
                echo 'Você não possui nenhum personagem neste mundo.';
            }
            echo "<br><h3 style='display: inline-block;'>Cenas</h3>"
               . "<a href='criarCena.php?mundo=$mundoId' style='float: right;'>Criar Cena</a><br>";
            $sql = "SELECT * FROM tbCenas WHERE stMundo='$mundoId' ORDER BY dtData";
            $query = $con->query($sql);
            if($query->num_rows>0){
                while($dados = $query->fetch_array(MYSQLI_ASSOC)){
                    $cenaNome = $dados["stNome"];
                    $cenaCreator = $dados["stCreator"];
                    $cenaEstado = $dados["blEstado"];
                    $cenaData = $dados["dtData"];
                    $cenaImagem = $dados["stImagem"];
                    //TODO verificar se a cena possui imagem
                    echo "<div class='cenaBox'><h3>$cenaNome</h3>$cenaData<br>Criada por $cenaCreator</div>";
                }
            }else{
                echo 'Não há cenas neste mundo.';
            }
            mysqli_close($con);
        }
    ?>
</div>

<?php include_once 'php/_footer.php'; ?>