<?php session_start(); ?>
<?php include_once 'php/_header.php'; ?>
<?php include_once 'php/banner.php'; ?>
<?php include_once 'php/menu.php'; ?>

<div class="conteudo">
    <h1>Notificações</h1>
    <?php
        $usuarioEmail = $_SESSION["usuarioEmail"];
        include 'php/_dbconnect.php';
        
        mysqli_close($con);
    ?>
</div>

<?php include_once 'php/_footer.php'; ?>