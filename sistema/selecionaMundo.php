<?php session_start(); ?>
<?php include_once 'php/_header.php'; ?>
<?php include_once 'php/banner.php'; ?>
<?php include_once 'php/menu.php'; ?>

<div id="conteudo">
    <h2>Mundos</h2>
    <?php
        include_once 'php/_dbconnect.php';
        $sql = "SELECT * FROM tbMundos m INNER JOIN tbUsuarios u ON m.stCreator = u.stEmail";   //TODO - Criar uma stored function no DB pra isso
        $query = $con->query($sql);
        while($dados = $query->fetch_array(MYSQLI_ASSOC)){
            $mundoNome = $dados["stNome"];
            $mundoCreator = $dados["stNickname"];
            if($dados["blPublic"]){
                $mundoTipo = 'Público';
            }else{
                $mundoTipo = 'Privado';
            }
            echo "<div id='mundoBox' style='border: 1px solid black;'>".
                    "$mundoNome - $mundoTipo - Criado por: $mundoCreator".
            "</div> ";
        }
        mysqli_close($con);
    ?>
</div>
<?php include_once 'php/_footer.php'; ?>

