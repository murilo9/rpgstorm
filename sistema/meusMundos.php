<?php session_start(); //Inicia a session
    //Pega os dados necessários para esta página:
    $usuarioEmail = $_SESSION["usuarioEmail"];
?>
<?php include_once 'php/_header.php'; ?>
<?php include_once 'php/banner.php'; ?>
<?php include_once 'php/menu.php'; ?>

<div id="conteudo">
    <h2>Meus Mundos</h2>
    <a href="criaMundo.php">Criar Mundo</a><br><br>
    <?php
        include_once 'php/_dbconnect.php';
        $sql = "SELECT * FROM tbMundos WHERE stCreator = '$usuarioEmail'";
        $query = $con->query($sql);
        if($query->num_rows>0){                     //Caso o usuário possua algum mundo
            while($dados = $query->fetch_array(MYSQLI_ASSOC)){  //Lista os mundos deste usuário
                $mundoNome = $dados["stNome"];
                $mundoId = $dados["stId"];
                if($dados["blPublic"]){
                    $mundoTipo = 'Público';
                }else{
                    $mundoTipo = 'Privado';
                }
                //Pega os staffs:
                $conOld = mysqli_store_result($con);
                
                $sql2 = "SELECT U.stNickname, S.stMundo FROM tbStaffs S INNER JOIN tbUsuarios U ".
                        "ON S.stUsuario = U.stEmail ".
                        "WHERE S.stMundo = '$mundoId'";
                $query2 = $con->query($sql2);
                $staffList = "";
                if($query2){
                    while($dados2 = $query2->fetch_array(MYSQLI_ASSOC)){
                        $staffList .= $dados2["stNickname"]." ";
                    }
                }else{
                    $staffList = mysqli_error($con);
                }
                
                //Pega a quantidade de cenas:
                $sql2 = "SELECT COUNT(stId) FROM tbCenas WHERE stMundo ='$mundoId' AS result";
                $query2 = $con->query($sql2);
                $cenasQntd = 0;
                if($query2){
                    while($dados2 = $query2->fetch_array(MYSQLI_ASSOC)){
                        $cenasQntd = $dados2["result"];
                    }
                }
                //Exibe os dados do mundo
                echo "<div id='mundoBox'>".
                        "<b>$mundoNome</b> <br>$mundoTipo<br>Staffs: $staffList<br>Cenas: $cenasQntd".
                    "</div>";
            }
        }else{                                      //Caso o usuário não possua nenhum mundo
            echo 'Você não possui nenhum mundo';
        }
        mysqli_close($con);
    ?>
</div>

<?php include_once 'php/_footer.php'; ?>