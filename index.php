<?php   include_once 'php/_header.php'; ?>
<?php   include_once 'php/banner.php';?>
<?php   include_once 'php/menu.php';?>

<div style="text-align: center;">
    <h1>Entrar</h1><br>
    <form class="formLogin" action='index.php' method='post'>
        Email <input type='text' name='inputEmail'><br><br>
        Senha <input type='password' name='inputSenha'><br><br>
        <input type="submit" value="Login">
    </form>
</div>

<?php   //Processamento de dados para login:
    if(isset($_POST["inputEmail"]) && isset($_POST["inputSenha"])){
        include_once 'php/_dbconnect.php';
        $mayLogin = false;
        $email = $_POST["inputEmail"];
        $senha = $_POST["inputSenha"];
        $sql = "SELECT validaLogin('$email','$senha') AS resultado";
        $query = $con->query($sql);
        while($dados = $query->fetch_array(MYSQLI_ASSOC)){ 
            if($dados["resultado"]){
                $mayLogin = true;
            }else{
                echo 'login fail';
            }
        }
        if($mayLogin){  //Inicia a coleta de dados pra sessão
            $sql = "SELECT * FROM tbUsuarios WHERE stEmail = '$email' && stSenha = '$senha'";
            $query = $con->query($sql);
            while($dados = $query->fetch_array(MYSQLI_ASSOC)){
                session_start();    //Inicia a session
                $_SESSION["usuarioEmail"] = $dados["stEmail"];
                $_SESSION["usuarioSenha"] = $dados["stSenha"];
                $_SESSION["usuarioNickname"] = $dados["stNickname"];
                header("location: sistema/selecionaMundo.php");
            }
        }
        mysqli_close($con);
        unset($_POST["inputEmail"]);
        unset($_POST["inputSenha"]);
    }else{
        echo 'nothing set';
    }
?>

<?php include_once 'php/_footer.php'; ?>

