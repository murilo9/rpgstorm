        /*
         *  Este arquivo contém as funções necessárias para fazer a inserção dinâmica
         *  de elementos de ficha na página de criar mundo. Também possui a função que
         *  dá request no formulário de dados do mundo.
         */
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
            var ficha = document.getElementById("modeloFicha");     //Pega a div do modelo de ficha
            var formConfigMundo = document.getElementById("formularioMundo");     //Pega o formulário que vai ser submetido
            document.getElementById("inputFicha").value = ficha.innerHTML;  //Guarda os elementos da ficha no input da form que vai ser submetida
            formConfigMundo.submit();
            //window.location = "server.php?x="+modeloFicha.innerHTML;	//(zuado):Envia o innerHTML (as tags) do formularioCriado pro servidor
	}