<?php session_start(); ?>
<?php include_once 'php/sessionVerif.php'; 
    $perfilId = $_POST["id"];
    $usuarioEmail = $_SESSION["usuarioEmail"];
    if($perfilId == $usuarioEmail){     //Se o usuário visitar o próprio perfil, redireciona pra conta.php
        header("location: conta.php");
    }
    include_once 'php/_dbconnect.php';
    $sql = "SELECT * FROM tbUsuarios WHERE stEmail='$perfilId'";
    $query = $con->query($sql);
    if($query->num_rows>0){
        $user = true;
        while($dados = $query->fetch_array(MYSQLI_ASSOC)){
            $infoUsuarioNome = $dados["stNickname"];
        }
    }else{
        $user = false;
    }
    mysqli_close($con);
?>

<script>
    function exibirConvite(){
        document.getElementById("convidar").style = "display: block;";
    }
    function loadDoc(func) { 
        var usuarioId = document.getElementById("usuarioId").innerText;     //Pega o ID do usuaário
        var infoUsuarioId = document.getElementById("infoUsuarioId").innerText;     //Pega o ID do destino
        var id = '_'+Math.floor(Math.random()*99999);    //Cria ID aleatório único pra conversa
        var message = document.getElementById("textBox").value;     //Pega o texto da textBox pra enviar pro chat
        var xhttp = new XMLHttpRequest(); 
        xhttp.onreadystatechange = function() { 
          if (this.readyState == 4 && this.status == 200 && func == 'get')
                  document.getElementById("chatBox").innerHTML = this.responseText; //Coloca a response no chatBox
        }; 
        xhttp.open("POST", "chatManager.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("usuarioId="+usuarioId+"&infoUsuarioId="+infoUsuarioId+"&func="+func+"&id="+id+"&message="+message);
    }
    window.onload = function(){loadDoc('get')}; //Assim que carregar a janela, dá um get request pro server
    window.setInterval(function(){loadDoc('get');},1000);  //Manda get request pro server a cada 1 seg
</script>

<?php include_once 'php/_header.php'; ?>
<?php include_once 'php/banner.php'; ?>
<?php include_once 'php/menu.php'; ?>

<div class="conteudo">
    <?php
        if(!$user){      //Se o usuário não existe, exibe mensagem de erro:
            echo 'Usuário inexistente.';
            die();
        }
        //Se chegou até aqui, o ususário existe, então mostra a pagina:
        echo "<div id='usuarioId' style='display: none'>$usuarioEmail</div>"    //Divs ocultas com id dos usuarios pro JS
            . "<div id='infoUsuarioId' style='display: none'>$perfilId</div>"
            . "<h1>$infoUsuarioNome</h1>"
            . "<h2>Chat</h2>"
            . "<div id='chatBox'></div>"
            . "<textarea id='textBox' cols='50' rows='8'></textarea><br>";
    ?>
    <button onclick="loadDoc('send')">Enviar</button><br>
    <button onclick="exibirConvite()">Convidar para Staff</button>
    <div id="convidar" style="display: none;">
        <?php
            include 'php/_dbconnect.php';
            $sql = "SELECT stNome, stId FROM tbMundos WHERE stCreator='$usuarioEmail'";
            $query = $con->query($sql);
            if($query->num_rows==0){    //Se o usuário não possuir nenhum mundo, exibe mensagem:
                echo 'Você não possui nenhum mundo.';
            }else{      //Caso o usuário possua um mundo, exibe a lista de mundos:
                echo "<form action='' method='post'>Convidar para staff de: "
                    . "<select name='selectMundo'>";
                while($dados = $query->fetch_array(MYSQLI_ASSOC)){
                    $mundoId = $dados["stId"];
                    $mundoNome = $dados["stNome"];
                    echo "<option value='$mundoId'>$mundoNome</option>";
                }
                echo "</select><input name='mundoNome' value='$mundoNome' type='hidden'>"
                        . "<br><input type='submit' value='Convidar'>"
                        . "<input name='id' type='hidden' value='$perfilId'>"   //Necessário pro início desta página
                        . "<input name='convite' type='hidden' value='true'></form>";
                        
            }
            mysqli_close($con);
        ?>
    </div>
</div>

<?php   //Processa envio de solicitação pra staff:
    if(isset($_POST["convite"])){   //Verifica antes se foi madada a solicitação
        $mundoId = $_POST["selectMundo"];
        $mundoNome = $_POST["mundoNome"];
        include 'php/_dbconnect.php';
        //Verifica se já existe uma solicitação:
        $sql = "SELECT stUsuario FROM tbNotifs "
                . "WHERE stUsuario='$perfilId' && stTipo='SS' && etc1='$mundoId'";
        $query = $con->query($sql);
        if($query->num_rows>0){     //Se já houver a solicitação, exibe a mensagem:
            echo 'Esta solicitação ja foi enviada para este jogador.';
        }else{      //Caso não haja a solicitação, pode enviar:
            $sql = "INSERT INTO tbNotifs(stUsuario, stTipo, stLink, stConteudo, etc1) "
                    . "VALUES('$perfilId','SS',null,'Você foi convidado para ser staff do mundo $mundoNome.','$mundoId')";
            $query = $con->query($sql);
            if(!$query){
                echo 'Erro no query(registrar notiff): '.mysqli_error($con);
            }
            echo 'Solicitação enviada.';
        }
        mysqli_close($con);
    }
?>

<?php include_once 'php/_footer.php'; ?>