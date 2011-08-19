<?php

/**
 * @category Zest
 * @package Zest_File
 * @subpackage UnitTests
 */
class Zest_File_Helper_CsvTest extends PHPUnit_Framework_TestCase{

	/**
	 * @var Zest_File
	 */
	protected $_file = null;
	
	protected function setUp(){
		$this->_file = new Zest_File($this->_getPathname());
	}

	public function testGetHeaderHasHeader(){
		$header = $this->_file->getHeader();
		
		$compare = array('Prenom', 'Nom', 'Rue', 'Code postal + Ville');
		$compare = array_map('strtolower', $compare);
		
		$this->assertEquals($compare, $header);
	}

	public function testGetHeaderHasNoHeader(){
		$this->_file->setHasHeader(false);
		$header = $this->_file->getHeader();
		$this->assertEquals(array(), $header);
	}

	public function testSetHeader(){
		$this->_file->setHeader(array('prenom', 'nom', 'rue', 'code postal et ville'));
		$data = $this->_file->fgetcsv();
		$row = current($data);
		$this->assertArrayHasKey('prenom', $row);
		$this->assertArrayHasKey('nom', $row);
		$this->assertArrayHasKey('rue', $row);
		$this->assertArrayHasKey('code postal et ville', $row);
	}

	public function testFgetcsv(){
		$data = $this->_file->fgetcsv();
		$this->assertEquals(5, count($data));
		foreach($data as $row){
			$this->assertArrayHasKey('prenom', $row);
			$this->assertArrayHasKey('nom', $row);
			$this->assertArrayHasKey('rue', $row);
			$this->assertArrayHasKey('code postal + ville', $row);
		}
	}

	public function testFputcsv(){
		$file = new Zest_File(Zest_AllTests::getTempDir().'/Zest_File_Helper_CsvTest/testFputcsv.csv');
		$file->unlink();
		$file->touch();
		
		$file->setHeader(array('prenom', 'nom'));
		$file->fputcsv(array(
			array('Clémence', 'Therrien')
		));
		
		$compare = "prenom;nom\nClémence;Therrien\n";
		$this->assertEquals($compare, $file->getContents());
		
		// append
		$file->fputcsv(array(
			array('Merlin', 'Lazure')
		));
		$compare = "prenom;nom\nClémence;Therrien\nMerlin;Lazure\n";
		$this->assertEquals($compare, $file->getContents());
		
		// overwrite
		$file->fputcsv(array(
			array('Merlin', 'Lazure')
		), false);
		$compare = "prenom;nom\nMerlin;Lazure\n";
		$this->assertEquals($compare, $file->getContents());
	}

	public function testFputcsvKeys(){
		$file = new Zest_File(Zest_AllTests::getTempDir().'/Zest_File_Helper_CsvTest/testFputcsvKeys.csv');
		$file->unlink();
		$file->touch();
		
		$file->setHeader(array('prenom', 'nom'));
		$file->fputcsv(array(
			array('nom' => 'Therrien', 'prenom' => 'Clémence')
		));
		
		$compare = "prenom;nom\nClémence;Therrien\n";
		$this->assertEquals($compare, $file->getContents());
	}
	
	protected function _getPathname(){
		return Zest_AllTests::getDataDir().'/file/csv.csv';
	}
	
}