<?php session_start(); //Inicia a session
    //Pega os dados necessários para esta página:
    $usuarioEmail = $_SESSION["usuarioEmail"];
?>
<?php include_once 'php/_header.php'; ?>

<script scr="js/funcoesFicha.js"></script>

<?php include_once 'php/banner.php'; ?>
<?php include_once 'php/menu.php'; ?>

<div style="text-align: center;">
    <h2>Criar novo mundo</h2>
        <!-- FORMULÁRIO QUE SERÁ SUBMETIDO -->
        <form id="formularioMundo" class="formulario" action="criaMundo.php" method="post" enctype="multipart/form-data" style="text-align: center;">
            <h3>Informações</h3><br>
            Nome do Mundo: <input name="inputNome" type="text"><br><br>
            Tipo:<br><input type="radio" name="tipo" value="true" checked> Público
            <input type="radio" name="tipo" value="false"> Privado<br><br>
            Imagem de Capa:<br><input name="arquivo" size="20" type="file"><br><br>
            <textarea name="inputDescricao" cols="50" rows="20">Descrição do mundo</textarea>
            <input type="hidden" name="inputId" id="inputId">   <!-- aqui vai o Id aleatório do mundo, gerado pelo js-->
            <input type="hidden" name="inputFicha" id="inputFicha">   <!-- aqui vai a div da ficha cridada dinamicamente-->
            <input type="hidden" name="enviar" value="ok">
        </form>
    <br>
    <div class="formulario" style="background: #edd9a8; border: none; text-align: center;">
        <h3>Modelo de Ficha de Personagem</h3><br>
        <b>Inserir:</b><br><Br>
        <button type="button" onclick="createSmallInput()">Campo Pequeno</button>
        <button type="button" onclick="createLargeInput()">Campo Grande</button>
        <button type="button" onclick="createSmallTextArea()">Area de Texto Pequena</button>
        <button type="button" onclick="createLargeTextArea()">Area de Texto Grande</button>
        <button type="button" onclick="createHR()">Divisor Horizontal</button><br><br>
        Label: <input name="texto" id="texto" type="text"> 
        <input id="negrito" value="negrito" type="checkbox"> Negrito<br><br>
        <button type="button" onclick="createLabel()">Inserir Label</button>
        <button type="button" onclick="createBR()">Pular Linha</button>
        <button type="button" onclick="removeElemento()">Deletar Último Elemento</button>
        <button type="button" onclick="resetarForm()">Resetar</button>
        
    </div><br>
    <div class="formulario">
        <b>Preview</b>
        <div id="modeloFicha">
            <!-- AQUI VÃO OS ELEMENTOS INSERIDOS DINAMICAMENTE -->
        </div><br><br>
    </div><br>
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
        $mundoDescricao = $_POST["inputDescricao"];     //Guarda o texto formatado que vai no <pre>
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
        //Criar arquivo ficha.php com as tags da ficha:
        $arquivoAberto = fopen("mundos/$mundoId/ficha.php",'x');
        if(!$arquivoAberto){
            echo 'Bad fopen on create: ficha.php';
            mysqli_close($con);
            die();
        }
        if(!fwrite($arquivoAberto,$mundoFicha)){
            echo 'Bad fopen on write.';
            mysqli_close($con);
            die();
        }
        fclose($arquivoAberto);
        //Criar arquivo info.php com descrição do mundo:
        $arquivoAberto = fopen("mundos/$mundoId/info.php",'x');
        if(!$arquivoAberto){
            echo 'Bad fopen on create: info.php';
            mysqli_close($con);
            die();
        }
        if(!fwrite($arquivoAberto,"<pre>$mundoDescricao</pre>")){
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
        $sql= "INSERT INTO tbStaffs VALUES ('$usuarioEmail','$mundoId')";
        $query = $con->query($sql);     //Insere o criador do mundo como staff
        if(!$query){
            echo 'Erro na inserção no banco de dados (tbStaffs): ';
            echo mysqli_error($con);
            mysqli_close($con);
            die();
        }
        $sql = "INSERT INTO tbMundoUsuarios VALUES ('$usuarioEmail','$mundoId')";
        $query = $con->query($sql);     //Insere o criador do mundo como usuário(que pode entrar) do mundo
        if(!$query){
            echo 'Erro na inserção no banco de dados (tbStaffs): ';
            echo mysqli_error($con);
            mysqli_close($con);
            die();
        }
        echo 'Mundo criado com sucesso.';
        //echo "Id: $mundoId<br>Nome: $mundoNome<br>Tipo: $mundoTipo<br>Criador: $usuarioEmail".
                "<br>Descr: $mundoDescricao<br>File: $nomeCapa<br>";
        //Unsets e disconnect:
        mysqli_close($con);
        unset($_POST["enviar"]);
    }
?>

<?php include_once 'php/_footer.php'; ?>