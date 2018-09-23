<?php   //Arquivo pro Ajax
    $personagemNome = $_POST["personagemNome"];
    $ficha = $_POST["ficha"];
    $personagemId = $_POST["id"];
    $mundoId = $_POST["mundo"];
    $usuarioEmail = $_POST["usuarioEmail"];
    //echo "Nome: $personagemNome Ficha: $ficha";
    //--Até aqui tudo funcionando...
    //--TODO validação do nome no BD, registro do personagem no BD e criação dos arquivos (ficha) no servidor
    include 'php/_dbconnect.php';
    $sql = "SELECT stNome FROM tbPersonagens WHERE stNome = '$personagemNome' || stId = '$personagemId'";
    $query = $con->query($sql);
    if($query->num_rows>0){     //Se um personagem com este nome existir, retorna name_exists
        echo 'Este nome de personagem não está disponível neste mundo.';
        mysqli_close($con);
        die();
    }
    //Cria a pasta no servidor:
    $pastaPersonagem = "mundos/$mundoId/personagens/$personagemId";
    mkdir($pastaPersonagem);
    //Cria arquivo com a div de ficha:
    $arquivoAberto = fopen("$pastaPersonagem/ficha.php", 'w');
    fwrite($arquivoAberto, $ficha);
    fclose($arquivoAberto);
    //Registra o personagem no DB:
    $sql = "INSERT INTO tbPersonagens VALUES ('$personagemId','$mundoId','$usuarioEmail','$personagemNome', 'none')";
    $query = $con->query($sql);
    if(!$query){
        echo "Erro no query:".mysqli_error($con);
    }else{
        echo "Personagem $personagemNome criado com sucesso neste mundo.";
    }
    mysqli_close($con);
?>
