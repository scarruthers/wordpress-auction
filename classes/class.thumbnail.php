<?php
	/*
	 * Image thumbnail generator
	 * Based on the work from http://sniptools.com/vault/generating-jpggifpng-thumbnails-in-php-using-imagegif-imagejpeg-imagepng
	 */
	class ThumbnailGenerator
	{
		public $sourceFile;
		public $destinationFile;
		public $width;
		public $height;
		public $format;
		public $scale;
 
		/*
		 * ThumbnailGenerator::__construct()
		 *
		 * @param mixed $sourceFile Path to source file
		 * @param mixed $destinationFile Path to destination file
		 * @param mixed $width Thumbnail file width
		 * @param mixed $height Thumbnail file height
		 * @param string $format jpeg, png or gif
		 * @param bool $scale Scale thumbnail
		 * @return
		 */
		public function __construct($sourceFile, $destinationFile, $width, $height, $format="jpeg", $scale=true)
		{
			$this->sourceFile = $sourceFile;
			$this->destinationFile = $destinationFile;
			$this->width = $width;
			$this->height = $height;
			$this->format = $format;
			$this->scale = $scale;
		}
 
		/**
		 * ThumbnailGenerator::generate()
		 *
		 * @return
		 */
		public function generate()
		{
			$fromFormat = "imagecreatefrom".$this->format;
			$sourceImage = $fromFormat($this->sourceFile);
			$sourceWidth = imagesx($sourceImage);
			$sourceHeight = imagesy($sourceImage);
 
			if($this->scale)
			{
				$ratio = $this->width / $sourceWidth;
				$this->width = $sourceWidth * $ratio;
				$this->height = $sourceHeight * $ratio;
			}
 
			$targetImage = imagecreatetruecolor($this->width,$this->height);
			imagecopyresampled($targetImage,$sourceImage,0,0,0,0,$this->width,
			$this->height,imagesx($sourceImage),imagesy($sourceImage));
 
			$imageFunction = "image".$this->format;
			$quality = ( $this->format == "png" ? 0 : 100 );
			return $imageFunction($targetImage, $this->destinationFile, $quality);
		}
	}
?>