<?php session_start(); ?>
<?php include_once 'php/sessionVerif.php'; ?>
<?php include_once 'php/_header.php'; ?>
<?php include_once 'php/banner.php'; ?>
<?php include_once 'php/menu.php'; ?>

<script>
    function exibeFicha(){
        document.getElementById("inputNome").value = document.getElementById("nome").innerText;
        document.getElementById("inputGenero").value = document.getElementById("genero").innerText;
        document.getElementById("inputRaca").value = document.getElementById("raca").innerText;
        document.getElementById("inputIdade").value = document.getElementById("idade").innerText;
        document.getElementById("inputDescricao").value = document.getElementById("descricao").innerText;
        document.getElementById("inputHabilidades").value = document.getElementById("habilidades").innerText;
        document.getElementById("personagemFicha").style = "display: inline-block";
    }
</script>

<div class="conteudo">
    <?php
        $personagemId = $_GET["id"];
        $personagemMundoId = $_GET["mundo"];
        include 'php/_dbconnect.php';
        $usuarioEmail = $_SESSION["usuarioEmail"];
        //Verifica se o personagem pertence a este usuário:
        $sql = "SELECT stId FROM tbPersonagens WHERE stId='$personagemId' && stDono = '$usuarioEmail'";
        $query = $con->query($sql);
        if($query->num_rows==0){    //Se o usuário não é dono deste personagem, mostra parcial:
            $viewMode = 'parcial';
        }else{          //Se o usuário é dono deste personagem, mostra tudo:
            $viewMode = 'full';
        }
        //Se chegou até aqui, pode pegar as informações do personagem no BD:
        $sql = "SELECT P.stNome AS pNome, P.stMundo AS pMundoId, M.stNome AS mNome, "
                . "U.stNickname AS uNome, U.stEmail AS uEmail "
                . "FROM tbPersonagens P INNER JOIN tbMundos M INNER JOIN tbUsuarios U "
                . "ON P.stMundo = M.stId && P.stDono = U.stEmail "
                . "WHERE P.stId='$personagemId' && P.stMundo='$personagemMundoId'";
        $query = $con->query($sql);
        while($dados = $query->fetch_array(MYSQLI_ASSOC)){
            $personagemNome = $dados["pNome"];
            $personagemMundo = $dados["mNome"];
            $personagemMundoId = $dados["pMundoId"];
            if($dados["uNome"] == 'none'){
                $personagemDono = 'Ninguém';
                $personagemDonoId = 'none';
            }else{
                $personagemDono = $dados["uNome"];
                $personagemDonoId = $dados["uEmail"];
            }
        }
        mysqli_close($con);
        //Pega o arquivo de ficha:
        $arquivoAberto = fopen("mundos/$personagemMundoId/personagens/$personagemId/ficha.php", 'r');
        if(!$arquivoAberto){
            die('Erro no fopen.');
        }
        $personagemFicha = '';
        while(!feof($arquivoAberto)){
            $personagemFicha .= fgets($arquivoAberto);
        }
        fclose($arquivoAberto);
        //Verifica se possui foto:
        include 'php/_dbconnect.php';
        $sql = "SELECT stFoto FROM tbPersonagens WHERE stId = '$personagemId'";
        $query = $con->query($sql);
        while($dados = $query->fetch_array(MYSQLI_ASSOC)){
            $personagemFoto = $dados["stFoto"];
        }
        mysqli_close($con);
        if($personagemFoto === 'none'){
            echo "<img src='img/foto-none.jpg' width='40%'></img>";
        }else{
            echo "<img src='mundos/$personagemMundoId/personagens/$personagemId/$personagemFoto' width='30%'></img>";
        }
        if($viewMode == 'full'){
        echo "<form action='php/mudaFoto.php?id=$personagemId' method='post' enctype='multipart/form-data'>"
        . "<input name='arquivo' type='file' size='20'><input name='atualiza' type='hidden' value='true'>"
                . "<input type='submit' value='Atualizar Retrato'></form>";
        }
        //Exibe todos os dados:
        echo "<h2>$personagemNome</h2>Mundo: <a href='escolhaCena.php?mundo=$personagemMundoId'>$personagemMundo</a><br>"
                . "Dono: <form method='post' action='perfil.php'><input name='id' type='hidden' value='$personagemDonoId'>"
                . "<input type='submit' value='$personagemDono'></form><br>";
        if($viewMode == 'full'){
                echo "<form action='deletaPersonagem.php' method='post'>"
                . "<input type='hidden' name='id' value='$personagemId'>"
                . "<input type='hidden' name='mundo' value='$personagemMundoId'>"
                . "<input type='submit' value='Deletar'></form><br><br>";
        } 
        echo "<div style='display: none'>$personagemFicha</div>";   //Div oculta com os dados do personagem
        echo "<button onclick='exibeFicha()'>Ver Ficha</button><br>";
        echo "<form class='formulario' id='personagemFicha' style='display: none'>" //Form que exibe a ficha do personagem
            . "Nome: <input name='inputNome' id='inputNome' type='text'><br>"
            . "Gênero: <input name='inputGenero' id='inputGenero' type='text'><br>"
            . "Raça: <input name='inputRaca' id='inputRaca' type='text'><br>"
            . "Idade: <input name='inputIdade' id='inputIdade' type='text' size='4'><br>"
            . "Descrição<br><textarea name='inputDescricao' id='inputDescricao' cols='50' rows='15'></textarea><br>"
            . "Habilidades <br><textarea name='inputHabilidades' id='inputHabilidades' cols='50' rows='15'></textarea></form>";
    ?>
</div>

<?php include_once 'php/_footer.php'; ?>