<?php session_start(); ?>
<?php include_once 'php/sessionVerif.php'; ?>
<?php include_once 'php/_header.php'; ?>
<?php include_once 'php/banner.php'; ?>
<?php include_once 'php/menu.php'; ?>

<div class="conteudo">
    <h2>Mundos</h2>
    <?php
        include_once 'php/_dbconnect.php';
        $sql = "SELECT M.stNome AS mNome, M.stId AS mId, M.stCapa AS mCapa, "
                . "U.stNickname AS uNome, M.blPublic AS mPublic "
                . "FROM tbMundos M INNER JOIN tbUsuarios U INNER JOIN tbCenas C "
                . "ON m.stCreator = u.stEmail";   //TODO - Criar uma stored function no DB pra isso
        $query = $con->query($sql);
        while($dados = $query->fetch_array(MYSQLI_ASSOC)){
            $mundoNome = $dados["mNome"];
            $mundoCreator = $dados["uNome"];
            $mundoId = $dados["mId"];
            $mundoCapa = $dados["mCapa"];
            if($dados["mPublic"]){
                $mundoTipo = 'Público';
            }else{
                $mundoTipo = 'Privado';
            }
            echo "<div class='mundoBox' style='border: 1px solid black;'>"
                    . "<img src='mundos/$mundoId/$mundoCapa'>".
                    "<a href='escolhaCena.php?mundo=$mundoId'>$mundoNome</a> - $mundoTipo - Criado por: $mundoCreator".
            "</div> ";
        }
        mysqli_close($con);
    ?>
</div>

<?php include_once 'php/_anuncio.php'; ?>
<?php include_once 'php/_footer.php'; ?>

