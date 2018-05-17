<?php
/**
 * picScale is a small PHP library to scale/resize images. Size of the image can be increased or decreased by keeping the aspect ratio of the image as it is.
 */

class PicScale
{
	private $imagePath;
	private $imageOriginal;
	private $imageOriginalWidth;
	private $imageOriginalHeight;
	private $imageType;
	private $blurLevel = 100;
	private $modifiedImage;
	private $percentageAllowedForCrop = 0;
	private $paddingType = 'blur';
	private $paddingcolor = array(255,255,255);

	function __construct($path = '')
	{
		if($path)
			$this->load($path);
	}

	function reset(){
		$this->imagePath = '';
		$this->imageOriginal = '';
		$this->imageOriginalWidth = 0;
		$this->imageOriginalHeight = 0;
		$this->imageType = '';
		$this->modifiedImage = '';
	}

	/**
	 * Method to load file
	 */
	function load($path){

		if(empty($path)){
			return "Image Path can't be empty!";
		}

		$this->imagePath = $path;

		if(!list($this->imageOriginalWidth, $this->imageOriginalHeight) = getimagesize($path)) 
			return "Unsupported picture type!";

		$type = strtolower(substr(strrchr($path,"."),1));
		if($type == 'jpeg') $type = 'jpg';

		switch($type){
			case 'bmp': $this->imageOriginal = imagecreatefromwbmp($path); break;
			case 'gif': $this->imageOriginal = imagecreatefromgif($path); break;
			case 'jpg': $this->imageOriginal = imagecreatefromjpeg($path); break;
			case 'png': $this->imageOriginal = imagecreatefrompng($path); break;
			default : return "Unsupported picture type!";
		}

		$this->imageType = $type;
	}

	/**
	 * Method to pad image with blurred version or middle crop it keeping the aspect ratio intact
	 */
	function resize($width, $height){

		if(empty($height) || empty($width))
			return "Please specify proper dimensions of converted image";

		$h_ratio     = $height/$this->imageOriginalHeight;
		$w_ratio     = $width/$this->imageOriginalWidth;
		$ratio       = ($h_ratio < $w_ratio) ? $h_ratio : $w_ratio;
		$dest_height = $this->imageOriginalHeight*($ratio);
		$dest_width  = $this->imageOriginalWidth*($ratio);

		$dest_x = ceil(($width-$dest_width)/2);
		$dest_y = ceil(($height-$dest_height)/2);

		$blurOrCrop = 'crop';
		if($dest_x){
			$percentPaddingX = (($dest_x)/$width)*100;
		}
		if($dest_y){
			$percentPaddingY = (($dest_y)/$height)*100;
		}

		if($percentPaddingX > $percentPaddingY)
			$percentPadding = $percentPaddingX;
		else
			$percentPadding = $percentPaddingY;

		// echo "<br/>width : ".$width." and height : ".$height." and percentPadding : ".$percentPadding;
		if($percentPadding > $this->percentageAllowedForCrop){
			$blurOrCrop = 'blur';
		}

		// dimensions of image that is cropped version of original image in respect of output image
		$cropRatio = $h_ratio>$w_ratio ? $h_ratio : $w_ratio;
		$croppedVersionWidth  = $width * (1/$cropRatio);
		$croppedVersionHeight = $height * (1/$cropRatio);
		$croppedVersion_x = ($this->imageOriginalWidth-$croppedVersionWidth)/2;
		$croppedVersion_y = ($this->imageOriginalHeight-$croppedVersionHeight)/2;

  		$this->modifiedImage = imagecreatetruecolor($width, $height);

		if($this->imageType == "gif" || $this->imageType == "png"){
			imagecolortransparent($this->modifiedImage, imagecolorallocatealpha($this->modifiedImage, 255, 255, 255, 127));
			imagealphablending($this->modifiedImage, false);
			imagesavealpha($this->modifiedImage, true);
		}
		else{
			$clear = imagecolorallocate( $this->modifiedImage, $this->paddingcolor[0], $this->paddingcolor[1], $this->paddingcolor[2]);
			imagefill($this->modifiedImage, 0, 0, $clear);
		}

  		if($this->paddingType == 'blur' && ($dest_x || $dest_y)){

			$paddingImage = imagecreatetruecolor($width, $height);
		  	imagecopyresampled($paddingImage, $this->imageOriginal, 0, 0, $croppedVersion_x, $croppedVersion_y, ($width), ($height), $croppedVersionWidth, $croppedVersionHeight);
		  	for ($i = 0; $i < $this->blurLevel; ++$i) {
			    imagefilter($paddingImage, IMG_FILTER_GAUSSIAN_BLUR);
		  	}
		}

		if($blurOrCrop == 'blur' && $this->paddingType == 'blur'){
			// for blurred image background
			if($paddingImage)
		  		imagecopyresampled($this->modifiedImage, $paddingImage,  0, 0, 0, 0, $width, $height, $width, $height);

		  	// put original image in the canvas
		  	imagecopyresampled($this->modifiedImage, $this->imageOriginal, $dest_x, $dest_y, 0, 0, $dest_width, $dest_height, $this->imageOriginalWidth, $this->imageOriginalHeight);
		}
		else if($this->paddingType == 'colorpadding'){
			// for white background
			$paddingImage = imagecreatetruecolor($width, $height);
		 	$clear = imagecolorallocate( $paddingImage, $this->paddingcolor[0], $this->paddingcolor[1], $this->paddingcolor[2]);
			imagefill($this->modifiedImage, 0, 0, $clear);

			// put original image in the canvas
		  	imagecopyresampled($this->modifiedImage, $this->imageOriginal, $dest_x, $dest_y, 0, 0, $dest_width, $dest_height, $this->imageOriginalWidth, $this->imageOriginalHeight);
		}
		else{
			imagecopyresampled($this->modifiedImage, $this->imageOriginal, 0, 0, $croppedVersion_x, $croppedVersion_y, $width, $height, $croppedVersionWidth, $croppedVersionHeight);

		}
	}

	/**
	 * Method to scale/resize image with given dimension
	 */
	function scaleImage($upperWidth, $upperHeight){

		$tmpSize = getimagesize($this->imagePath);
        list($width, $height, $type, $attr) = $tmpSize;
		
		$scaledWidth = $upperWidth;
		$scaledHeight = $upperHeight;

        if($width > 640){
			$aspectRatio = $width/$height;
			$scaledWidth = 640;
			$scaledHeight = floor($scaledWidth / $aspectRatio);
			$this->resize($scaledWidth, $scaledHeight, 0);
		}
		else if($height > 480){
			$aspectRatio = $width/$height;
			$scaledHeight = 480;
			$scaledWidth = floor($scaledHeight * $aspectRatio);
			$this->resize($scaledWidth, $scaledHeight, 0);
		}
		else{
			$this->resize($width, $height, 0);
		}
	}

	/**
	 * Output the processed image
	 */
	function output($outputImagePath){

		switch($this->imageType){
			case 'bmp': imagewbmp($this->modifiedImage, $outputImagePath); break;
			case 'gif': imagegif($this->modifiedImage, $outputImagePath); break;
			case 'jpg': imagejpeg($this->modifiedImage, $outputImagePath); break;
			case 'png': imagepng($this->modifiedImage, $outputImagePath); break;
		}
	}

	function options($optionName, $optionValue){

		switch ($optionName) {
			case 'colorpadding':
				$this->paddingType = 'colorpadding';
				break;

			case 'paddingcolor':
				$this->paddingcolor = $optionValue;
				break;

			case 'crop-image':
				$this->percentageAllowedForCrop = 100;
				break;

			case 'crop-percent':
				$this->percentageAllowedForCrop = $optionValue;
				break;
			
			default:
				# code...
				break;
		}

	}
}
?>
