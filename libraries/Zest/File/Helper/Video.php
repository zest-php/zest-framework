<?php

/**
 * @category Zest
 * @package Zest_File
 * @subpackage Helper
 */
class Zest_File_Helper_Video extends Zest_File_Helper_Audio{
	
	/**
	 * @param integer $fr
	 * @return Zest_File_Helper_Video
	 */
	protected function _setFrameRate($fr){
		return $this->_setCommand('r', $fr);
	}
	
	/**
	 * @param string $size
	 * @return Zest_File_Helper_Video
	 */
	protected function _setSize($size){
		return $this->_setCommand('s', $size);
	}
	
	/**
	 * @param array $crops
	 * @return Zest_File_Helper_Video
	 */
	protected function _setCrop(array $crops){
		return $this->_setCropPad('crop', $crops);
	}
	
	/**
	 * @param array $pads
	 * @return Zest_File_Helper_Video
	 */
	protected function _setPad(array $pads){
		return $this->_setCropPad('pad', $pads);
	}
	
	/**
	 * @param integer|string $rate
	 * @return Zest_File_Helper_Video
	 */
	protected function _setMaxRate($rate){
		return $this->_setCommand('maxrate', $rate);
	}
	
	/**
	 * @param integer|string $rate
	 * @return Zest_File_Helper_Video
	 */
	protected function _setMinRate($rate){
		return $this->_setCommand('minrate', $rate);
	}
	
	/**
	 * @param integer|string $bufsize
	 * @return Zest_File_Helper_Video
	 */
	protected function _setBufferSize($bufsize){
		return $this->_setCommand('bufsize', $bufsize);
	}
	
	/**
	 * @param boolean $same
	 * @return Zest_File_Helper_Video
	 */
	protected function _setIsSameQuality($same = true){
		return $this->_setIsCommand('sameq', $same);
	}
	
	/**
	 * @param boolean $disable
	 * @return Zest_File_Helper_Video
	 */
	protected function _setIsVideoDisable($disable = true){
		return $this->_setIsCommand('vn', $disable);
	}
	
	/**
	 * @param integer|string $br
	 * @return Zest_File_Helper_Video
	 */
	protected function _setVideoBitRate($br){
		return $this->_setCommand('b', $br);
	}
	
	/**
	 * @param string $codec
	 * @return Zest_File_Helper_Video
	 */
	protected function _setVideoCodec($codec){
		return $this->_setCommand('vcodec', $codec);
	}
	
	/**
	 * @param string $preset
	 * @return Zest_File_Helper_Video
	 */
	protected function _setVideoPreset($preset){
		return $this->_setCommand('vpre', $preset);
	}
	
}