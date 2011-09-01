<?php 
require("..\..\Events.php");

//Inicia a configura��o
Events::setConf("Auto_run", true);
Events::setConf("Auto_index", true);

//Carrega os nomes ficheiros dos plugins.
$plugins = scandir("plugins");

//Percorre o array com todos os ficheiros de plugins
foreach ($plugins as $index=> $file_name){
	//Verifica se o index actual � um ficheiro e n�o uma pasta
	if (is_file("plugins/".$file_name)){
		//Inclui e executa o plugin
		require("plugins/".$file_name);
		//Por uma quest�o de seguran�a e para n�o comprometer o funcionamento da aplica��o
		//Depois de se carregar um plugin deve-se sempre habilitar a classe
		//Para n�o se dar o caso de algum plugin a desabilitar e n�o a habilitar novamente.
		Events::Enable();
	}
}


echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8856-16" />';
	//Declara uma vari�vel que permitir� adicionar conte�do dentro dos plugins para a p�gina
	$content = "";
	//Corre todos os eventos anexados para quando os headers HTML s�o mostrados
	//Esta � apenas um exemplo da sintaxe para o nome dos eventos.
	Events::Run("html_headers");
	echo $content;
	echo '<title>';
	
	$content = "PHP-SES Plugins Example";
	
	Events::Run("html_show_title");
	echo $content.'</title>
</head><body>';

echo "<h1>T�tulo</h1>";

$content = "<a href=\"#\">Link1</a><br />";
//Aqui corre os eventos que permitem alterar o menu
Events::Run("html_generic_body_menu");

echo $content;
echo '</body>
</html>
'


?>