<?php

/**
 * @category Zest
 * @package Zest_File
 * @subpackage UnitTests
 */
class Zest_File_Helper_AudioTest extends PHPUnit_Framework_TestCase{

	public function testCommand(){
		$file = new Zest_File($this->_getPathname());
		$command = $file->getCommand(array(
			'audioFrequency' => '44100 Hz',
			'isAudioDisable' => true,
			'audioBitRate' => '64k',		// bit/s
			'audioCodec' => 'codec',
			'audioPreset' => 'preset',
		
			'format' => 'mp3',
			'isOverwrite' => true,
			'duration' => 1,				// ou 'duration' => '00:00:01'
			'fileSizeLimit' => 10000000,	// bytes
			'seek' => 1,					// ou 'seek' => '00:00:01'
		), $this->_getDestination());
		
		$this->assertContains(' -ar "44100 Hz" ', $command);	// audioFrequency
		$this->assertContains(' -an ', $command);				// isAudioDisable
		$this->assertContains(' -ab "64k" ', $command);			// audioBitRate
		$this->assertContains(' -acodec "codec" ', $command);	// audioCodec
		$this->assertContains(' -apre "preset" ', $command);	// audioPreset
		
		$this->assertContains(' -f "mp3" ', $command);			// format
		$this->assertContains(' -y ', $command);				// isOverwrite
		$this->assertContains(' -t 1 ', $command);				// duration
		$this->assertContains(' -fs 10000000 ', $command);		// fileSizeLimit
		$this->assertContains(' -ss 1 ' , $command);			// seek
	}
	
	protected function _getPathname(){
		return Zest_AllTests::getDataDir().'/file/audio.mp3';
	}
	
	protected function _getDestination(){
		return Zest_AllTests::getTempDir().'/Zest_File_Helper_AudioTest/converted.mp3';
	}
	
}