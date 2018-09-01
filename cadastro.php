<?php include_once 'php/_header.php'; ?>
<?php   include_once 'php/banner.php';?>
<?php   include_once 'php/menu.php';?>
<div style="text-align: center;">
    <h1>Criar Conta</h1>
    Insira seu email e defina um nickname e senha:<br><br>
    <form id="formLogin" action='cadastro.php' method="post">
        Email <input type="text" name='inputEmail'><br><br>
        Nickname <input type="text" name='inputNickname'><br><br>
        Senha <input type="password" name='inputSenha'><br><br>
        <input type="submit" value='Cadastrar'>
    </form>
</div>


<?php
    if(isset($_POST["inputEmail"]) && isset($_POST["inputSenha"]) && isset($_POST["inputNickname"])){
        include_once 'php/_dbconnect.php';
        $email = $_POST["inputEmail"];
        $senha = $_POST["inputSenha"];
        $nickname = $_POST["inputNickname"];
        $sql = "SELECT stEmail FROM tbUsuarios WHERE stEmail='$email'";
        $query = $con->query($sql);
        if($query->num_rows>0){
            echo 'Email já cadastrado<br>';
        }
        $sql = "SELECT stNickname FROM tbUsuarios WHERE stNickname='$nickname'";
        $query = $con->query($sql);
        if($query->num_rows>0){
            echo 'Nickname já utilizado<br>';
        }
        $sql = "INSERT INTO tbUsuarios VALUES ('$email','$nickname','$senha')";
        $query = $con->query($sql);
        if($query){
            echo 'insert success';
        }else{
            echo 'insert failed';
        }
        mysqli_close($con);
        unset($_POST["inputEmail"]);
        unset($_POST["inputSenha"]);
    }else{
        echo 'noting set';
    }
?>

<?php include_once 'php/_footer.php'; ?>