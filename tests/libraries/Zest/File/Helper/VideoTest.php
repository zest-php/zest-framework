<?php

/**
 * @category Zest
 * @package Zest_File
 * @subpackage UnitTests
 */
class Zest_File_Helper_VideoTest extends PHPUnit_Framework_TestCase{

	public function testCommand(){
		$file = new Zest_File($this->_getPathname());
		$command = $file->getCommand(array(
			// audio
			'audioFrequency' => '44100 Hz',
			'isAudioDisable' => true,
			'audioBitRate' => '64k',		// bit/s
			'audioCodec' => 'codec',
			'audioPreset' => 'preset',
			
			// video
			'frameRate' => 25,
			'size' => '640x480',
			'crop' => array('top' => 10, 'right' => 10, 'bottom' => 10, 'left' => 10),
			'pad' => array('top' => 10, 'right' => 10, 'bottom' => 10, 'left' => 10),
			'maxRate' => '4000k',
			'minRate' => '4000k',
			'bufferSize' => '1835k',
			'isSameQuality' => true,
			'isVideoDisable' => true,
			'videoBitRate' => '200 kb/s',
			'videoCodec' => 'codec',
			'videoPreset' => 'preset',
		
			'format' => 'flv',
			'isOverwrite' => true,
			'duration' => 1,				// ou 'duration' => '00:00:01'
			'fileSizeLimit' => 10000000,	// bytes
			'seek' => 1,					// ou 'seek' => '00:00:01'
		), $this->_getDestination());
		
		// audio
		$this->assertContains(' -ar "44100 Hz" ', $command);	// audioFrequency
		$this->assertContains(' -an ', $command);				// isAudioDisable
		$this->assertContains(' -ab "64k" ', $command);			// audioBitRate
		$this->assertContains(' -acodec "codec" ', $command);	// audioCodec
		$this->assertContains(' -apre "preset" ', $command);	// audioPreset
		
		// video
		$this->assertContains(' -r 25 ', $command);				// frameRate
		$this->assertContains(' -s "640x480" ', $command);		// size
		$this->assertContains(' -croptop 10 ', $command);		// crop
		$this->assertContains(' -cropright 10 ', $command);		// crop
		$this->assertContains(' -cropbottom 10 ', $command);	// crop
		$this->assertContains(' -cropleft 10 ', $command);		// crop
		$this->assertContains(' -padtop 10 ', $command);		// pad
		$this->assertContains(' -padright 10 ', $command);		// pad
		$this->assertContains(' -padbottom 10 ', $command);		// pad
		$this->assertContains(' -padleft 10 ', $command);		// pad
		$this->assertContains(' -maxrate "4000k" ', $command);	// maxRate
		$this->assertContains(' -minrate "4000k" ', $command);	// minRate
		$this->assertContains(' -bufsize "1835k" ', $command);	// bufferSize
		$this->assertContains(' -sameq ', $command);			// isSameQuality
		$this->assertContains(' -vn ', $command);				// isVideoDisable
		$this->assertContains(' -b "200 kb/s" ', $command);		// videoBitRate
		$this->assertContains(' -vcodec "codec" ', $command);	// videoCodec
		$this->assertContains(' -vpre "preset" ', $command);	// videoPreset
		
		$this->assertContains(' -f "flv" ', $command);			// format
		$this->assertContains(' -y ', $command);				// isOverwrite
		$this->assertContains(' -t 1 ', $command);				// duration
		$this->assertContains(' -fs 10000000 ', $command);		// fileSizeLimit
		$this->assertContains(' -ss 1 ' , $command);			// seek
	}
	
	protected function _getPathname(){
		return Zest_AllTests::getDataDir().'/file/video.flv';
	}
	
	protected function _getDestination(){
		return Zest_AllTests::getTempDir().'/Zest_File_Helper_VideoTest/converted.flv';
	}
	
}