<?php

$con = mysqli_connect("127.0.0.1", "root", "", "dbrpgstorm", "3306"); 

if(!$con){ 
    echo 'Falha na conexão com o banco de dados.'; 
    echo mysqli_error($con); 
} 

?>

