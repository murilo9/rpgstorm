<?php session_start(); ?>
<?php include_once 'php/sessionVerif.php'; 
    //Pega os dados necessários para esta página:
    $usuarioEmail = $_SESSION["usuarioEmail"];
?>
<?php include_once 'php/_header.php'; ?>

<script>
    function enviar(){
        document.getElementById("inputId").value = '_'+Math.floor(Math.random()*9999);  //Gera Id aleatório pro mundo
        var formConfigMundo = document.getElementById("formularioMundo");     //Pega o formulário que vai ser submetido
        formConfigMundo.submit();
        //window.location = "server.php?x="+modeloFicha.innerHTML;	//(zuado):Envia o innerHTML (as tags) do formularioCriado pro servidor
    }
</script>

<?php include_once 'php/banner.php'; ?>
<?php include_once 'php/menu.php'; ?>

<div style="text-align: center;">
    <h2>Criar novo mundo</h2>
        <!-- FORMULÁRIO QUE SERÁ SUBMETIDO -->
        <form id="formularioMundo" class="formulario" method="post" enctype="multipart/form-data" style="text-align: center;">
            <h3>Informações</h3><br>
            Nome do Mundo: <input name="inputNome" type="text"><br><br>
            Tipo:<br><input type="radio" name="tipo" value="true" checked> Público
            <input type="radio" name="tipo" value="false"> Privado<br><br>
            Imagem de Capa:<br><input name="arquivo" size="20" type="file"><br><br>
            <textarea name="inputDescricao" cols="50" rows="20">Descrição do mundo</textarea>
            <input type="hidden" name="inputId" id="inputId">   <!-- aqui vai o Id aleatório do mundo, gerado pelo js-->
            <input type="hidden" name="enviar" value="ok">
        </form>
    <br>
    <div class="formulario" style="background: #edd9a8; border: none; text-align: center;">
        <button type="button" onclick="enviar()">Criar Mundo</button>
    </div>
</div>

<?php
    if(isset($_POST["enviar"]) && $_POST["enviar"] === "ok"){     //Só inicia o procedimento se enviar estiver ok
        include_once 'php/_dbconnect.php';
        //Verificar se os dados são válidos:
        $mundoNome = $_POST["inputNome"];
        $mundoTipo = $_POST["tipo"];
        $mundoId = $_POST["inputId"];
        $mundoDescricao = $_POST["inputDescricao"];     //Guarda o texto formatado que vai no <text>
        $mundoFicha = $_POST["inputFicha"];     //Possui as tags de elementos que compõem a ficha
        $sql = "SELECT stId FROM tbMundos WHERE stNome = '$mundoNome'";
        $query = $con->query($sql);
        if($query->num_rows>0){
            echo 'Ja existe um mundo com este nome';
            mysqli_close($con);
            die();
        }
        $sql = "SELECT stId FROM tbMundos WHERE stId = '$mundoId'";
        $query = $con->query($sql);
        if($query->num_rows>0){
            echo 'Erro interno. Tente mais uma vez.';
            mysqli_close($con);
            die();
        }
        if($_FILES["arquivo"]["name"] === ''){
            echo 'O mundo deve ter uma imagem de capa.';
            mysqli_close($con);
            die();
        }
        //Criar pasta do mundo no servidor:
        mkdir("mundos/$mundoId");
        mkdir("mundos/$mundoId/cenas");
        mkdir("mundos/$mundoId/personagens");
        //Carregar a imagem de capa do mundo pro servidor:
        $uploadDir = "mundos/$mundoId/";
        $uploadFile = $uploadDir . $_FILES["arquivo"]["name"];
        $nomeCapa = $_FILES["arquivo"]["name"];
        if(move_uploaded_file($_FILES["arquivo"]["tmp_name"], $uploadFile)){
            echo 'Upload Feito com sucesso.<br>';
        }else{
            echo 'Erro. O upload não foi feito';
            mysqli_close($con);
            die();
        }
        //Criar arquivo info.php com descrição do mundo:
        $arquivoAberto = fopen("mundos/$mundoId/info.php",'x');
        if(!$arquivoAberto){
            echo 'Bad fopen on create: info.php';
            mysqli_close($con);
            die();
        }
        if(!fwrite($arquivoAberto,"<div style='max-width: 100%; text-align: justify;'><text>"
                . "$mundoDescricao</text></div>")){
            echo 'Bad fopen on write.';
            mysqli_close($con);
            die();
        }
        fclose($arquivoAberto);
        //Criar mundo no BD:
        $sql = "INSERT INTO tbMundos VALUES ('$mundoId','$mundoNome','$usuarioEmail',$mundoTipo,'$nomeCapa')";
        $query = $con->query($sql);     //Cria o mundo na tabela de mundos
        if(!$query){
            echo 'Erro na inserção no banco de dados (tbMundos): ';
            echo mysqli_error($con);
            mysqli_close($con);
            die();
        }
        //Insere o criador do mundo como staff:
        $sql= "INSERT INTO tbStaffs VALUES ('$usuarioEmail','$mundoId')";
        $query = $con->query($sql);     //Insere o criador do mundo como staff
        if(!$query){
            echo 'Erro na inserção no banco de dados (tbStaffs): ';
            echo mysqli_error($con);
            mysqli_close($con);
            die();
        }
        //Insere o criador do mundo como usuário
        $sql = "INSERT INTO tbMundoUsuarios VALUES ('$usuarioEmail','$mundoId',true,'$usuarioEmail')";
        $query = $con->query($sql);     //Insere o criador do mundo como usuário(que pode entrar) do mundo
        if(!$query){
            echo 'Erro na inserção no banco de dados (tbStaffs): ';
            echo mysqli_error($con);
            mysqli_close($con);
            die();
        }
        //Se chegou até aqui, então o mundo foi criado com sucesso
        //Unsets e disconnect:
        mysqli_close($con);
        unset($_POST["enviar"]);
        header("location: meusMundos.php");
    }
?>

<?php include_once 'php/_footer.php'; ?>