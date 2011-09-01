<?php
require("..\..\Events.php");

if (!defined(IS_AUTH)){
	exit;
}

//Se a variсvel $_GET["event"] nуo estiver declarada, termina a execuчуo da pсgina.
if (!isset($_GET["event"])){
	echo "Nуo foi definido nenhum evento.";
	exit;
}

//Define algumas configuraчѕes para nуo ser necessсrio anexar a funчуo.
Events::setConf("Auto_run", true);
Events::setConf("Auto_index", true);

//Declara uma funчуo anexada ao clique do botуo
function update_file_handles_html_buttons_click_btn_sample (){
	//Vai buscar o nњmero de clicks que o botуo jс tem
	$number_clicks = file_get_contents("clicks.txt");
	
	//Escreve outra vez para o ficheiro adicionando um clique
	file_put_contents("clicks.txt", $number_clicks + 1);
	echo $number_clicks + 1;
}

//Corre todos os eventos associados ao clique do botуo.
Events::Run($_GET["event"]);
?>