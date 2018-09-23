function loadDocAjax(personagemNome) { 
        var xhttp = new XMLHttpRequest(); 
        var ficha = document.getElementById("modeloFicha");
        xhttp.onreadystatechange = function() { 
          if (this.readyState == 4 && this.status == 200) { 
            alert(this.responseText);
          } 
        }; 
        var randomId = '_'+Math.floor(Math.random()*99999);   //Cria ID aleatório pro personagem
        var mundoId = document.getElementById("mundoId").innerText;     //Pega id do mundo (innerHTML da div oculta mundoId)
        var usuarioEmail = document.getElementById("usuarioEmail").innerText;     //Pega id do mundo (innerHTML da div oculta mundoId)
        xhttp.open("POST", "instanciaPersonagem.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send('personagemNome='+personagemNome+'&ficha='+ficha.innerHTML+'&id='
                +randomId+'&mundo='+mundoId+'&usuarioEmail='+usuarioEmail); 
    } 
    function criarPersonagem(){     //Verifica se o personagem possui um nome antes de enviar o Ajax request
        var personagemNome = document.getElementById("personagemNome").value;
        if(personagemNome != ''){
            loadDocAjax(personagemNome);
        }else{
            alert('O(a) personagem deve ter um nome.');
        }
    }
