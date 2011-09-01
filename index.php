<?php

require("Events.php");

Events::setConf("Auto_run", true);
Events::setConf("Auto_index", true);

function some_function(){
	echo "Olá<br />";
}

function other_function(){
	echo "Olá2<br />";
}

function another_function($arg1, $arg2){
	echo "$arg1 * $arg2 = ".$arg1 * $arg2."<br />";
}

function xpto_handles_new_events (){
	echo "<br />hanldes<br />";
}

function xpto_2_handles_new_events (){
	echo "<br />xpto2<br />";
}

Events::Attach("system.core.event", "some_function");
Events::Attach("system.core.event", "other_function");
Events::Attach("system.core.events", "another_function");
Events::Attach("system.core.events", "banother_function");
Events::Attach("system.core.events", "canother_function");
Events::Attach("system.core.events", "danother_function");
Events::Attach("system.core.events", "eanother_function");
Events::Attach("system.core.events", "fanother_function");
Events::Detach("system.core.events", "danother_function");

echo "<br />".Events::Exist("system.core.events")."<br />";
if (Events::Exist("system.core.events", "fanother_function") == true){
	echo "<br />Olás<br />";
}

//Events::Detach("system.core.event");
Events::Run("system.core.event");
Events::Run("system.core.events", Array(2, 4));
//Events::Show();
Events::Run("new_events");

//Events::Show();




?>