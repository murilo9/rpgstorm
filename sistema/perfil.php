<?php session_start(); ?>
<?php include_once 'php/sessionVerif.php'; 
    $perfilId = $_POST["id"];
    $usuarioEmail = $_SESSION["usuarioEmail"];
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
            <button onclick="loadDoc('send')">Enviar</button>
</div>

<?php include_once 'php/_footer.php'; ?>