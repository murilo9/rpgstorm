<?php session_start(); //Inicia a session
    //Pega os dados necessários para esta página:
    $usuarioEmail = $_SESSION["usuarioEmail"];
?>
<?php include_once 'php/_header.php'; ?>

<script>
        function createSmallInput(){
		var formulario = document.getElementById("modeloFicha");
		var meuInput = document.createElement("input");
		meuInput.type = "text";
                meuInput.size = 4;
		formulario.appendChild(meuInput);
	}
        function createLargeInput(){
		var formulario = document.getElementById("modeloFicha");
		var meuInput = document.createElement("input");
		meuInput.type = "text";
		formulario.appendChild(meuInput);
	}
        function createSmallTextArea(){
		var formulario = document.getElementById("modeloFicha");
		var meuInput = document.createElement("textarea");
		meuInput.cols = "30";
                meuInput.rows = "8";
		formulario.appendChild(meuInput);
	}
        function createLargeTextArea(){
		var formulario = document.getElementById("modeloFicha");
		var meuInput = document.createElement("textarea");
		meuInput.cols = "50";
                meuInput.rows = "15";
		formulario.appendChild(meuInput);
	}
	function createBR(){
		var formulario = document.getElementById("modeloFicha");
		var meuBR = document.createElement("br");
		formulario.appendChild(meuBR);
	}
	function createLabel(){
		var formulario = document.getElementById("modeloFicha");
		var meuLabel = document.createElement("text");
                if(document.getElementById("negrito").checked){
                    meuLabel.innerHTML = '<b>' + document.getElementById("texto").value + '</b>';alert('check');
                }else
                    meuLabel.innerHTML = document.getElementById("texto").value;
		formulario.appendChild(meuLabel);
	}
	function createHR(){
            var formulario = document.getElementById("modeloFicha");
            var meuHR = document.createElement("hr");
            formulario.appendChild(meuHR);
	}
	//Tratamento dos excludes:
	function removeElemento(){
            var formulario = document.getElementById("modeloFicha");
	formulario.removeChild(formulario.lastChild);
	}
        function resetarForm(){
            var formulario = document.getElementById("modeloFicha");
            formulario.innerHTML = '';
        }
	//TODO:
	function enviar(){
            document.getElementById("inputId").value = '_'+Math.floor(Math.random()*9999);  //Gera Id aleatório pro mundo
            var formMundo = document.getElementById("formularioMundo");
            formMundo.submit();
            //window.location = "server.php?x="+modeloFicha.innerHTML;	//(zuado):Envia o innerHTML (as tags) do formularioCriado pro servidor
	}
</script>

<?php include_once 'php/banner.php'; ?>
<?php include_once 'php/menu.php'; ?>

<div style="text-align: center;">
    <h2>Criar novo mundo</h2>
        <!-- FORMULÁRIO QUE SERÁ SUBMETIDO -->
        <form id="formularioMundo" class="formulario" action="criaMundo.php" method="post" enctype="multipart/form-data" style="text-align: center;">
            <h3>Informações</h3><br>
            Nome do Mundo: <input name="inputNome" type="text"><br><br>
            Tipo:<br><input type="radio" name="tipo" value=true checked> Público
            <input type="radio" name="tipo" value=false> Privado<br><br>
            Imagem de Capa:<br><input name="arquivo" size="20" type="file"><br><br>
            <textarea name="descricao" cols="50" rows="20">Descrição do mundo</textarea>
            <input type="hidden" name="inputId" id="inputId">
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
        <form id="modeloFicha">
            <!-- AQUI VÃO OS ELEMENTOS INSERIDOS DINAMICAMENTE -->
        </form><br><br>
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
        $sql = "SELECT stId FROM tbMundos WHERE stNome = '$mundoNome'";
        $query = $con->query($sql);
        if($query->num_rows>0){
            echo 'Ja existe um mundo com este nome';
            die();
        }
        $sql = "SELECT stId FROM tbMundos WHERE stId = '$mundoId'";
        $query = $con->query($sql);
        if($query->num_rows>0){
            echo 'Erro interno. Tente mais uma vez.';
            die();
        }
        //Criar mundo no BD:
        echo "Id: $mundoId<br>Nome: $mundoNome<br>Tipo: $mundoTipo<br>Criador: $usuarioEmail";
        //$sql = "INSERT INTO tbMundos VALUES ('','','',$mundoTipo)";
        //Criar pasta do mundo no servidor:
        //Carregar a imagem de capa do mundo pro servidor:
        //Criar arquivo com a div de ficha:
        //Criar arquivo info.php com descrição do mundo:
        //Unsets e disconnect:
    }
?>

<?php include_once 'php/_footer.php'; ?>