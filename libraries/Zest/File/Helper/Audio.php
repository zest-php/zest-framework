<?php

/**
 * @category Zest
 * @package Zest_File
 * @subpackage Helper
 */
class Zest_File_Helper_Audio extends Zest_File_Helper_Abstract_FFmpeg{
	
	/**
	 * @param integer|string $frequency
	 * @return Zest_File_Helper_Audio
	 */
	protected function _setAudioFrequency($frequency){
		return $this->_setCommand('ar', $frequency);
	}
	
	/**
	 * @param boolean $disable
	 * @return Zest_File_Helper_Audio
	 */
	protected function _setIsAudioDisable($disable = true){
		return $this->_setIsCommand('an', $disable);
	}
	
	/**
	 * @param integer|string $rate
	 * @return Zest_File_Helper_Audio
	 */
	protected function _setAudioBitRate($rate){
		return $this->_setCommand('ab', $rate);
	}
	
	/**
	 * @param string $codec
	 * @return Zest_File_Helper_Audio
	 */
	protected function _setAudioCodec($codec){
		return $this->_setCommand('acodec', $codec);
	}
	
	/**
	 * @param string $preset
	 * @return Zest_File_Helper_Audio
	 */
	protected function _setAudioPreset($preset){
		return $this->_setCommand('apre', $preset);
	}
	
}