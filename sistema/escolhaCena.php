<?php session_start(); ?>
<?php   
    include_once 'php/sessionVerif.php';
    //Pega as informações básicas do mundo e vê se o usuário está neste mundo:
    $mundoId = $_GET["mundo"];
    $usuarioEmail = $_SESSION["usuarioEmail"];
    $usuarioNome = $_SESSION["usuarioNickname"];
    include 'php/_dbconnect.php';
    $sql = "SELECT stUsuario, blStatus FROM tbMundoUsuarios "
         . "WHERE stUsuario = '$usuarioEmail' && stMundo = '$mundoId'";
    $query = $con->query($sql);
    if($query->num_rows>0){     //Caso o usuário esteja neste mundo, deixa ele entrar:
        while($dados = $query->fetch_array(MYSQLI_ASSOC)){
            if($dados["blStatus"]==1){
                $enterStatus = 'true';      //Pode entrar
            }else if($dados["blStatus"]==0){
                $enterStatus = 'solicited';     //Aguardando solicitação já enviada
            }
        }
    }else{                      //Caso contrário, não:
        $enterStatus = 'unsolicited';       //Solicitação não enviada
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
        $mundoCapa = $dados["stCapa"];
        //Cria div oculta com nome da imagem de capa, pra ser 'lida' pelo html:
        echo "<div id='nomeCapa' style='display: none;'>$mundoCapa</div>";
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
<?php include_once 'php/_header.php'; ?>
<?php include_once 'php/banner.php'; ?>
<?php include_once 'php/menu.php'; ?>

<script>
    function sairMundo(){
        if(confirm('Deseja mesmo deixar de fazer parte deste mundo?')){
            document.getElementById("formSair").submit();
        }
    }
    function exibeDescricao(){
        document.getElementById("mundoDescricao").style = "display: block";
    }
</script>

<div class="conteudo">
    <?php
        if($enterStatus != 'true'){     //Se o usuário não puder entrar, exibe apenas as informações:
            echo "<h2>$mundoNome</h2>Mundo $mundoTipo<br>Criado por $mundoCreatorNome<br><br>"
                    . "<img class='mundoCapa' src='mundos/$mundoId/$capaArquivo'><br><br>$mundoDescricao<br><br>";
            //Exibe lista de staffs:
            $staffList = 'Staffs:<br>';
            include 'php/_dbconnect.php';
            $sql = "SELECT U.stNickname AS uNome FROM tbStaffs S INNER JOIN tbUsuarios U "
                    . "ON S.stUsuario = U.stEmail WHERE S.stMundo='$mundoId'";
            $query = $con->query($sql);
            if(!$query){
                echo mysqli_error($con);
            }
            while($dados = $query->fetch_array(MYSQLI_ASSOC)){
                $staffList .= $dados["uNome"].'<br>';
            }
            echo "$staffList<br>";
            mysqli_close($con);
            if($enterStatus == 'unsolicited'){    //Exibe a opção de pedir parar entrar no mundo:
                echo "<form method='post'>"
                    . "<input name='usuarioId' type='hidden' value='$usuarioEmail'>"
                    . "<input name='entra' type='hidden' value='true'>"
                    . "<input type='submit' value='Entrar no Mundo'></form>";
            }else if($enterStatus == 'solicited'){  //Exibe a mensagem de aguardo:
                echo 'Uma solciitação de entrada foi enviada. Aguarde a aprovação pela staff.';
            }
        }else{              //Se o usuário puder entrar, exibe os personagens e cenas:
            include 'php/_dbconnect.php';
            echo "<div style='text-align: center;'><h1>$mundoNome</h1>"
                . "<img src='mundos/$mundoId/$mundoCapa' style='max-width: 500px; max-height: 350px;'>"
                . "<br><br><button onclick='exibeDescricao()'>Ver descrição</button>"
                . "<div id='mundoDescricao' class='bloco' style='display: none'>$mundoDescricao</div></div>";   //Esta div possui seu próprio estilo no CSS
            echo '<div class="bloco">';
            //Exibe o botão de sair do mundo (caso o usuário não seja o dono):
            if($mundoCreator != $usuarioEmail){
                echo "<form id='formSair' method='post'>"
                    . "<input name='usuarioId' type='hidden' value='$usuarioEmail'>"    //Exibe form de sair do mundo
                    . "<input name='mundoId' type='hidden' value='$mundoId'>"
                    . "<input name='sair' type='hidden' value='true'></form>"
                    . "<button onclick='sairMundo()'>Sair deste mundo</button><br>";     //Exibe botão de sair do mundo
                echo "Mundo $mundoTipo - Criado por $mundoCreatorNome<br><br>";   //Exibe informações do mundo
            }else{
                echo "Mundo $mundoTipo - Criado por você<br><br>";   //Exibe informações do mundo
            }
            //Exibe lista de staffs:
            $sql = "SELECT U.stNickname AS sNome, S.stUsuario AS sEmail "
                    . "FROM tbStaffs S INNER JOIN tbUsuarios U "
                    . "ON S.stUsuario = U.stEmail "
                    . "WHERE S.stMundo='$mundoId'";
            $query = $con->query($sql);
            $staffList = 'Staffs: ';
            while($dados = $query->fetch_array(MYSQLI_ASSOC)){
                $staffId = $dados["sEmail"];
                $staffName = $dados["sNome"];
                $staffList .= "<form method='post' action='perfil.php' style='display: inline-block;'>"
                        . "<input name='id' type='hidden' value='$staffId'>"
                        . "<input type='submit' value='$staffName'></form>";
            }
            echo $staffList.'<br>';
            //Exibe opção de criar personagem:
            echo "<a href='criarPersonagem.php?mundo=$mundoId'><h3>Criar Personagem</h3></a><br>";
            //Pega lista de personagens que o usuário possui neste mundo:
            $sql = "SELECT * FROM tbPersonagens WHERE stMundo='$mundoId' && stDono='$usuarioEmail'";
            $query = $con->query($sql);
            if($query->num_rows>0){
                $personagensList = 'Personagens seus neste mundo:';
                while($dados = $query->fetch_array(MYSQLI_ASSOC)){
                    $personagemNome = $dados["stNome"];
                    $personagemId = $dados["stId"];
                    $personagemMundo = $dados["stMundo"];
                    $personagensList .= "<br><a href='infoPersonagem.php?id=$personagemId&mundo=$personagemMundo'>"
                            . "$personagemNome</a>";
                }
                echo $personagensList;
            }else{
                echo 'Você não possui nenhum personagem neste mundo.';
            }
            echo "</div>";
            echo "<br><h2 style='display: inline-block;'>Cenas</h2>"
               . "<a href='criarCena.php?mundo=$mundoId' style='float: right;'><h3>Criar Cena</h3></a><br>";
            //Exibe a lista de cenas que o mundo possui:
            $sql = "SELECT C.stId AS cId, C.stNome AS cNome, C.stCreator, C.blEstado AS cEstado, "
                    . "C.dtData cData, C.stImagem AS cImagem, P.stNome AS pNome, P.stDono AS pDono "
                    . "FROM tbCenas C INNER JOIN tbPersonagens P "
                    . "ON C.stCreator = P.stId WHERE C.stMundo='$mundoId' ORDER BY dtData";
            $query = $con->query($sql);
            if($query->num_rows>0){
                while($dados = $query->fetch_array(MYSQLI_ASSOC)){
                    $cenaId = $dados["cId"];
                    $cenaNome = $dados["cNome"];
                    $cenaCreator = $dados["pNome"];
                    $personagemDono = $dados["pDono"];
                    $cenaEstado = $dados["cEstado"];
                    $cenaData = $dados["cData"];
                    if($dados["cImagem"] == 'none'){
                        $cenaImagem = "mundos/$mundoId/$mundoCapa";
                    }else{
                        $arquivo = $dados["cImagem"];
                        $cenaImagem = "mundos/$mundoId/cenas/$cenaId/$arquivo";
                    }
                    echo "<div class='bloco'><div class='cenaImagem'><img src='$cenaImagem' style='width: 200px;'>"
                            . "</div><div class='cenaConteudo'><h3><a href='cena.php?id=$cenaId&mundo=$mundoId'>"
                            . "$cenaNome</h3></a>$cenaData<br>Criada por $cenaCreator";
                    if($personagemDono == $usuarioEmail){     //Se a cena for do usuário, exibe a opção de deletar:
                        echo "<form action='deletaCena.php' method='post'>"
                        . "<input name='mundo' type='hidden' value='$mundoId'>"
                        . "<input name='id' type='hidden' value='$cenaId'>"
                        . "<input type='submit' value='Deletar'></form>";
                    }
                    echo '</div></div>';
                }
            }else{
                echo 'Não há cenas neste mundo.';
            }
            mysqli_close($con);
            echo '</div>';
        }
    ?>
</div>

<?php   //Processa o pedido de usuário entrar ou sair do mundo:
    if(isset($_POST["entra"])){
        include 'php/_dbconnect.php';
        $usuarioEmail = $_POST["usuarioId"];
        //Verifica se o mundo é publico:
        $sql = "SELECT blPublic, stCreator, stNome FROM tbMundos WHERE stId='$mundoId'";
        $query = $con->query($sql);
        while($dados = $query->fetch_array(MYSQLI_ASSOC)){
            $mundoTipo = $dados["blPublic"];
            $mundoCreator = $dados["stCreator"];
            $mundoNome = $dados["stNome"];
        }
        if($mundoTipo){     //Se o mundo for público, deixa o usuário entrar e cadastra no BD:
            $sql = "INSERT INTO tbMundoUsuarios VALUES ('$usuarioEmail', '$mundoId', true,'$mundoCreator')";
            $query = $con->query($sql);
            if(!$query){
                die("Erro no query: ". mysqli_error($con));
            }
            header("location: escolhaCena.php?mundo=$mundoId");
        }else{      //Se o mundo for privado, apenas manda pedido de entrada:
            $sql = "INSERT INTO tbMundoUsuarios VALUES ('$usuarioEmail','$mundoId', false, null)";
            $query = $con->query($sql);
            if(!$query){
                echo 'Erro no query(inserir em MundoUsuarios):'.mysqli_error($con);
                mysqli_close($con);
                die();
            }
            //Cria a notificação pro dono do mundo:
            $sql = "INSERT INTO tbNotifs(stUsuario, stTipo, stLink, stConteudo, etc1, etc2) "
                 . "VALUES ('$mundoCreator','SM',null,'$usuarioNome deseja entrar no seu mundo $mundoNome.',"
                    . "'$usuarioEmail','$mundoId')";
            $query = $con->query($sql);
            if(!$query){
                echo 'Erro no query(inserir em Notifs):'.mysqli_error($con);
                mysqli_close($con);
                die();
            }
            header("location: escolhaCena.php?mundo=$mundoId");
        }
        mysqli_close($con);
    }
    if(isset($_POST["sair"])){
        include 'php/_dbconnect.php';
        $usuarioEmail = $_POST["usuarioId"];
        $mundoId = $_POST["mundoId"];
        //Elimina o usuário do mundo no BD:
        $sql = "DELETE FROM tbMundoUsuarios WHERE stUsuario='$usuarioEmail' && stMundo='$mundoId'";
        $query = $con->query($sql);
        if(!$query){
            echo 'Erro no query(deletar MundoUsuario): '.mysqli_error($con);
            mysqli_close($con);
            die();
        }
        mysqli_close($con);
        header("location: selecionaMundo.php");
    }
?>

<?php include_once 'php/_anuncio.php'; ?>
<?php include_once 'php/_footer.php'; ?>