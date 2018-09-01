<?php   include_once 'php/_header.php'; ?>
<h1>RpgStorm - Home</h1>
<form action='index.php' method='post'>
    Email: <input type='text' name='inputEmail'><br>
    Senha: <input type='password' name='inputSenha'><br>
    <input type="submit" value="Login">
</form>

<?php   //Processamento de dados para login:
    if(isset($_POST["inputEmail"]) && isset($_POST["inputSenha"])){
        include_once 'php/_dbconnect.php';
        $email = $_POST["inputEmail"];
        $senha = $_POST["inputSenha"];
        $sql = "SELECT validaLogin('$email','$senha') AS resultado";
        $query = $con->query($sql);
        while($dados = $query->fetch_array(MYSQLI_ASSOC)){ 
            if($dados["resultado"]){
                echo 'login success';
            }else{
                echo 'login fail';
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

