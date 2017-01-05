<?php
function imgresize($filename, $width = 0, $height = 0) {
			
			if (!is_file($filename)) return "";
			
			$img_src = imagecreatefromjpeg($filename);
			
			$original_width = imagesx($img_src);
			$original_height = imagesy($img_src);
			
			if (($width !=0 || $height != 0) && !($original_width<$width && $original_height<$height )) {
				if ( (($width<=$height) && ($width != 0)) || $height == 0) {
					
					$new_width = $width;
					$new_height = ($new_width/$original_width)*$original_height;
					
					if ($new_height > $height && $height != 0) {
						$new_height = $height;
						$new_width = ($new_height/$original_height)*$original_width;				
					}
					
				}
				else if ( (($width >= $height) && ($height != 0)) ||
						  //($original_width > $original_height && $width > $height) || 
						  $width == 0) {
						  	
					$new_height = $height;
					$new_width = ($new_height/$original_height)*$original_width;
					
					if ($new_width>$width && $width !=0) {
						$new_width = $width;
						$new_height = ($new_width/$original_width)*$original_height;
					}
				}
			}
			else {
				$new_width = $original_width;
				$new_height = $original_height;
			}
			
			$new_width = (int) $new_width;
			$new_height = (int) $new_height;
			
			$img_des = imagecreatetruecolor($new_width,$new_height);

			imagecopyresampled ($img_des, $img_src, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);
			
			return $img_des;
		}

?>
