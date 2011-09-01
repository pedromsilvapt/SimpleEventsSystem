<?php

/*
 
#    This program is free software; you can redistribute it and/or
 
#    modify it under the terms of the GNU General Public License
 
#    as published by the Free Software Foundation; either version 2
 
#    of the License, or (at your option) any later version.
 
#
 
#    This program is distributed in the hope that it will be useful,
 
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
 
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 
#    GNU General Public License for more details.
 
#    http://www.gnu.org/licenses/gpl.txt
 
#
 
*/
 
 
 
 /******
 
 *
 
 * SimpleEventsSystem - a class to emulate events in PHP.
 
 * 
 
 * @author	Scorch (aka Scorchpt or Scorchsw)
 
 * @access	public
 
 * @version	Beta 0.9
 
 * @link	http://centroinfo.iblogger.org/
 
 *
 
 ******/


class Events {

	private static $EventsList;
	
	private static $state = 1;
	
	private static $errors = array();
	
	private static $config = array(
	"Auto_run" => true,
	"Auto_index" => true,
	);
	
	private static $predefindedErrors = array(
		0 => array(
					"id" => 0,
					"title" => "Argumentos Inv�lidos",
					"msg" => "Tem de preencher os argumentos da fun��o correctamente.",
					"function" => ""
					),
		1 => array(
					"id" => 1,
					"title" => "Fun��o n�o declarada",
					"msg" => "A fun��o n�o est� declarada. Se n�o quer receber esta mensagem novamente, provavelmente ter� de desactivar o argumento de verificar fun��es na fun��o Attach.",
					"function" => ""
					),
		2 => array(
					"id" => 2,
					"title" => "Fun��o j� existente",
					"msg" => "A fun��o j� est� anexada a esse evento.",
					"function" => ""
					),
		3 => array(
					"id" => 3,
					"title" => "O evento n�o existe",
					"msg" => "O evento declarado n�o existe. Verifique se o escreveu bem.",
					"function" => ""
					),
		4 => array(
					"id" => 4,
					"title" => "A fun��o n�o existe",
					"msg" => "O evento declarado n�o existe. Verifique se o escreveu bem.",
					"function" => ""
					)
	);
	
	public function confExists($var_name){
		if (isset(self::$config[$var_name])){
			return(true);
		} else {
			return(false);
		}
	}
	
	public function getConf($var_name){
		if (isset(self::$config[$var_name])){
			return(self::$config[$var_name]);
		} else {
			return(false);
		}
	}
	
	public function setConf($var_name, $var_value){
		if (isset(self::$config[$var_name])){
			self::$config[$var_name] = $var_value;
			return(true);
		} else {
			return(false);
		}
	}
	
	/*
	*Esta fun��o "detona" um novo erro.
	*
	*@param id - O id do erro, definido na vari�vel $predefindedErrors
	*@param function - O nome da fun��o em que o erro aconteceu.
	*/
	private function thrownError($id, $function){
		self::$errors[] = self::$predefindedErrors[$id];
		self::$errors[count(self::$errors)]["function"] = $function;
	}
	
	/*
	*Imprime n espa�os em branco
	*
	*@param int number - n�mero de espa�os em branco a imrpimir
	*@return $start_index - Retorna os espa�os em branco em HTML (&nbsp;)
	*/
	private function WhiteSpaces($number){
		if ($number > 0){
			return("&nbsp;".self::WhiteSpaces($number-1));
		}
	}
	
	/*
	*Obt�m o index do pr�ximo espa�o preenchido a come�ar por $start_index
	*dentro do event $event_name
	*
	*@param string event_name - Nome do evento a procurar
	*@param int start_index - O index do espa�o a come�ar a procurar
	*@return $start_index - Retorna o index do pr�ximo espa�o preenchido
	*/
	private function GetNextFill ($event_name, $start_index){
		
		//Corre um ciclo at� encontrar um espa�o preenchido
		for (; self::$EventsList[$event_name][$start_index] == null; $start_index++);
		
		return($start_index);
	}
	
	
	/*
	*Recoloca as func��es do array.
	*
	*Se o parametro @event_name estiver vazio, a fun��o recoloca as fun��es de todos os eventos
	*Se estiver preenchido recoloca apenas as fun��es do evento.
	*
	*@param string opcional event_name - nome do evento a re-colocar.
	*@return true - Retorna true em caso de sucesso
	*@return false - Retorna false caso o evento n�o exista
	*/
	private function Reoder_Array_List ($event_name = ""){
		
		$tempList = array();
		
		//Veriica se o parametro est� declarado
		if ($event_name  == ""){
			
			//Percorre todos os eventos
			foreach (self::$EventsList as $event => $functions){
				
				foreach ($functions as $index => $FunctionName){
					//Verifica se a vari�vel n�o est� vazia
					if (!empty($FunctionName)){
						$tempList[$event][] = $FunctionName;
					}
				}
				
			
			}
			self::$EventsList = $tempList;
		//Verifica se o parametro est� preenchido e se existe esse evento
		} elseif ($event_name != "" && isset(self::$EventsList[$event_name])) {
		
			foreach (self::$EventsList[$event_name] as $index => $FunctionName){
				//Verifica se a vari�vel n�o est� vazia
				if (!empty($FunctionName)){
					$tempList[] = $FunctionName;
				}
			}
			
			self::$EventsList[$event_name] = $tempList;
			
		} else {
			//Retorna false se o evento n�o existir
			return(false);
		}
		
		return(true);
	
	}
	
	/*
	*Esta fun��o permite anexar novos eventos.
	*
	*@param string event_name - O nome do evento a que se vai anexar a fun��o
	*@param string function_name - O nome da fun��o a anexar
	*@param array  function_name - Um array uni-dimensional com os nomes das fun��es a anexar
	*@param boolean opcional verify_function - True se quiser que apenas adicione a fun��o se esta estiver declarada.
	*@return true - Retorna true se adicionou a fun��o correctamente
	*@return false - Retorna false se a framework estiver desabilitada
	*@return false - Retorna false se falhou a verifica��o da fun��o ($verify_function == true)
	*@return false - Retorna false se $event_name ou $function_name estiverem em branco        
	*@return false - Retorna false se a fun��o j� estiver anexada ao evento
	*/
	public function Attach ($event_name, $function_name, $verify_function = false){
		
		if (self::$state == 0){
			return(false);
		}
		
		$error = false;
		
		//Verifica se os parametros est�o preenchidos.
		if ($event_name == "" || $function_name == ""){
			//Detona um novo erro
			self::thrownError(0, "Attach");
			return(false);
		}
		if (is_array($function_name)){
			foreach ($function_name as $FunctionName){
				//Verifica se a op��o de verificar as fun��es est� activada, e se a fun��o n�o est� declarada.
				if ($verify_function == true && !is_callable($FunctionName)){
					self::thrownError(1, "Attach");
					$error = true;
					continue;
				}
		
				//Verifica se o evento ainda n�o existe
				if(!is_array(self::$EventsList[$event_name])){
					//Declara a fun��o ao respectivo evento
					self::$EventsList[$event_name][count(self::$EventsList[$event_name])] = $FunctionName;
				
				} elseif (!in_array($FunctionName, self::$EventsList[$event_name])){
					//Declara a fun��o ao respectivo evento
					self::$EventsList[$event_name][count(self::$EventsList[$event_name])] = $FunctionName;
					
				} else {
					self::thrownError(2, "Attach");
					$error = true;
					continue;
				}
			}	
		} else {		
			//Verifica se a op��o de verificar as fun��es est� activada, e se a fun��o n�o est� declarada.
			if ($verify_function == true && !is_callable($function_name)){
				self::thrownError(1, "Attach");
				return(false);
			}
			
			self::$EventsList[$event_name] = "";
			
			//Verifica se o evento ainda n�o existe
			if(!is_array(self::$EventsList[$event_name])){
				//Declara a fun��o ao respectivo evento
				self::$EventsList[$event_name][] = $function_name;
			
			} elseif (!in_array($function_name, self::$EventsList[$event_name])){
				//Declara a fun��o ao respectivo evento
				self::$EventsList[$event_name][] = $function_name;
				
			} else {
				self::thrownError(2, "Attach");
				return(false);
			}
			
			if ($error == true){
				return(false);
			}
		
			return(true);
		
		}
	}
	
	/*
	*Esta fun��o permite desanexar fun��es ou mesmo apagar eventos para n�o serem mais executados
	*
	*@param string event_name - Nome do evento a apagar/da fun��o a apagar
	*@param string opcional function_name - Fun��o a desanexar
	*@return true - Retorna true em caso de sucesso.
	*@return false - Retorna false se a framework estiver desabilitada
	*@return false - Retorna false se $event_name estiver vazia ou o evento n�o existir
	*/
	public function Detach ($event_name, $function_name = ""){
		
		if (self::$state == 0){
			return(false);
		}
		
		//Verifica se os parametros est�o preenchidos.
		if ($event_name == "" || !isset(self::$EventsList[$event_name])){
			self::thrownError(0, "Detach");
			return(false);
		}
		
		//Se n�o estiver definido nenhum nome de fun��o, apaga todas as fun��es relativas ao evento
		if ($function_name == ""){
			unset(self::$EventsList[$event_name]);
		} else {
			//Verifica se o parametro � um array
			if (is_array($function_name)){
				
				foreach ($function_name as $FunctionName){
					
					//Se o espa�o estiver em branco passa ao seguinte
					if ($FunctionName == ""){
						continue;
					}
					//Sen�o percorre o array do respectivo evento
					foreach (self::$EventsList[$event_name] as $index => $FunctionName){
				
						//Se a fun��o for a definida apaga-a
						if ($FunctionName == $function_name){
							unset(self::$EventsList[$event_name][$index]);
						}
					}
				}
			} else {
			
				//Sen�o percorre o array do respectivo evento
				foreach (self::$EventsList[$event_name] as $index => $FunctionName){
				
					//Se a fun��o for a definida apaga-a
					if ($FunctionName == $function_name){
						unset(self::$EventsList[$event_name][$index]);
					}
				}
			}
		
			
		}
		
		self::Reoder_Array_List();
		return(true);
	}

	/*
	*Esta fun��o executa todas as fun��es anexadas a um evento at� ao momento.
	*
	*@param string event_name - O nome do evento a executar
	*@param array opcional paramsList - Os par�metros a passar para a fun��o
	*@return true - Retorna true se o processo foi conclu�do com sucesso
	*@return false - Retorna false se a framework estiver desabilitada
	*@return false - Retorna false se a vari�vel $event_name estiver vazia
	*@return false - Retorna false se o evento n�o existir
	*/
	public function Run($event_name, $paramsList = ""){
		
		if (self::$state == 0){
			return(false);
		}
		
		//Verifica se $event_name n�o est� vazia
		if ($event_name == ""){
			self::thrownError(0, "Run");
			return(false);
		}
		
		//Verifica se o evento existe
		if (isset(self::$EventsList[$event_name])){
			//Verifica se $paramsList est� vazia ou n�o � um array
			if ($paramsList == "" || !is_array($paramsList)){
				
				foreach (self::$EventsList[$event_name] as $index => $FunctionName){
					//Verifica se a fun��o $FunctionName pode ser chamada
					if (is_callable($FunctionName)){
						//Chama a fun��o
						call_user_func($FunctionName);
					}
				}
			//Verifica se $paramsList n�o estiver vazia e for um array
			} elseif ($paramsList != "" || is_array($paramsList)) {
				foreach (self::$EventsList[$event_name] as $index => $FunctionName){
					//Verifica se a fun��o $FunctionName pode ser chamada
					if (is_callable($FunctionName)){
						//Chama a fun��o e passa os argumentos
						call_user_func_array($FunctionName, $paramsList);
					}
				}
			}
		}
		
		if (self::getConf("Auto_run") == 1){
			//Obt�m as fun��es definidas
			$defined_functions = get_defined_functions();
			
			//Percorre as fun��es definidas pelo utilizador
			foreach ($defined_functions["user"] as $index => $FunctionName){
				self::$EventsList[$event_name] = "";
				
				if (substr($FunctionName, -9 - strlen($event_name)) == "_handles_".$event_name && substr($FunctionName, 0, -9 - strlen($event_name)) != "" && !is_array(self::$EventsList[$event_name])){
					if ($paramsList == "" || !is_array($paramsList)){
						call_user_func($FunctionName);
					} elseif ($paramsList != "" || is_array($paramsList)){
						call_user_func_array($FunctionName, $paramsList);
					}
					
					if (self::getConf("Auto_index") == true){
						self::$EventsList[$event_name][] = $FunctionName;
					}
					
				} elseif (substr($FunctionName, -9 - strlen($event_name)) == "_handles_".$event_name && substr($FunctionName, 0, -9 - strlen($event_name)) != "" && !in_array($FunctionName, self::$EventsList[$event_name])){
					if ($paramsList == "" || !is_array($paramsList)){
						call_user_func($FunctionName);
					} elseif ($paramsList != "" || is_array($paramsList)){
						call_user_func_array($FunctionName, $paramsList);
					}
					
					if (self::getConf("Auto_index") == true){
						self::$EventsList[$event_name][] = $FunctionName;
					}
				}
			}
			return(true);
		} elseif (!isset(self::$EventsList[$event_name])){
			self::thrownError(3, "Run");
			return(false);
		}
		
		return(true);
	}
	
	
	/*
	*Esta fun��o imprime/retorna todas as fun��es anexadas aos eventos at� ao momento
	*Se $event_name estiver definida, mostra apenas as fun��es de determinado evento
	*
	*@param string opcional $event_name - O nome do evento a mostra as fun��es
	*@param bool opcional $output - Se estiver a true, imprime directamente para o ecr� os eventos. Se estiver a false, gera uma vari�vel
	*@return true - Retorna true na aus�ncia de erros e se $output == true
	*@return $return - Retorna a vari�vel $return na aus�ncia de erros e se $output == false
	*@return false - Retorna false se a framework estiver desabilitada
	*@return false - Retorna false caso o evento $event_name n�o exista
	*/
	public function Show ($event_name = "", $output = true){
		
		if (self::$state == 0){
			return(false);
		}
		
		$return = "";
		
		//Verifica se o utilizador especificou algum evento em especifico
		if ($event_name != ""){
			//Verifica se o evento existe
			if (isset(self::$EventsList[$event_name])){
				//Adiciona conte�dos � vari�vel
				$return .= "<b>Evento:</b> ".$event_name."<br />";
				//Percorre todos os espa�o do evento e adiciona-os � vari�vel $return.
				foreach (self::$EventsList[$event_name] as $index => $FunctionName){
					$return .= self::WhiteSpaces(8).$index."- ".$FunctionName."<br />";
				}
				$return .= "}<br />";
			//Se o evento n�o existir retorna false.
			} else {
				self::thrownError(3, "Show");
				return(false);
			}
		//Se n�o for especificado nenhum evento
		} else {
			//Obt�m todos os eventos
			foreach (self::$EventsList as $EventName => $Functions){
				//Adiciona conte�dos � vari�vel
				$return .= "{<b>Evento:</b> ".$EventName."<br />";
				//Percorre todas as fun��es de cada evento
				foreach (self::$EventsList[$EventName] as $index => $FunctionName){
					$return .= self::WhiteSpaces(8).$index."- ".$FunctionName."<br />";
				}
				$return .= "}<br />";
			}
		}
		
		//Verifica se o utilizador ordenou para imprimir
		if ($output == true){
			echo $return;
		//Sen�o retorna a vari�vel
		} else {
			return($return);
		}
		
		return (true);
	}

	/*
	*Esta fun��o verifica se o evento existe, ou se a fun��o est� anexada ao evento.
	*
	*@param strign event_name - O nome do evento a verificar se existe
	*@param string opcional function_name - O nome da fun��o a verificar se est� anexada ao evento
	*@return true - Caso o evento exista
	*@return true - Caso o evento exista e a fun��o esteja anexada a ele
	*@return false - Caso o evento n�o exista
	*@return false - Caso a fun��o n�o esteja anexada ao evento.
	*/
	public function Exist($event_name, $function_name = ""){
		
		if ($function_name == ""){
			if (isset(self::$EventsList[$event_name])){
				return(true);
			} else {
				return(false);
			}
		} else {
			if (is_array(self::$EventsList[$event_name])){
				if (in_array($function_name, self::$EventsList[$event_name])){
					return(true);
				} else {
					return(false);
				}
			} else {
				return(false);
			}
		}
	}
	
	/*
	*Obt�m o array contendo a lista dos eventos e/ou fun��es.
	* NOTA: Devem-se sempre usar as fun��es disponibilizadas pela classe para realizar ac��es que tenham a haver com o array, 
	*       pois a sua estrutura pode mudar e isso fazer com que a aplica��o deixe de funcionar
	*@return $EventsList - Retorna o array com os eventos e as fun��es
	*/
	public function getList(){
		return($EventsList);
	}
	
	/*
	* Esta fun��o desabilita a framework, completamente, at� que esta seja re-habilitada.
	*/
	public function Disable(){
		self::$state = 0;
		return(true);
	}
	
	/*
	* Esta fun��o re-habilita a framework, completamente, at� que esta seja desabilitada.
	*/
	public function Enable(){
		self::$state = 1;
		return(true);
	}
	
	/*
	*Esta fun��o devolve o �ltimo erro ocorrido pela framework.
	*
	*@return false - Caso ainda n�o tenha ocorrido nenhum erro ou o log de erros tenha sido limpo.
	*@return self::$errors[count(self::$errors)] - Caso o log tenha algum registo.
	*/
	public function getLastError(){
		if (count(self::$errors) == 0){
			return(false);
		} else {
			return(self::$errors[count(self::$errors)]);
		}
	}
	
	/*
	*Esta fun��o retorna o array de erros occoridos.
	*
	*@return self::$errors - Retorna o log inteiro
	*/
	public function getErrosLog (){
		return(self::$errors);
	}
	
	/*
	*Esta fun��o apaga o log de erros da classe
	*/
	public function clearErrosLog (){
		unset(self::$errors);
		return(true);
	}
}





?>