<?php session_start(); ?>

<?php       //Atualiza o retrato do personagem no BD:
    if(isset($_POST["atualiza"])){
        if($_POST["atualiza"] === 'true'){
            $usuarioEmail = $_SESSION["usuarioEmail"];
            $personagemId = $_GET["id"];
            include '_dbconnect.php';
            //Verificando se o personagem é deste usuário:
            $sql = "SELECT stId, stMundo FROM tbPersonagens WHERE stId='$personagemId' && stDono = '$usuarioEmail'";
            $query = $con->query($sql);
            if($query->num_rows==0){
                mysqli_close($con);
                die('Erro: este personagem não é seu.');
            }else{      //Pega o mundo do personagem
                while($dados = $query->fetch_array(MYSQLI_ASSOC)){
                    $personagemMundoId = $dados["stMundo"];
                }
            }
            //Fazendo upload da imagem:
            $uploadDir = "../mundos/$personagemMundoId/personagens/$personagemId/";
            $file = $_FILES["arquivo"]["name"];
            $uploadFile = $uploadDir.$file;
            if(move_uploaded_file($_FILES["arquivo"]["tmp_name"], $uploadFile)){
                echo 'Upload feito com sucesso.';
            }else{
                echo 'Falha no upload.';
            }
            //Atualizando no BD:
            $sql = "UPDATE tbPersonagens SET stFoto = '$file' WHERE stId = '$personagemId'";
            $query = $con->query($sql);
            if(!$query){
                echo 'Erro no query:'.mysqli_error($con);
            }
            mysqli_close($con);
            header("location: ../infoPersonagem.php?id=$personagemId");
            echo 'fim';
        }
        
    }
?>

