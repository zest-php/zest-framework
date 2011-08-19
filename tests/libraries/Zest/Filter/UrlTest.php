<?php

/**
 * @category Zest
 * @package Zest_Filter
 * @subpackage UnitTests
 */
class Zest_Filter_UrlTest extends PHPUnit_Framework_TestCase{
	
	/**
	 * @var string
	 */
	protected $_words = 'des mots avec des caractères spéciaux et des apostrophes : #1 " % / ! et pour finir un espace ';
	
	public function testFilter(){
		$filter = new Zest_Filter_Url();
		$filtered = $filter->filter($this->_words);
		$this->assertEquals('des-mots-avec-des-caracteres-speciaux-et-des-apostrophes-1-et-pour-finir-un-espace', $filtered);
	}
	
	public function testSpaceChar(){
		$filter = new Zest_Filter_Url();
		$filtered = $filter->setSpaceChar('_')->filter($this->_words);
		$this->assertEquals('des_mots_avec_des_caracteres_speciaux_et_des_apostrophes_1_et_pour_finir_un_espace', $filtered);
	}
	
}