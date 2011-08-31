<?php

/**
 * @category Zest
 * @package Zest_File
 * @subpackage UnitTests
 */
class Zest_File_DirTest extends Zest_File_AbstractTest{
	
	protected function setUp(){
		$dir = $this->_getPathname();
		if(file_exists($dir)){
			$this->_rmdir($dir);
		}
		mkdir($dir);
	}
	
	public function testBasename(){
		$base = str_replace(DIRECTORY_SEPARATOR, '/', $this->_getPathname());
		$pathname = $base.'/basename';
		
		$dir = new Zest_Dir($pathname);
		$this->assertEquals('basename', $dir->getBasename());
	}
	
	public function testDirname(){
		$base = str_replace(DIRECTORY_SEPARATOR, '/', $this->_getPathname());
		$pathname = $base.'/basename';
		
		$dir = new Zest_Dir($pathname);
		$this->assertEquals($base, $dir->getDirname());
		$this->assertEquals($base, $dir->getPath());
	}
	
	public function testIsReadable(){
		$pathname = $this->_getPathname().'/is_readable';
		
		$dir = new Zest_Dir($pathname);
		$dir->mkdir();
		
		$this->assertTrue($dir->isReadable());
	}
	
//	public function testIsExecutable(){
//		$pathname = $this->_getPathname().'/is_executable';
//		
//		$dir = new Zest_Dir($pathname);
//		$dir->mkdir();
//		
//		$this->assertTrue($dir->isExecutable());
//	}
	
	public function testIsWritable(){
		$pathname = $this->_getPathname().'/is_writable';
		
		$dir = new Zest_Dir($pathname);
		$dir->mkdir();
		
		$this->assertTrue($dir->isReadable());
	}
	
	public function testFileExists(){
		$pathname = $this->_getPathname().'/file_exists';
		
		$dir = new Zest_Dir($pathname);
		$dir->mkdir();
		$this->assertTrue($dir->fileExists());
	}
	
	public function testIsFile(){
		$pathname = $this->_getPathname().'/is_file';
		
		$dir = new Zest_Dir($pathname);
		$dir->mkdir();
		
		$this->assertFalse($dir->isFile());
	}
	
	public function testIsDir(){
		$pathname = $this->_getPathname().'/is_dir';
		
		$dir = new Zest_Dir($pathname);
		$dir->mkdir();
		
		$this->assertTrue($dir->isDir());
	}
	
	public function testMTime(){
		$pathname = $this->_getPathname().'/mtime';
		
		$dir = new Zest_Dir($pathname);
		$dir->mkdir();
		
		$this->assertLessThanOrEqual(time(), $dir->getMTime());
	}
	
	public function testRecursiveMkdirDirname(){
		$pathname = $this->_getPathname().'/mkdir_dirname/test';
		
		$dir = new Zest_Dir($pathname);
		$dir->recursiveMkdirDirname();
		
		$this->assertTrue(file_exists(dirname($pathname)));
	}
	
	public function testPathnameAlternative(){
		$base = str_replace(DIRECTORY_SEPARATOR, '/', $this->_getPathname());
		$pathname = $base.'/alternative';
		
		$dir = new Zest_Dir($pathname);
		$this->assertEquals($pathname, $dir->getPathnameAlternative());
		$dir->mkdir();
		$this->assertEquals($pathname.'_1', $dir->getPathnameAlternative());
	}
	
	public function testRenameOver(){
		$base = str_replace(DIRECTORY_SEPARATOR, '/', $this->_getPathname());
		
		$pathname = $base.'/rename';
		Zest_Dir::factory($pathname)->mkdir();
		touch($pathname.'/file.txt');
		
		$pathname_over = $base.'/rename_over_me';
		Zest_Dir::factory($pathname_over)->mkdir();
		$this->assertTrue(file_exists($pathname_over));
		
		$dir = new Zest_Dir($pathname);
		$dir->rename($pathname_over, zest_dir::RENAME_OVER);
		
		$this->assertFalse(file_exists($pathname));
		$this->assertEquals($pathname_over, $dir->getPathname());
		$this->assertTrue(file_exists($pathname_over));
		$this->assertEquals(1, count($dir->getChildren()));
		
	}
	
//	public function testChmod(){
//		$pathname = $this->_getPathname().'/chmod';
//		
//		$dir = new Zest_Dir($pathname);
//		$dir->mkdir();
//		
//		$this->assertEquals('drwxrwxrwx', $this->_getFileperms($pathname));
//		$dir->chmod(0755);
//		$this->assertEquals('drwxrw-rw-', $this->_getFileperms($pathname));
//	}
	
	public function testFactory(){
		$this->assertInstanceOf('Zest_Dir', Zest_Dir::factory());
		
		$pathname = str_replace(DIRECTORY_SEPARATOR, '/', $this->_getPathname());
		$this->assertEquals($pathname, Zest_Dir::factory($pathname)->getPathname());
	}
	
	public function testSetPathnameTrimSlash(){
		$pathname = str_replace(DIRECTORY_SEPARATOR, '/', $this->_getPathname());
		$dir = new Zest_Dir($pathname.'/');
		$this->assertEquals($pathname, $dir->getPathname());
	}
	
	public function testMkdir(){
		$pathname = $this->_getPathname().'/mkdir';
		Zest_Dir::factory($pathname)->mkdir();
		$this->assertTrue(file_exists($pathname));
	}
	
	public function testRecursiveMkdir(){
		$pathname = $this->_getPathname().'/mkdir_recursive/mkdir_recursive';
		Zest_Dir::factory($pathname)->recursiveMkdir();
		$this->assertTrue(file_exists($pathname));
	}
	
	public function testRmdir(){
		$pathname = $this->_getPathname().'/rmdir';
		$dir = new Zest_Dir($pathname);
		$dir->mkdir();
		$dir->rmdir();
		$this->assertFalse(file_exists($pathname));
	}
	
	public function testRecursiveRmdir(){
		$base = $this->_getPathname();
		
		$pathname = $base.'/rmdir_recursive/rmdir_recursive';
		Zest_Dir::factory($pathname)->recursiveMkdir();
		
		$pathname = $base.'/rmdir_recursive';
		Zest_Dir::factory($pathname)->recursiveRmdir();
		
		$this->assertFalse(file_exists($pathname));
	}
	
	public function testSetPathname(){
		$pathname = str_replace(DIRECTORY_SEPARATOR, '/', $this->_getPathname());
		$dir = new Zest_Dir();
		$dir->setPathname($pathname);
		$this->assertEquals($pathname, $dir->getPathname());
	}
	
	public function testCopyInOver(){
		$base = $this->_getPathname();
		
		$pathname = $base.'/copy_over_recursive/copy_over_recursive';
		$dir = new Zest_Dir($pathname);
		$dir->recursiveMkdir();
		
		$touch_copied = $pathname.'/touch_copied';
		touch($touch_copied);
		
		$pathname_in = $base.'/copy_over';
		Zest_Dir::factory($pathname_in)->recursiveMkdir();
		
		Zest_Dir::factory($pathname_in.'/copy_over_recursive')->recursiveMkdir();
		$this->assertFalse(file_exists($pathname_in.'/copy_over_recursive/touch_copied'));
		
		$touch = $pathname_in.'/touch_copy_over';
		touch($touch);
		
		$dir->copyIn($pathname_in, Zest_Dir::COPY_OVER);
		
		$this->assertTrue(file_exists($touch));
		$this->assertTrue(file_exists($pathname_in.'/copy_over_recursive/touch_copied'));
		$this->assertFalse(file_exists($pathname_in.'/copy_over_recursive/copy_over_recursive'));
	}
	
	public function testCopyInAlternative(){
		$base = $this->_getPathname();
		
		$pathname = $base.'/copy_alternative_recursive/copy_alternative_recursive';
		$dir = new Zest_Dir($pathname);
		$dir->recursiveMkdir();
		
		$pathname_in = $base.'/copy_alternative';
		$dir->copyIn($pathname_in, Zest_Dir::COPY_ALTERNATIVE);
		$dir->copyIn($pathname_in, Zest_Dir::COPY_ALTERNATIVE);
		
		$this->assertTrue(file_exists($pathname_in));
		
		$pathname = $pathname_in.'/copy_alternative_recursive';
		$this->assertTrue(file_exists($pathname));
		$this->assertFalse(file_exists($pathname.'/copy_alternative_recursive'));
		
		$pathname = $pathname_in.'/copy_alternative_recursive_1';
		$this->assertTrue(file_exists($pathname));
		$this->assertFalse(file_exists($pathname.'/copy_alternative_recursive'));
	}
	
	public function testGlob(){
		$base = $this->_getPathname();
		
		$pathname = $base.'/glob_recursive/glob_recursive';
		$dir = new Zest_Dir($pathname);
		$dir->recursiveMkdir();
		
		$dir = new Zest_Dir($base.'/glob_recursive');
		
		$this->assertEquals(0, count($dir->glob('noglob*')));
		$this->assertEquals(1, count($dir->glob('glob_*')));
	}
	
	public function testRecursiveGlob(){
		$base = $this->_getPathname();
		
		$pathname = $base.'/recursive_glob_recursive/recursive_glob_recursive/recursive_glob_recursive';
		$dir = new Zest_Dir($pathname);
		$dir->recursiveMkdir();
		
		$dir = new Zest_Dir($base.'/recursive_glob_recursive');
		
		$this->assertEquals(0, count($dir->recursiveGlob('noglob*')));
		$this->assertEquals(2, count($dir->recursiveGlob('recursive_glob_*')));
		
		foreach($dir->recursiveGlob('recursive_glob_*') as $dir){
			$this->assertInstanceOf('Zest_Dir', $dir);
		}
	}
	
	public function testGetChildren(){
		$base = $this->_getPathname();
		
		$pathname = $base.'/getchildren_recursive/getchildren_recursive';
		$dir = new Zest_Dir($pathname);
		$dir->recursiveMkdir();
		
		$dir = new Zest_Dir($base.'/getchildren_recursive');
		touch($dir->getPathname().'/file.txt');
		
		$children = $dir->getChildren();
		$this->assertEquals(2, count($children));
		
		$file = current($children);
		$dir = next($children);
		
		$this->assertInstanceOf('Zest_File', $file);
		$this->assertInstanceOf('Zest_Dir', $dir);
	}
	
	public function testRecursiveGetChildren(){
		$base = $this->_getPathname();
		
		$pathname = $base.'/recursive_getchildren_recursive/recursive_getchildren_recursive';
		$dir = new Zest_Dir($pathname);
		$dir->recursiveMkdir();
		
		touch($dir->getPathname().'/file.txt');
		$dir = new Zest_Dir($base.'/recursive_getchildren_recursive');
		
		$children = $dir->getChildren();
		$this->assertEquals(1, count($children));
		
		$children = $dir->recursiveGetChildren();
		$this->assertEquals(2, count($children));
		
		$dir = current($children);
		$file = next($children);
		
		$this->assertInstanceOf('Zest_File', $file);
		$this->assertInstanceOf('Zest_Dir', $dir);
	}
	
	public function testForeach(){
		$base = $this->_getPathname();
		
		$pathname = $base.'/foreach_recursive/foreach_recursive';
		$dir = new Zest_Dir($pathname);
		$dir->recursiveMkdir();
		
		$dir = new Zest_Dir($base.'/foreach_recursive');
		foreach($dir as $child){
			$this->assertInstanceOf('Zest_Dir', $child);
		}
	}
	
	public function testGetIterator(){
		$base = $this->_getPathname();
		
		$pathname = $base.'/iterator_recursive/iterator_recursive';
		$dir = new Zest_Dir($pathname);
		
		$this->assertInstanceOf('ArrayIterator', $dir->getIterator());
	}
	
	public function testCleanGarbage(){
		$base = $this->_getPathname();
		
		$pathname = $base.'/garbage';
		$dir = new Zest_Dir($pathname);
		$dir->recursiveMkdir();
		touch($pathname.'/file.txt');
		
		sleep(2);
		$dir->cleanGarbage(1, 1);
		
		$this->assertFalse(file_exists($pathname.'/file.txt'));
	}
	
	public function testGetSize(){
		$base = $this->_getPathname();
		
		$pathname = $base.'/size_recursive/size_recursive';
		$dir = new Zest_Dir($pathname);
		$dir->recursiveMkdir();
		
		$dir = new Zest_Dir($base.'/size_recursive');
		$this->assertEquals(0, $dir->getSize());
		file_put_contents($pathname.'/file.txt', 'testGetSize');
		$this->assertEquals(11, $dir->getSize());
	}
	
	protected function _getPathname(){
		return Zest_AllTests::getTempDir().'/Zest_File_DirTest';
	}
	
}