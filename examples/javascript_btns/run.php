<?php
require("..\..\Events.php");

if (!defined(IS_AUTH)){
	exit;
}

//Se a vari�vel $_GET["event"] n�o estiver declarada, termina a execu��o da p�gina.
if (!isset($_GET["event"])){
	echo "N�o foi definido nenhum evento.";
	exit;
}

//Define algumas configura��es para n�o ser necess�rio anexar a fun��o.
Events::setConf("Auto_run", true);
Events::setConf("Auto_index", true);

//Declara uma fun��o anexada ao clique do bot�o
function update_file_handles_html_buttons_click_btn_sample (){
	//Vai buscar o n�mero de clicks que o bot�o j� tem
	$number_clicks = file_get_contents("clicks.txt");
	
	//Escreve outra vez para o ficheiro adicionando um clique
	file_put_contents("clicks.txt", $number_clicks + 1);
	echo $number_clicks + 1;
}

//Corre todos os eventos associados ao clique do bot�o.
Events::Run($_GET["event"]);
?>