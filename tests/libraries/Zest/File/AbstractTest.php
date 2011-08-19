<?php

/**
 * @category Zest
 * @package Zest_File
 * @subpackage UnitTests
 */
abstract class Zest_File_AbstractTest extends PHPUnit_Framework_TestCase{
	
	protected function _rmdir($dir){
		foreach(glob($dir.'/*') as $file){
			if(is_file($file)){
				unlink($file);
			}
			else{
				$this->_rmdir($file);
			}
		}
		rmdir($dir);
	}
	
	protected function _getFileperms($pathname){
		$perms = fileperms($pathname);
		
		if(($perms & 0xC000) == 0xC000){
			// Socket
			$info = 's';
		}
		else if(($perms & 0xA000) == 0xA000){
			// Lien symbolique
			$info = 'l';
		}
		else if(($perms & 0x8000) == 0x8000){
			// Régulier
			$info = '-';
		}
		else if(($perms & 0x6000) == 0x6000){
			// Block special
			$info = 'b';
		}
		else if(($perms & 0x4000) == 0x4000){
			// Dossier
			$info = 'd';
		}
		else if(($perms & 0x2000) == 0x2000){
			// Caractère spécial
			$info = 'c';
		}
		else if(($perms & 0x1000) == 0x1000){
			// pipe FIFO
			$info = 'p';
		}
		else {
			// Inconnu
			$info = 'u';
		}
		
		// Autres
		$info .=(($perms & 0x0100) ? 'r' : '-');
		$info .=(($perms & 0x0080) ? 'w' : '-');
		$info .=(($perms & 0x0040) ?(($perms & 0x0800) ? 's' : 'x' ) :(($perms & 0x0800) ? 'S' : '-'));
		
		// Groupe
		$info .=(($perms & 0x0020) ? 'r' : '-');
		$info .=(($perms & 0x0010) ? 'w' : '-');
		$info .=(($perms & 0x0008) ?(($perms & 0x0400) ? 's' : 'x' ) :(($perms & 0x0400) ? 'S' : '-'));
		
		// Tout le monde
		$info .=(($perms & 0x0004) ? 'r' : '-');
		$info .=(($perms & 0x0002) ? 'w' : '-');
		$info .=(($perms & 0x0001) ?(($perms & 0x0200) ? 't' : 'x' ) :(($perms & 0x0200) ? 'T' : '-'));
					
		return $info;
	}
	
}