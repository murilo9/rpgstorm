<?php session_start(); ?>
<?php include_once 'php/_header.php'; ?>
<?php include_once 'php/banner.php'; ?>
<?php include_once 'php/menu.php'; ?>

<script>
    function loadDocAjax(personagemNome) { 
        var xhttp = new XMLHttpRequest(); 
        var ficha = document.getElementById("modeloFicha");
        xhttp.onreadystatechange = function() { 
          if (this.readyState == 4 && this.status == 200) { 
              alert(this.responseText);
            //--TODO tratamento de resposta do servidor 
          } 
        }; 
        xhttp.open("POST", "instanciaPersonagem.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send('personagemNome='+personagemNome+'&ficha='+ficha.innerHTML); 
    } 
    function criarPersonagem(){     //Verifica se o personagem possui um nome antes de enviar o Ajax request
        var personagemNome = document.getElementById("personagemNome").value;
        if(personagemNome != ''){
            loadDocAjax(personagemNome);
        }else{
            alert('O(a) personagem deve ter um nome.');
        }
    }
</script>

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
    <h2>Criar Personagem em <?php echo $mundoNome;?></h2>
    <?php   //pega modelo de ficha
        $arquivoAberto = fopen("mundos/$mundoId/ficha.php", 'r');
        $modeloFicha = '';
        while(!feof($arquivoAberto)){
            $modeloFicha .= fgets($arquivoAberto);
        }
        fclose($arquivoAberto);
        //Exibe a ficha para o usuário preencher:
        echo "<div class='formulario' id='modeloFicha'>$modeloFicha</div>";
    ?>
    <button type="button" onclick="criarPersonagem()">Criar</button>
</div>

<?php include_once 'php/_footer.php'; ?>