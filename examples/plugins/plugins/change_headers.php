<?php

//Uma maneira simples e pr�tica de se anexar uma fun��o � classe
function on_headers_handles_html_headers (){
	Global $content;
	
	$content .= '<meta name="description" content="Exemplo da Classe PHP-SES com plugins." />';
}

function on_headers_show_keywords_handles_html_headers(){
	Global $content;
	
	$content .= '<meta name="keywords" content="PHP, OOP, Plugins, Exemplo, PHP-SES, OpenSource" />
';
}

?>