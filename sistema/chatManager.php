<?php 
    $usuarioEmail = $_POST["usuarioId"];
    $infoUsuarioEmail = $_POST["infoUsuarioId"];
    $func = $_POST["func"];
    $id = $_POST["id"];
    $message = "<div class='message' writer='$usuarioEmail'>".$_POST["message"].'</div><br>';
    include 'php/_dbconnect.php';
    if($func == 'get'){     //Se for um request pra receber as mensagens:
        $sql = "SELECT stId FROM tbChat "
            . "WHERE (stUser1='$usuarioEmail' && stUser2='$infoUsuarioEmail') || "
                . "(stUser1='$infoUsuarioEmail' && stUser2='$usuarioEmail')";
        $query = $con->query($sql);
        if($query->num_rows>0){     //Se houver uma conversa entre estes dois usuários:
            while($dados = $query->fetch_array(MYSQLI_ASSOC)){
                $arquivoAberto = fopen("chat/".$dados["stId"].'.php', 'r');  //Abre o arquivo da conversa
                $chat = '';
                while(!feof($arquivoAberto)){
                    $chat .= fgets($arquivoAberto);
                }
                fclose($arquivoAberto);
                echo $chat;     //Exibe as divs da conversa
            }
        }
        //Se não houver uma conversa, não retorna nada
        echo 'nothing';     //--DEBUG
    }else if($func == 'send'){      //Se for um request pra enviar uma mensagem:
        $sql = "SELECT stId FROM tbChat "
            . "WHERE (stUser1='$usuarioEmail' && stUser2='$infoUsuarioEmail') || "
                . "(stUser1='$infoUsuarioEmail' && stUser2='$usuarioEmail')";
        $query = $con->query($sql);
        if($query->num_rows==0){     //Se não houver uma conversa entre estes dois usuários:
            //Registra uma nova conversa no BD:
            $sql = "INSERT INTO tbChat VALUES ('$id','$usuarioEmail','$infoUsuarioEmail')";
            $query = $con->query($sql);
            if(!$query){
                echo "Erro no query(registrar conversa): ".mysqli_error($con);
                mysqli_close($con);
                die();
            }
            //Cria o arquivo com as divs de conversa:
            $arquivoNome = "$id.php";
            $arquivoAberto = fopen("chat/$arquivoNome",'x');
            fwrite($arquivoAberto, $message);
            fclose($arquivoAberto);
        }else{      //Caso haja uma conversa entre estes usuários registrada no BD:
            while($dados = $query->fetch_array(MYSQLI_ASSOC)){      //Pega a ID do arquivo:
                $arquivoNome = $dados["stId"].'.php';   //pega o nome do arquivo que está no BD
            }
            //Abre o arquivo para escrita da nova mensagem:
            $arquivoAberto = fopen("chat/$arquivoNome", 'a');
            fwrite($arquivoAberto, $message);
            fclose($arquivoAberto);
        }
    }
    mysqli_close($con);
    
