<?php session_start(); ?>
<?php include_once 'php/sessionVerif.php'; 
    //Pega os dados necessários para esta página:
    $usuarioEmail = $_SESSION["usuarioEmail"];
    $usuarioNome = $_SESSION["usuarioNickname"];
?>
<?php include_once 'php/_header.php'; ?>
<?php include_once 'php/banner.php'; ?>
<?php include_once 'php/menu.php'; ?>

<div class="conteudo" style="text-align: center;">
    <form class="formulario" action="conta.php" method="post">
        <?php
            echo "<h3>$usuarioNome</h3>";
        ?>
        <b>Atualizar Senha</b><br><br>
        Senha Atual:  <input type="password" name="inputSenhaAtual"><br><br>
        Senha Nova:  <input type="password" id="senhaNova" name="inputSenhaNova"><br><br>
        Confirmar Senha Nova:  <input type="password" id="senhaNovaC" name="inputSenhaNovaC"><br><br>
        <input type="submit" value="Atualizar"><br><br>
        <text style="color: red;">
        <?php   //Tratamento dos dados de formulário:
            if(isset($_POST["inputSenhaAtual"]) && isset($_POST["inputSenhaNova"]) && isset($_POST["inputSenhaNovaC"])){
                $senhaAtual = $_POST["inputSenhaAtual"];
                $senhaNova = $_POST["inputSenhaNova"];
                $senhaNovaC = $_POST["inputSenhaNovaC"];
                $continue = true;
                if(strlen($senhaNova)<8){
                    echo 'A nova senha deve ter pelo menos 8 caracteres<br>';
                    $continue = false;
                }
                if($senhaNova !== $senhaNovaC){
                    echo 'As novas senhas não coincidem.<br>';
                    $continue = false;
                }
                include_once 'php/_dbconnect.php';
                $sql = "SELECT stSenha FROM tbUsuarios WHERE stSenha = '$senhaAtual' && stEmail = '$usuarioEmail'";
                $query = $con->query($sql);
                if($query->num_rows==0){
                    echo 'A senha atual digitada não está correta.<br>';
                    $continue = false;
                }
                if($continue){
                    $sql = "UPDATE tbUsuarios SET stSenha = '$senhaNova' WHERE stEmail = '$usuarioEmail'";
                    $query = $con->query($sql);
                    if(!$query){
                        echo 'Erro durante a atualização no banco de dados.<br>';
                    }else{
                        echo 'Senha modificada com sucesso.<br>';
                    }
                }
                unset($_POST["inputSenhaAtual"]);
                unset($_POST["inputSenhaNova"]);
                unset($_POST["inputSenhaNovaC"]);
                mysqli_close($con);
            }
        ?>
        </text>
    </form>
</div>
<?php include_once 'php/_footer.php'; ?>

