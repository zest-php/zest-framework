<?php

/**
 * @category Zest
 * @package Zest_Controller
 */
abstract class Zest_Controller_Cron extends Zest_Controller_Action{

	/**
	 * @var array
	 */
	protected $_schedules = array();
	
	/**
	 * @param string $action
	 * @param string $time http://fr.wikipedia.org/wiki/Crontab#Syntaxe
	 * @return void
	 */
	public function schedule($action, $time){
		$this->_schedules[$action] = $time;
	}
	
	/**
	 * @return void
	 */
	public function executeAction(){
		$this->_disableRender();
		
		$this->_actionToStack('finish');
		
		$schedules = array_reverse($this->_schedules);
		foreach($schedules as $action => $time){
			if($this->_canExecute($time)){
				$explode = explode('/', $action);
				$explode = array_pad($explode, -3, null);
				list($module, $controller, $action) = $explode;
				$this->_actionToStack($action, $controller, $module);
			}
		}
	}
	
	/**
	 * @return void
	 */
	public function finishAction(){
		echo 'finish';
	}
	
	/**
	 * @param string $time
	 * @return boolean
	 */
	protected function _canExecute($time){
		$values = explode(' ', date('i, H, d, m, w'));
		$values = array_map('intval', $values);
		
		$time = explode(' ', $time);
		
		if(count($values) != count($time)){
			trigger_error('erreur de syntaxe', E_USER_WARNING);
			return false;
		}
		
		$totalValid = 0;
		foreach($time as $key => $timePart){
		
			$value = $values[$key];
			
			if(is_int(strpos($timePart, ',')) && is_int(strpos($timePart, '/')) || substr_count($timePart, '/') > 1){
				trigger_error('erreur de syntaxe', E_USER_WARNING);
				return false;
			}
			
			$isValid = false;
			
			$virgule = explode(',', $timePart);
			foreach($virgule as $virgulePart){
				if(!$virgulePart) continue;
				
				$virguleValid = false;
				
				// toutes les x unités de temps
				if(is_int(strpos($virgulePart, '/'))){
					
					list($moduloPart1, $moduloPart2) = explode('/', $virgulePart);
					
					// les unités de temps de x1 à x2
					if(is_int(strpos($moduloPart1, '-'))){
						
						list($betweenPart1, $betweenPart2) = explode('-', $moduloPart1);
						if($value >= $betweenPart1 && $value <= $betweenPart2){
							$moduloPart1 = $value-$moduloPart1;
						}
						else{							
							$virguleValid = false;
							break;
						}
					}
					else if($moduloPart1 === '*'){
						$moduloPart1 = $value;
					}
					else{
						trigger_error('erreur de syntaxe', E_USER_WARNING);
					}
						
					$virguleValid = $moduloPart1%$moduloPart2 == 0;
					
				}
				
				// les unités de temps de x1 à x2
				else if(is_int(strpos($virgulePart, '-'))){
						
					list($betweenPart1, $betweenPart2) = explode('-', $virgulePart);
					if($value >= $betweenPart1 && $value <= $betweenPart2){
						$virguleValid = true;
					}
					
				}
				
				// les unités de temps égale à x
				else if($virgulePart === '*' || $value == $virgulePart){
					$virguleValid = true;
				}
				
				if($virguleValid){
					$isValid = true;
					break;
				}
				
			}
			
			if($isValid){
				$totalValid++;
			}
			
		}
		
		return $totalValid == count($time);
	}
	
}