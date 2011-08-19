<?php

/**
 * @category Zest
 * @package Zest_File
 * @subpackage UnitTests
 */
class Zest_File_Helper_ImageTest extends PHPUnit_Framework_TestCase{
	
	protected function setUp(){
		$dir = Zest_AllTests::getTempDir().'/Zest_File_Helper_ImageTest';
		if(file_exists($dir)){
			foreach(glob($dir.'/*') as $file){
				unlink($file);
			}
		}
		else{
			mkdir($dir);
		}
	}

	public function testResizeOutside(){
		$file = $this->_getFile('jpg');
		
		$file->resize(array('width' => 100, 'height' => 100, 'fit' => 'outside'));
		// idem $file->resize(array('outside' => 100));
		
		list($width, $height) = $file->image()->getSize();
		$this->assertEquals(320, $width);
		$this->assertEquals(100, $height);
	}

	public function testResizeInside(){
		$file = $this->_getFile('jpg');
		
		$file->resize(array('width' => 100, 'height' => 100, 'fit' => 'inside'));
		// idem $file->resize(array('inside' => 100));
		
		list($width, $height) = $file->image()->getSize();
		$this->assertEquals(100, $width);
		$this->assertEquals(31, $height);
	}

	public function testResizeOutsideNotForced(){
		$file = $this->_getFile('jpg');
		$file->resize(array('outside' => 1000));
		list($width, $height) = $file->image()->getSize();
		$this->assertEquals(800, $width);
		$this->assertEquals(250, $height);
	}

	public function testResizeInsideNotForced(){
		$file = $this->_getFile('jpg');
		$file->resize(array('inside' => 1000));
		list($width, $height) = $file->image()->getSize();
		$this->assertEquals(800, $width);
		$this->assertEquals(250, $height);
	}

	public function testResizeOutsideForced(){
		$file = $this->_getFile('jpg');
		$file->resize(array('outside' => 500, 'forceResize' => true));
		list($width, $height) = $file->image()->getSize();
		$this->assertEquals(1600, $width);
		$this->assertEquals(500, $height);
	}

	public function testResizeInsideForced(){
		$file = $this->_getFile('jpg');
		$file->resize(array('inside' => 1000, 'forceResize' => true));
		list($width, $height) = $file->image()->getSize();
		$this->assertEquals(1000, $width);
		$this->assertEquals(313, $height);
	}
	
	public function testResizeJpg(){
		$file = $this->_getFile('jpg');
		$file->resize(array('inside' => 500));
		$this->assertEquals(
			md5_file(Zest_AllTests::getDataDir().'/file/image_inside_500.jpg'),
			md5_file($file->getPathname())
		);
	}
	
	public function testResizeGif(){
		$file = $this->_getFile('gif');
		$file->resize(array('inside' => 500));
		$this->assertEquals(
			md5_file(Zest_AllTests::getDataDir().'/file/image_inside_500.gif'),
			md5_file($file->getPathname())
		);
	}

	public function testResizeGifTransparent(){
		$file = $this->_getFile('gif', true);
		$file->resize(array('inside' => 500));
		$this->assertEquals(
			md5_file(Zest_AllTests::getDataDir().'/file/image_transparent_inside_500.gif'),
			md5_file($file->getPathname())
		);
	}

	public function testConvertGifToJpg(){
		$file = $this->_getFile('gif');
		$file->resize(array('inside' => 500, 'extension' => 'jpg'));
		$this->assertEquals(
			md5_file(Zest_AllTests::getDataDir().'/file/image_gif_to_jpg.jpg'),
			md5_file($file->getPathname())
		);
	}

	public function testConvertGifTransparentToJpg(){
		$file = $this->_getFile('gif', true);
		$file->resize(array('inside' => 500, 'extension' => 'jpg', 'backgroundColor' => 'ff0000'));
		$this->assertEquals(
			md5_file(Zest_AllTests::getDataDir().'/file/image_transparent_gif_to_jpg.jpg'),
			md5_file($file->getPathname())
		);
	}

	public function testResizePng(){
		$file = $this->_getFile('png');
		$file->resize(array('inside' => 500));
		$this->assertEquals(
			md5_file(Zest_AllTests::getDataDir().'/file/image_inside_500.png'),
			md5_file($file->getPathname())
		);
	}

	public function testResizePngTransparent(){
		$file = $this->_getFile('png', true);
		$file->resize(array('inside' => 500));
		$this->assertEquals(
			md5_file(Zest_AllTests::getDataDir().'/file/image_transparent_inside_500.png'),
			md5_file($file->getPathname())
		);
	}

	public function testConvertPngToJpg(){
		$file = $this->_getFile('png');
		$file->resize(array('inside' => 500, 'extension' => 'jpg'));
		$this->assertEquals(
			md5_file(Zest_AllTests::getDataDir().'/file/image_png_to_jpg.jpg'),
			md5_file($file->getPathname())
		);
	}

	public function testConvertPngTransparentToJpg(){
		$file = $this->_getFile('png', true);
		$file->resize(array('inside' => 500, 'extension' => 'jpg', 'backgroundColor' => 'ff0000'));
		$this->assertEquals(
			md5_file(Zest_AllTests::getDataDir().'/file/image_transparent_png_to_jpg.jpg'),
			md5_file($file->getPathname())
		);
	}

	public function testGetSize(){
		$file = $this->_getFile('jpg');
		list($width, $height) = $file->image()->getSize();
		$this->assertEquals(800, $width);
		$this->assertEquals(250, $height);
	}

	protected function _getFile($extension, $transparent = false){
		$pathname = $this->_getPathname($extension, $transparent);
		$file = new Zest_File($pathname);
		
		$to = Zest_AllTests::getTempDir().'/Zest_File_Helper_ImageTest/'.basename($pathname);
		$file->copy($to);
		$file->setPathname($to);
		
		return $file;
	}
	
	protected function _getPathname($extension, $transparent = false){
		return Zest_AllTests::getDataDir().'/file/image'.($transparent ? '_transparent' : '').'.'.$extension;
	}
	
}