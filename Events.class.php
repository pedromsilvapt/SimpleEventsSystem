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
					"title" => "Argumentos Inválidos",
					"msg" => "Tem de preencher os argumentos da função correctamente.",
					"function" => ""
					),
		1 => array(
					"id" => 1,
					"title" => "Função não declarada",
					"msg" => "A função não está declarada. Se não quer receber esta mensagem novamente, provavelmente terá de desactivar o argumento de verificar funções na função Attach.",
					"function" => ""
					),
		2 => array(
					"id" => 2,
					"title" => "Função já existente",
					"msg" => "A função já está anexada a esse evento.",
					"function" => ""
					),
		3 => array(
					"id" => 3,
					"title" => "O evento não existe",
					"msg" => "O evento declarado não existe. Verifique se o escreveu bem.",
					"function" => ""
					),
		4 => array(
					"id" => 4,
					"title" => "A função não existe",
					"msg" => "O evento declarado não existe. Verifique se o escreveu bem.",
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
	*Esta função "detona" um novo erro.
	*
	*@param id - O id do erro, definido na variável $predefindedErrors
	*@param function - O nome da função em que o erro aconteceu.
	*/
	private function thrownError($id, $function){
		self::$errors[] = self::$predefindedErrors[$id];
		self::$errors[count(self::$errors)]["function"] = $function;
	}
	
	/*
	*Imprime n espaços em branco
	*
	*@param int number - número de espaços em branco a imrpimir
	*@return $start_index - Retorna os espaços em branco em HTML (&nbsp;)
	*/
	private function WhiteSpaces($number){
		if ($number > 0){
			return("&nbsp;".self::WhiteSpaces($number-1));
		}
	}
	
	/*
	*Obtém o index do próximo espaço preenchido a começar por $start_index
	*dentro do event $event_name
	*
	*@param string event_name - Nome do evento a procurar
	*@param int start_index - O index do espaço a começar a procurar
	*@return $start_index - Retorna o index do próximo espaço preenchido
	*/
	private function GetNextFill ($event_name, $start_index){
		
		//Corre um ciclo até encontrar um espaço preenchido
		for (; self::$EventsList[$event_name][$start_index] == null; $start_index++);
		
		return($start_index);
	}
	
	
	/*
	*Recoloca as funcções do array.
	*
	*Se o parametro @event_name estiver vazio, a função recoloca as funções de todos os eventos
	*Se estiver preenchido recoloca apenas as funções do evento.
	*
	*@param string opcional event_name - nome do evento a re-colocar.
	*@return true - Retorna true em caso de sucesso
	*@return false - Retorna false caso o evento não exista
	*/
	private function Reoder_Array_List ($event_name = ""){
		
		$tempList = array();
		
		//Veriica se o parametro está declarado
		if ($event_name  == ""){
			
			//Percorre todos os eventos
			foreach (self::$EventsList as $event => $functions){
				
				foreach ($functions as $index => $FunctionName){
					//Verifica se a variável não está vazia
					if (!empty($FunctionName)){
						$tempList[$event][] = $FunctionName;
					}
				}
				
			
			}
			self::$EventsList = $tempList;
		//Verifica se o parametro está preenchido e se existe esse evento
		} elseif ($event_name != "" && isset(self::$EventsList[$event_name])) {
		
			foreach (self::$EventsList[$event_name] as $index => $FunctionName){
				//Verifica se a variável não está vazia
				if (!empty($FunctionName)){
					$tempList[] = $FunctionName;
				}
			}
			
			self::$EventsList[$event_name] = $tempList;
			
		} else {
			//Retorna false se o evento não existir
			return(false);
		}
		
		return(true);
	
	}
	
	/*
	*Esta função permite anexar novos eventos.
	*
	*@param string event_name - O nome do evento a que se vai anexar a função
	*@param string function_name - O nome da função a anexar
	*@param array  function_name - Um array uni-dimensional com os nomes das funções a anexar
	*@param boolean opcional verify_function - True se quiser que apenas adicione a função se esta estiver declarada.
	*@return true - Retorna true se adicionou a função correctamente
	*@return false - Retorna false se a framework estiver desabilitada
	*@return false - Retorna false se falhou a verificação da função ($verify_function == true)
	*@return false - Retorna false se $event_name ou $function_name estiverem em branco        
	*@return false - Retorna false se a função já estiver anexada ao evento
	*/
	public function Attach ($event_name, $function_name, $verify_function = false){
		
		if (self::$state == 0){
			return(false);
		}
		
		$error = false;
		
		//Verifica se os parametros estão preenchidos.
		if ($event_name == "" || $function_name == ""){
			//Detona um novo erro
			self::thrownError(0, "Attach");
			return(false);
		}
		if (is_array($function_name)){
			foreach ($function_name as $FunctionName){
				//Verifica se a opção de verificar as funções está activada, e se a função não está declarada.
				if ($verify_function == true && !is_callable($FunctionName)){
					self::thrownError(1, "Attach");
					$error = true;
					continue;
				}
		
				//Verifica se o evento ainda não existe
				if(!is_array(self::$EventsList[$event_name])){
					//Declara a função ao respectivo evento
					self::$EventsList[$event_name][count(self::$EventsList[$event_name])] = $FunctionName;
				
				} elseif (!in_array($FunctionName, self::$EventsList[$event_name])){
					//Declara a função ao respectivo evento
					self::$EventsList[$event_name][count(self::$EventsList[$event_name])] = $FunctionName;
					
				} else {
					self::thrownError(2, "Attach");
					$error = true;
					continue;
				}
			}	
		} else {		
			//Verifica se a opção de verificar as funções está activada, e se a função não está declarada.
			if ($verify_function == true && !is_callable($function_name)){
				self::thrownError(1, "Attach");
				return(false);
			}
			
			self::$EventsList[$event_name] = "";
			
			//Verifica se o evento ainda não existe
			if(!is_array(self::$EventsList[$event_name])){
				//Declara a função ao respectivo evento
				self::$EventsList[$event_name][] = $function_name;
			
			} elseif (!in_array($function_name, self::$EventsList[$event_name])){
				//Declara a função ao respectivo evento
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
	*Esta função permite desanexar funções ou mesmo apagar eventos para não serem mais executados
	*
	*@param string event_name - Nome do evento a apagar/da função a apagar
	*@param string opcional function_name - Função a desanexar
	*@return true - Retorna true em caso de sucesso.
	*@return false - Retorna false se a framework estiver desabilitada
	*@return false - Retorna false se $event_name estiver vazia ou o evento não existir
	*/
	public function Detach ($event_name, $function_name = ""){
		
		if (self::$state == 0){
			return(false);
		}
		
		//Verifica se os parametros estão preenchidos.
		if ($event_name == "" || !isset(self::$EventsList[$event_name])){
			self::thrownError(0, "Detach");
			return(false);
		}
		
		//Se não estiver definido nenhum nome de função, apaga todas as funções relativas ao evento
		if ($function_name == ""){
			unset(self::$EventsList[$event_name]);
		} else {
			//Verifica se o parametro é um array
			if (is_array($function_name)){
				
				foreach ($function_name as $FunctionName){
					
					//Se o espaço estiver em branco passa ao seguinte
					if ($FunctionName == ""){
						continue;
					}
					//Senão percorre o array do respectivo evento
					foreach (self::$EventsList[$event_name] as $index => $FunctionName){
				
						//Se a função for a definida apaga-a
						if ($FunctionName == $function_name){
							unset(self::$EventsList[$event_name][$index]);
						}
					}
				}
			} else {
			
				//Senão percorre o array do respectivo evento
				foreach (self::$EventsList[$event_name] as $index => $FunctionName){
				
					//Se a função for a definida apaga-a
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
	*Esta função executa todas as funções anexadas a um evento até ao momento.
	*
	*@param string event_name - O nome do evento a executar
	*@param array opcional paramsList - Os parâmetros a passar para a função
	*@return true - Retorna true se o processo foi concluído com sucesso
	*@return false - Retorna false se a framework estiver desabilitada
	*@return false - Retorna false se a variável $event_name estiver vazia
	*@return false - Retorna false se o evento não existir
	*/
	public function Run($event_name, $paramsList = ""){
		
		if (self::$state == 0){
			return(false);
		}
		
		//Verifica se $event_name não está vazia
		if ($event_name == ""){
			self::thrownError(0, "Run");
			return(false);
		}
		
		//Verifica se o evento existe
		if (isset(self::$EventsList[$event_name])){
			//Verifica se $paramsList está vazia ou não é um array
			if ($paramsList == "" || !is_array($paramsList)){
				
				foreach (self::$EventsList[$event_name] as $index => $FunctionName){
					//Verifica se a função $FunctionName pode ser chamada
					if (is_callable($FunctionName)){
						//Chama a função
						call_user_func($FunctionName);
					}
				}
			//Verifica se $paramsList não estiver vazia e for um array
			} elseif ($paramsList != "" || is_array($paramsList)) {
				foreach (self::$EventsList[$event_name] as $index => $FunctionName){
					//Verifica se a função $FunctionName pode ser chamada
					if (is_callable($FunctionName)){
						//Chama a função e passa os argumentos
						call_user_func_array($FunctionName, $paramsList);
					}
				}
			}
		}
		
		if (self::getConf("Auto_run") == 1){
			//Obtém as funções definidas
			$defined_functions = get_defined_functions();
			
			//Percorre as funções definidas pelo utilizador
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
	*Esta função imprime/retorna todas as funções anexadas aos eventos até ao momento
	*Se $event_name estiver definida, mostra apenas as funções de determinado evento
	*
	*@param string opcional $event_name - O nome do evento a mostra as funções
	*@param bool opcional $output - Se estiver a true, imprime directamente para o ecrã os eventos. Se estiver a false, gera uma variável
	*@return true - Retorna true na ausência de erros e se $output == true
	*@return $return - Retorna a variável $return na ausência de erros e se $output == false
	*@return false - Retorna false se a framework estiver desabilitada
	*@return false - Retorna false caso o evento $event_name não exista
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
				//Adiciona conteúdos à variável
				$return .= "<b>Evento:</b> ".$event_name."<br />";
				//Percorre todos os espaço do evento e adiciona-os à variável $return.
				foreach (self::$EventsList[$event_name] as $index => $FunctionName){
					$return .= self::WhiteSpaces(8).$index."- ".$FunctionName."<br />";
				}
				$return .= "}<br />";
			//Se o evento não existir retorna false.
			} else {
				self::thrownError(3, "Show");
				return(false);
			}
		//Se não for especificado nenhum evento
		} else {
			//Obtém todos os eventos
			foreach (self::$EventsList as $EventName => $Functions){
				//Adiciona conteúdos à variável
				$return .= "{<b>Evento:</b> ".$EventName."<br />";
				//Percorre todas as funções de cada evento
				foreach (self::$EventsList[$EventName] as $index => $FunctionName){
					$return .= self::WhiteSpaces(8).$index."- ".$FunctionName."<br />";
				}
				$return .= "}<br />";
			}
		}
		
		//Verifica se o utilizador ordenou para imprimir
		if ($output == true){
			echo $return;
		//Senão retorna a variável
		} else {
			return($return);
		}
		
		return (true);
	}

	/*
	*Esta função verifica se o evento existe, ou se a função está anexada ao evento.
	*
	*@param strign event_name - O nome do evento a verificar se existe
	*@param string opcional function_name - O nome da função a verificar se está anexada ao evento
	*@return true - Caso o evento exista
	*@return true - Caso o evento exista e a função esteja anexada a ele
	*@return false - Caso o evento não exista
	*@return false - Caso a função não esteja anexada ao evento.
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
	*Obtém o array contendo a lista dos eventos e/ou funções.
	* NOTA: Devem-se sempre usar as funções disponibilizadas pela classe para realizar acções que tenham a haver com o array, 
	*       pois a sua estrutura pode mudar e isso fazer com que a aplicação deixe de funcionar
	*@return $EventsList - Retorna o array com os eventos e as funções
	*/
	public function getList(){
		return($EventsList);
	}
	
	/*
	* Esta função desabilita a framework, completamente, até que esta seja re-habilitada.
	*/
	public function Disable(){
		self::$state = 0;
		return(true);
	}
	
	/*
	* Esta função re-habilita a framework, completamente, até que esta seja desabilitada.
	*/
	public function Enable(){
		self::$state = 1;
		return(true);
	}
	
	/*
	*Esta função devolve o último erro ocorrido pela framework.
	*
	*@return false - Caso ainda não tenha ocorrido nenhum erro ou o log de erros tenha sido limpo.
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
	*Esta função retorna o array de erros occoridos.
	*
	*@return self::$errors - Retorna o log inteiro
	*/
	public function getErrosLog (){
		return(self::$errors);
	}
	
	/*
	*Esta função apaga o log de erros da classe
	*/
	public function clearErrosLog (){
		unset(self::$errors);
		return(true);
	}
}





?>