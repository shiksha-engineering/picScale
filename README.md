# picScale
picScale is a small PHP library to scale/resize images. Size of the image can be increased or decreased by keeping the aspect ratio of the image as it is.

## How to Use
This library is pretty simple in usage.

##### Step 1: Load the library  
`include_once("PicScale.php");`

##### Step 2: Image to be resized  
`$input = "inputImage.png";`

##### Step 3: Output Image  
`$output = "outputImage.png";`

##### Step 4: Take the object of the library  
`$resizeObj = new PicScale($input);`

##### Step 5: Resize the image to the dimension you want  
`$resizeObj->resize($width = 200,$height = 300);`

##### Step 6: Output the image  
`$resizeObj->output($output);`

## Example 

#### Example 1 : With Blurred image padding

`$input = "plant.jpg";`  
`$output = "BlurredImagePadding.jpg";`  
`$new = new PicScale($input);`  
`$new->resize($width = 450, $height = 450);`  
`$new->output($output);`  

Input Image:  

![](https://preview.ibb.co/ktGPdy/plant.jpg)

Output Image:  

![](https://image.ibb.co/bMrUBJ/Blurred_Image_Padding.jpg)


#### Example 2 : With White Space padding
`$input = "plant.jpg";`   
`$output = "WhiteSpacePadding.jpg";`   
`$new = new PicScale($input);`   
`$new->options("colorpadding", true);`   
`$new->resize($width = 450, $height = 450);`   
`$new->output($output);`   

Input Image:  

![](https://preview.ibb.co/ktGPdy/plant.jpg)

Output Image:

![](https://image.ibb.co/hhtZBJ/White_Space_Padding.jpg)

#### Example 3 : With Colored(any) padding 

If you want to put any color in the padding area

`$new = new PicScale($input);`
`$new->options("colorpadding", true);`  
`$new->resize($width = 600, $height = 450);`  
`$new->options("paddingcolor", array(149,213,50));`  
`$new->output($output);`  

Input Image:  

![](https://s21.postimg.org/j3khle79z/nature.jpg)

Output Image:  

![](https://s21.postimg.org/js4ve41ef/nature1.jpg)


#### Example 4 : Crop Image

`$new = new PicScale($input);`  
`$new->options("crop-image", true);`  
`$new->resize($width = 600, $height = 450);`  
`$new->output($output);`

Input Image:  

![](https://s21.postimg.org/j3khle79z/nature.jpg)

Output Image:  

![](https://s16.postimg.org/xv2f88f5x/nature1.jpg)

OR, if you want to specify the percentage of image that can be cropped else pad-image with any color/blur image.

`$new = new PicScale($input);`  
`$new->options("crop-percent", 50);`  
`$new->resize($width = 600, $height = 450);`  
`$new->output($output);`
