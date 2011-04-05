<?php

/**
 * documentation : http://ffmpeg.org/ffmpeg-doc.html#SEC8
 * 
 * @category Zest
 * @package Zest_File
 * @subpackage Helper
 */
class Zest_File_Helper_Abstract_FFmpeg extends Zest_File_Helper_Abstract_Convertable{
	
	/**
	 * @var ffmpeg_movie
	 */
	protected $_ffmpeg = null;
	
	/**
	 * @var array
	 */
	protected $_command = null;
	
	/**
	 * @return ffmpeg_movie
	 */
	protected function _getFfmpegMovie(){
		if(is_null($this->_ffmpeg)){
//			if(!extension_loaded('ffmpeg')){
//				throw new Zest_File_Exception('L\'extension PHP "ffmpeg" n\'est pas chargée.');
//			}
			if(class_exists('ffmpeg_movie', false)){
				$this->_ffmpeg = new ffmpeg_movie($this->_file->getPathname());
			}
		}
	}
	
	/**
	 * @param Zest_File $file
	 * @param array $options
	 * @return Zest_File_Helper_Abstract_FFmpeg
	 */
	protected function _convertTo(Zest_File $file, array $options){		
		$command = $this->getCommand($options, $file);
		
		exec($command, $output);
		
		if(!$file->isReadable() || !$file->getSize()){
			// $output
			throw new Zest_File_Exception(sprintf('La conversion du fichier "%s" a échoué.', $file->getBasename()));
		}
		
		return $this;
	}
	
	/**
	 * @param array $options
	 * @return boolean
	 */
	public function canBeConverted(array $options){
		if(class_exists('ffmpeg_movie', false)){
			// @todo : tester la conversion des x premières frames
			return true;
		}
		return false;
	}
	
	/**
	 * @param integer $width
	 * @param integer $height
	 * @return array
	 */
	public function getSize(array $options){
		if(class_exists('ffmpeg_movie', false)){
			$width = $this->getFrameWidth();
			$height = $this->getFrameHeight();
			if($this->canBeConverted($options)){
				return $this->_getSize($width, $height, $options);
			}
			return array($width, $height);
		}
		$return = $this->_getSize(1e6, 1e6, $options);
		if($return[0] != 1e6 && $return[1] != 1e6){
			return $return;
		}
		return array(null, null);
	}
	
	/**
	 * @param array $options
	 * @param string|Zest_File $destination
	 * @return string
	 */
	public function getCommand(array $options, $destination){
		$forbidden = array('_setIsCommand', '_setCommand', '_unsetCommand', '_setCropPad');
		
		$this->_command = array();
		
		// input file
		$this->_setCommand('i', $this->_file->getPathname());
		
		// options
		foreach($options as $name => $value){
			$method = '_set'.ucfirst($name);
			if(!in_array($method, $forbidden)){
				if(method_exists($this, $method)){
					$this->$method($value);
				}
			}
		}
		
		// génération de la commande FFMPEG
		$command = 'ffmpeg';
		foreach($this->_command as $key => $value){
			if(!is_null($value) && !is_numeric($value)){
				$value = escapeshellarg($value);
			}
			$command .= ' -'.$key.(strlen($value) ? ' '.$value : '');
		}
		
		// output to null : NUL (windows) ou /dev/null (linux)
		if($destination instanceof Zest_File){
			$destination = $destination->getPathname();
		}
		$command .= ' '.escapeshellarg($destination);
		
		return $command;
	}
	
	/**
	 * Méthodes disponibles
	 * 		getDuration, getFrameCount, getFrameRate
	 * 		getFilename, getComment, getTitle, getAuthor, getCopyright, getArtist, getGenre, getTrackNumber, getYear
	 * 		getFrameHeight, getFrameWidth, getPixelFormat, getBitRate, getVideoBitRate
	 * 
	 * 		getAudioBitRate, getAudioSampleRate, getFrameNumber, getVideoCodec, getAudioCodec ,getAudioChannels
	 * 		hasAudio, hasVideo, getFrame, getNextKeyFrame
	 * 
	 * @param string $method
	 * @param array $args
	 * @return mixed
	 */
	public function __call($method, $args){
		return $this->_call($this->_getFfmpegMovie(), $method, $args);	
	}
	
	/**
	 * @param string $command
	 * @param boolean $boolean
	 * @return Zest_File_Helper_Abstract_FFmpeg
	 */
	protected function _setIsCommand($command, $boolean){
		if($boolean){
			$this->_setCommand($command);
		}
		$this->_unsetCommand($command);
		
		return $this;
	}
	
	/**
	 * @param string $command
	 * @param integer|string $value
	 * @return Zest_File_Helper_Abstract_FFmpeg
	 */
	protected function _setCommand($command, $value = null){
		$this->_command[$command] = $value;
		return $this;
	}
	
	/**
	 * @param string $command
	 * @return void
	 */
	private function _unsetCommand($command){
		if(isset($this->_command[$command])){
			unset($this->_command[$command]);
		}
	}
	
	/**
	 * @param string $type
	 * @param array $options
	 * @return Zest_File_Helper_Abstract_FFmpeg
	 */
	protected function _setCropPad($type, array $options){
		$options = array_change_key_case($options, CASE_LOWER);

		$positions = array('top', 'right', 'bottom', 'left');
		foreach($options as $position => $option){
			if(in_array($position, $positions)){
				$this->_setCommand($type.$position, $option);
			}
			else{
				throw new Zest_File_Exception(sprintf('Les clefs disponibles pour la commande "%s" sont "top", "right", "bottom" et "left".', $type));
			}
		}
		
		return $this;
	}
	
	/**
	 * @param string $format
	 * @return Zest_File_Helper_Abstract_FFmpeg
	 */
	protected function _setFormat($format){
		return $this->_setCommand('f', $format);
	}
	
	/**
	 * @param boolean $overwrite
	 * @return Zest_File_Helper_Abstract_FFmpeg
	 */
	protected function _setIsOverwrite($overwrite = true){
		return $this->_setIsCommand('y', $overwrite);
	}
	
	/**
	 * @param integer|string $duration
	 * @return Zest_File_Helper_Abstract_FFmpeg
	 */
	protected function _setDuration($duration){
		return $this->_setCommand('t', $duration);
	}
	
	/**
	 * @param integer|string $size
	 * @return Zest_File_Helper_Abstract_FFmpeg
	 */
	protected function _setFileSizeLimit($size){
		return $this->_setCommand('fs', $size);
	}
	
	/**
	 * @param integer|string $seek
	 * @return Zest_File_Helper_Abstract_FFmpeg
	 */
	protected function _setSeek($seek){
		return $this->_setCommand('ss', $seek);
	}
	
}