<?php

/**
 * @category Zest
 * @package Zest_Controller
 * @subpackage Plugin
 */
class Zest_Controller_Plugin_HttpRequest_CssSprite extends Zest_Controller_Plugin_HttpRequest_Abstract{

	/**
	 * @var array
	 */
	protected $_defaultSprite = array(
		'sprite' => null,
		'sprite_layout' => 'horizontal',
		'sprite_quality' => 75,
		'sprite_background_color' => 'transparent',
		'pathname' => null,
		'width' => 0,
		'height' => 0,
		'images' => array()
	);

	/**
	 * @var array
	 */
	protected $_defaultImage = array(
		'sprite_alignment' => 'left top',
		'sprite_margin_left' => 0,
		'sprite_margin_right' => 0,
		'sprite_margin_top' => 0,
		'sprite_margin_bottom' => 0,
	);

	/**
	 * @var array
	 */
	protected $_spriteLayout = array('vertical', 'horizontal');

	/**
	 * @var array
	 */
	protected $_spriteAlignment = array('left', 'right', 'top', 'bottom', 'repeat');

	/**
	 * @var array
	 */
	protected $_sprites = array();

	/**
	 * @var array
	 */
	protected $_cssFiles = array();

	/**
	 * @var string
	 */
	const PREG_COMMENT = '\/\*\*([^\*]+)\*\/';

	/**
	 * @var string
	 */
	const PREG_IMAGE = 'url\(([^\)]+)\)';
	
	/**
	 * @var string
	 */
	const PREG_PROPERTY = '([^ :\*]+)[ :]+([^ ;\*]+)[ ;\*]*';
	
	/**
	 * @var string
	 */
	protected $_gcPattern = '*.css';

	/**
	 * @return Zest_Controller_Plugin_HttpRequest_CssSprite
	 */
	public function process(){
		parent::process();
		
		if(!$this->_cache) return;
		
		$css = $this->_selectElements('css');
		if(!$css) return;
		
		$process = false;
		
		foreach($css as $key => $helperElement){
			$mixed = $this->_getSource($helperElement->href);
			if(!$mixed) continue;
			extract($mixed);		// $source, $pathname
			
			// récupération du contenu
			$code = null;
			if(file_exists($pathname)){
				$code = file_get_contents($pathname);
			}
			
			// utilisé dans la prochaine boucle foreach
			$helperElement->urlSourceDir = $source[0];
			$destinationFile = $this->_cache[1].'/'.md5($code).'.css';
			
			if(!file_exists($destinationFile)){
				$process = true;
			}

			// ajout des css
			$this->_cssFiles[$key] = (object) array(
				'sourceFile' => $pathname,
				'destinationFile' => $destinationFile,
				'code' => $code,
				'images' => array()
			);
		}
		
		if($process){
			$this->_prepare();
			$this->_process();
		
			foreach($this->_cssFiles as $key => $cssFile){
				if(file_exists($cssFile->destinationFile)) continue;
				
				// réécriture des url des images
				$helperElement = $css[$key];
				$cssFile->code = $this->_replaceCacheToSourceUrls($cssFile->code, $helperElement->urlSourceDir, $helperElement->href);
				unset($helperElement->urlSourceDir);
				
				// écriture des fichiers
				file_put_contents($cssFile->destinationFile, $cssFile->code);
			}
		}
		
		foreach($css as $key => $helperElement){
			if(isset($this->_cssFiles[$key])){
				$cssFile = $this->_cssFiles[$key];
				
				// réattribution des css
				$href = rtrim($this->_cache[0], '/\\').'/'.basename($cssFile->destinationFile);
				$this->_view->head()->css($href, $helperElement->media);
			}
			else{
				$this->_view->head()->css($helperElement->href, $helperElement->media);
			}
			
		}
		
		return $this;
	}

	/**
	 * @return void
	 */
	protected function _prepare(){

		/*
			récupération des sprites

			NOM											VALEURS POSSIBLES			VALEUR PAR DÉFAUT
			sprite						obligatoire
			sprite-image				obligatoire
			sprite-layout				optionnel 		vertical, horizontal		horizontal
			sprite-quality				optionnel		nombre de 0 à 100			75
			sprite-background-color		optionnel		couleur RVB					transparent
		*/

		foreach($this->_cssFiles as $cssKey => $css){
			preg_match_all('/'.self::PREG_COMMENT.'/', $css->code, $matches);
			foreach($matches[1] as $key => $match){
				if(is_int(strpos($match, 'sprite-ref'))) continue;

				$properties = $this->_getProperties($match);
				$properties['match'] = $matches[0][$key];

				if(!isset($properties['sprite'])){
					continue;
				}
				
				if(isset($properties['sprite_image'])){
					$properties['sprite_image'] = preg_replace('/'.self::PREG_IMAGE.'/', '\\1', $properties['sprite_image']);
					$properties['sprite_image'] = trim($properties['sprite_image'], '\'"');
					$properties['pathname'] = dirname($css->sourceFile).'/'.$properties['sprite_image'];
					touch($properties['pathname']);
					$properties['pathname'] = realpath($properties['pathname']);
				}
				else{
					$properties['error'] = 'la propriété "sprite-image" est obligatoire';
				}
				
				if(isset($properties['sprite_layout']) && !in_array($properties['sprite_layout'], $this->_spriteLayout)){
					$properties['error'] = 'la valeur de la propriété "sprite-layout" doit être "'.implode('" | "', $this->_spriteLayout).'"';
				}
				
				$properties = array_merge($this->_defaultSprite, $properties);

				// si le sprite a déjà été récupéré dans une autre css
				if(isset($this->_sprites[$properties['sprite']])){
					$sprite = $this->_sprites[$properties['sprite']];
					
					// ajout de valeurs propres à la css courante
					$sprite->match[$cssKey] = $properties['match'];
					$sprite->sprite_image[$cssKey] = $properties['sprite_image'];
					
					// comparaison des valeurs des propriétés car chaque sprite doit avoir un nom unique
					$compare1 = (array) $sprite;
					$compare2 = $properties;
					unset($compare1['match'], $compare1['error'], $compare1['sprite_image'], $compare2['match'], $compare2['error'], $compare2['sprite_image']);
					
					if($compare1 != $compare2){
						$sprite->error = sprintf('un sprite avec l\'identifiant "%s" existe déjà mais avec des propriétés différentes', $sprite->sprite);
					}
				}
				
				// si le sprite est un nouveau sprite
				else{
					$sprite = (object) $properties;
					
					$sprite->match = array($cssKey => $properties['match']);
					$sprite->sprite_image = array($cssKey => $properties['sprite_image']);
					
					$this->_sprites[$properties['sprite']] = $sprite;
				}
			}
		}
		
		/*
			récupération des images

			NOM										VALEURS POSSIBLES					VALEUR PAR DÉFAUT
			sprite-ref				obligatoire
			sprite-alignment		optionnel		left, right, top, bottom, repeat	left
			sprite-margin-left		optionnel		[offset]px							0px
			sprite-margin-right		...
			sprite-margin-top		...
			sprite-margin-bottom	...

			si deux expressions sont identiques, il peut y avoir des comportements indésirables
				background-image: url(../img/flux_rss.gif); /** sprite-ref: mysprite
				_background-image: url(../img/flux_rss.gif); /** sprite-ref: mysprite
				.background-image: url(../img/flux_rss.gif); /** sprite-ref: mysprite
			
			il est donc conseillé de différencier les deux expressions par l'ajout de texte dans le commentaire
				background-image: url(../img/flux_rss.gif); /** sprite-ref: mysprite;
				_background-image: url(../img/flux_rss.gif); /** sprite-ref: mysprite; ie6
				.background-image: url(../img/flux_rss.gif); /** sprite-ref: mysprite; ie7
			
			seules les propriétés commençants par "sprite" sont récupérées
		*/

		foreach($this->_cssFiles as $css){
			preg_match_all('/_?\.?background[^u]+'.self::PREG_IMAGE.'[^;]*;?\s*'.self::PREG_COMMENT.'/', $css->code, $matches);
			foreach($matches[2] as $key => $match){
				if(!is_int(strpos($match, 'sprite-ref'))) continue;

				$properties = $this->_getProperties($match);
				
				$relativePathname = trim($matches[1][$key], '\'"');
				$pathname = realpath(dirname($css->sourceFile).'/'.$relativePathname);
				$properties = array_merge($this->_defaultImage, $properties);

				// récupération du sprite
				$sprite = null;
				if(isset($this->_sprites[$properties['sprite_ref']])){
					$sprite = $this->_sprites[$properties['sprite_ref']];
				}

				$cssPrefix = strtolower(substr($matches[0][$key], 0, 1));
				
				$image = array(
					'match' => $matches[0][$key],
					'sprite' => $sprite,
					'cssPrefix' => $cssPrefix != 'b' ? $cssPrefix : '',
					'cssImportant' => is_int(strpos($matches[0][$key], '!important'))
				);
				
				if($sprite){
					if(isset($sprite->error)){
						$image['error'] = sprintf('le sprite "%s" contient une erreur', $sprite->sprite);
					}
				}
				else{
					$image['error'] = sprintf('le sprite "%s" n\'existe pas', $properties['sprite_ref']);
				}

				// ajout de l'image dans le tableau des images du sprite
				if($sprite){
					$spriteImage = null;
					
					// recherche si l'image n'est pas déjà dans le sprite
					foreach($sprite->images as $img){
						if(isset($img->error)) continue;
						
						if($img->pathname == $pathname && $img->properties == $properties){
							$spriteImage = $img;
							break;
						}
					}
					
					// création d'une nouvelle image dans le sprite
					if(is_null($spriteImage)){
						$spriteImage = (object) array(
							'pathname' => $pathname,
							'relativePathname' => $relativePathname,
							'properties' => $properties
						);

						if(!file_exists($pathname)){
							$spriteImage->error = sprintf('le fichier "%s" n\'existe pas', $relativePathname);
						}

						$sprite->images[] = $spriteImage;
					}
					
					$image['spriteImage'] = $spriteImage;
				}

				$css->images[] = $image;
			}
		}
		
		// tri des images pour détecter l'image à mettre en dernier
		foreach($this->_sprites as $sprite){
			if(isset($sprite->error)) continue;
				
			foreach($sprite->images as $key => $image){
				if(isset($image->error)) continue;

				$imageProperties = $image->properties;

				switch($sprite->sprite_layout){
					case 'vertical':
						if($this->_testAlignment($imageProperties, 'bottom')){
							unset($sprite->images[$key]);
							$sprite->images[] = $image;
						}
						break;
					case 'horizontal':
						if($this->_testAlignment($imageProperties, 'right')){
							unset($sprite->images[$key]);
							$sprite->images[] = $image;
						}
						break;
				}
			}
		}
	}
	
	protected function _process(){
		foreach($this->_sprites as $sprite){
			if(isset($sprite->error)) continue;
			
			// calcul des positions des images et de la taille des sprites
			foreach($sprite->images as $image){
				if(isset($image->error)) continue;

				list($width, $height) = getimagesize($image->pathname);
				$image->width = $width;
				$image->height = $height;

				switch($sprite->sprite_layout){
					case 'vertical':
						// height
						if($image->properties['sprite_margin_top'] > 0){
							$sprite->height += $image->properties['sprite_margin_top'];
						}

						$image->y = $sprite->height;
						$sprite->height += $height;

						if($image->properties['sprite_margin_bottom'] > 0){
							$sprite->height += $image->properties['sprite_margin_bottom'];
						}

						// width
						$outerWidth = $width;
						if($image->properties['sprite_margin_left'] > 0){
							$outerWidth += $image->properties['sprite_margin_left'];
						}
						if($image->properties['sprite_margin_right'] > 0){
							$outerWidth += $image->properties['sprite_margin_right'];
						}
						if($outerWidth > $sprite->width){
							$sprite->width = $outerWidth;
						}

						break;
					case 'horizontal':
						// width
						if($image->properties['sprite_margin_left'] > 0){
							$sprite->width += $image->properties['sprite_margin_left'];
						}

						$image->x = $sprite->width;
						$sprite->width += $width;

						if($image->properties['sprite_margin_right'] > 0){
							$sprite->width += $image->properties['sprite_margin_right'];
						}

						// height
						$outerHeight = $height;
						if($image->properties['sprite_margin_top'] > 0){
							$outerHeight += $image->properties['sprite_margin_top'];
						}
						if($image->properties['sprite_margin_bottom'] > 0){
							$outerHeight += $image->properties['sprite_margin_bottom'];
						}
						if($outerHeight > $sprite->height){
							$sprite->height = $outerHeight;
						}

						break;
				}
			}

			// alignement repeat : pour les images qui se répètent, il faut que la taille du sprite soit un multiple de la taille de l'image
			foreach($sprite->images as $image){
				if(isset($image->error)) continue;

				if($this->_testAlignment($image->properties, 'repeat')){
					switch($sprite->sprite_layout){
						case 'vertical':
							// répétition sur x
							if($image->width && $sprite->width%$image->width != 0){
								$sprite->width = (floor($sprite->width/$image->width)+1) * $image->width;
							}
							break;
						case 'horizontal':
							// répétition sur y
							if($image->height && $sprite->height%$image->height != 0){
								$sprite->height = (floor($sprite->height/$image->height)+1) * $image->height;
							}
							break;
					}
				}
			}

			// calcul des alignements des images
			foreach($sprite->images as $image){
				if(isset($image->error)) continue;

				switch($sprite->sprite_layout){
					case 'vertical':
						if($this->_testAlignment($image->properties, 'left') || $this->_testAlignment($image->properties, 'repeat')){
							$image->x = 0;

							if($this->_testAlignment($image->properties, 'left') && $image->properties['sprite_margin_left'] > 0){
								$image->x += $image->properties['sprite_margin_left'];
							}
						}
						else if($this->_testAlignment($image->properties, 'right')){
							$image->x = $sprite->width-$image->width;

							if($this->_testAlignment($image->properties,'right') && $image->properties['sprite_margin_right'] > 0){
								$image->x -= $image->properties['sprite_margin_right'];
							}
						}
						break;
					case 'horizontal':
						if($this->_testAlignment($image->properties, 'top') || $this->_testAlignment($image->properties, 'repeat')){
							$image->y = 0;

							if($this->_testAlignment($image->properties, 'top') && $image->properties['sprite_margin_top'] > 0){
								$image->y += $image->properties['sprite_margin_top'];
							}
						}
						else if($this->_testAlignment($image->properties, 'bottom')){
							$image->y = $sprite->height-$image->height;

							if($this->_testAlignment($image->properties, 'bottom') && $image->properties['sprite_margin_bottom'] > 0){
								$image->y -= $image->properties['sprite_margin_bottom'];
							}
						}
						break;
				}
			}
		}

		// génération des sprites
		foreach($this->_sprites as $sprite){
			if(isset($sprite->error) || !$sprite->width || !$sprite->height) continue;

			$sprite->error = sprintf('le sprite "%s" n\'a pas été généré', $sprite->sprite);
			$spriteExtension = strtolower(pathinfo($sprite->pathname, PATHINFO_EXTENSION));
			
			// création du sprite vide
			$dst_im = imagecreatetruecolor($sprite->width, $sprite->height);
			
			// couleur de fond
			$backgroundColor = null;
			$color = trim($sprite->sprite_background_color, '#');
			if($color != 'transparent'){
				$red = substr($color, 0, 2);
				$green = substr($color, 2, 2);
				$blue = substr($color, 4, 2);
				$backgroundColor = imagecolorallocate($dst_im, hexdec($red), hexdec($green), hexdec($blue));
			}

			// transparence du fond
			if(!$backgroundColor){
				switch($spriteExtension){
					case 'jpg':
						$backgroundColor = imagecolorallocate($dst_im, 255, 255, 255);
						break;
					case 'png':
						imagealphablending($dst_im, false);

						// shim
						$shim = imagecreatefrompng(dirname(__FILE__).'/CssSprite/shim.png');
						imagecopyresampled($dst_im, $shim, 0, 0, 0, 0, $sprite->width, $sprite->height, 1, 1);
						break;
					case 'gif':
						$backgroundColor = imagecolorallocate($dst_im, 255, 255, 255);
						imagecolortransparent($dst_im, $backgroundColor);
						break;
				}
			}
			
			// couleur de fond
			if($backgroundColor){
				imagealphablending($dst_im, true);
				imagefill($dst_im, 0, 0, $backgroundColor);
			}
			
			foreach($sprite->images as $image){
				if(isset($image->error)) continue;

				$src_im = null;

				// récupération de l'image
				$extension = strtolower(pathinfo($image->pathname, PATHINFO_EXTENSION));
				switch($extension){
					case 'jpg':
						$src_im = imagecreatefromjpeg($image->pathname);
						break;
					case 'png':
						$src_im = imagecreatefrompng($image->pathname);
						break;
					case 'gif':
						$src_im = imagecreatefromgif($image->pathname);
						break;
					default:
				}

				if(!$src_im) continue;

				// alignement repeat
				if($this->_testAlignment($image->properties, 'repeat')){
					$dstCoordinates = array();

					switch($sprite->sprite_layout){
						case 'vertical':
							$x = 0;
							while($x < $sprite->width){
								$dstCoordinates[] = array('x' => $x, 'y' => $image->y);
								$x += $image->width;
							}
							break;
						case 'horizontal':
							$y = 0;
							while($y < $sprite->height){
								$dstCoordinates[] = array('x' => $image->x, 'y' => $y);
								$y += $image->height;
							}
							break;
					}
				}
				else{
					$dstCoordinates = array(array('x' => $image->x, 'y' => $image->y));
				}
				
				// copie de l'image dans le sprite
				foreach($dstCoordinates as $coordinates){
					imagecopy($dst_im, $src_im, $coordinates['x'], $coordinates['y'], 0, 0, $image->width, $image->height);
				}
				imagedestroy($src_im);
			}

			unset($sprite->error);

			switch($spriteExtension){
				case 'jpg':
					imagejpeg($dst_im, $sprite->pathname, $sprite->sprite_quality);
					break;
				case 'png':
					imagesavealpha($dst_im, true);
					imagepng($dst_im, $sprite->pathname);
					break;
				case 'gif':
					imagegif($dst_im, $sprite->pathname);
					break;
				default:
			}
				
			imagedestroy($dst_im);
		}

		// calcul des positions css
		foreach($this->_sprites as $sprite){
			if(isset($sprite->error)) continue;

			foreach($sprite->images as $image){
				if(isset($image->error)) continue;

				$image->x *= -1;
				$image->y *= -1;
				
				if($this->_testAlignment($image->properties, 'right')){
					$image->x = 'right';
				}
				else{
					$image->x += $image->properties['sprite_margin_left'];
				}

				if($this->_testAlignment($image->properties, 'bottom')){
					$image->y = 'bottom';
				}
				else{
					$image->y += $image->properties['sprite_margin_top'];
				}
			}
		}

		// suppression des commentaires de sprite et modification du css avec les calculs précédents
		foreach($this->_cssFiles as $cssKey => $css){
			foreach($css->images as $image){
				// sprite de l'image
				if(isset($image['sprite'])){
					$sprite = $image['sprite'];
					
					// erreur sur le sprite (manque une propriété, mauvaise valeur de propriété ou identifiant de sprite utilisé avec des valeurs différentes)
					if(isset($sprite->error)){
						$replace = '/* '.$sprite->error.' : '.trim($sprite->match[$cssKey], '/* ').' */';
						$css->code = str_replace($sprite->match[$cssKey], $replace, $css->code);
						
						$image['error'] = sprintf('le sprite "%s" contient une erreur', $sprite->sprite);
					}
					else{
						$css->code = str_replace($sprite->match[$cssKey], '', $css->code);
					}
				}
				
				$error = null;
				if(isset($image['error'])){
					// erreur spécifique sur l'image (le sprite contient une erreur ou n'existe pas)
					$error = $image['error'];
				}
				else if(!isset($image['spriteImage'])){
					continue;
				}
				else if(isset($image['spriteImage']->error)){
					// erreur global sur l'image (le fichier n'existe pas)
					$error = $image['spriteImage']->error;
				}
				if($error){
					$comment = preg_replace('/.*\s*('.self::PREG_COMMENT.')/', '\\1', $image['match']);
					$replace = '/* '.$error.' : '.trim($comment, '/* ').' */';
					$match = str_replace($comment, $replace, $image['match']);
					$css->code = str_replace($image['match'], $match, $css->code);
					continue;
				}
				
				$sprite = $image['sprite'];
				$spriteImage = $image['spriteImage'];
				
				$important = $image['cssImportant'] ? ' !important' : '';
				$backgroundImage = $image['cssPrefix'].'background-image: url('.$sprite->sprite_image[$cssKey].')'.$important.'; /* generated line */';
				$backgroundPosition = $image['cssPrefix'].'background-position: '.(is_int($spriteImage->x) ? $spriteImage->x.'px' : $spriteImage->x).' '.(is_int($spriteImage->y) ? $spriteImage->y.'px' : $spriteImage->y).$important.'; /* generated line */';
				
				$stripComment = trim(preg_replace('/'.self::PREG_COMMENT.'/', '', $image['match']));
				$css->code = preg_replace('/(\s*)'.preg_quote($image['match'], '/').'/', '\\1'.$stripComment.'\\1'.$backgroundImage.'\\1'.$backgroundPosition, $css->code);
			}
			
			$css->code = trim($css->code);
		}
	}

	/**
	 * @param array $imageSprite
	 * @param string $search
	 * @return boolean
	 */
	protected function _testAlignment($imageSprite, $search){
		return is_int(strpos($imageSprite['sprite_alignment'], $search));
	}

	/**
	 * @param string $code
	 * @return array
	 */
	protected function _getProperties($code){
		preg_match_all('/'.self::PREG_PROPERTY.'/', $code, $sprite);
		$properties = array();
		foreach($sprite[1] as $key => $propertyName){
			if(substr($propertyName, 0, 6) != 'sprite') continue;

			if(substr($sprite[2][$key], -2) == 'px'){
				$sprite[2][$key] = intval($sprite[2][$key]);
			}
			$propertyName = str_replace('-', '_', $propertyName);
			$properties[$propertyName] = $sprite[2][$key];
		}
		return $properties;
	}
	
}