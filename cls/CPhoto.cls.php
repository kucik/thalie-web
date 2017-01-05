<?php
	
	class CPhoto {

		var $items;
		var $id;
		var $db;
	
		function CPhoto(&$db,$id) {
			
			$this->db = $db;
			$rs = $this->db->qy("SELECT gallery	FROM category WHERE id = ".$id." ");
			
			if ($rs['rows'][0]['gallery']) {			
			
				$this->gallery_dir = GALLERY_DIR.$rs['rows'][0]['gallery'];		
			
				$handle = opendir($this->gallery_dir);

				while (false !== ($file = readdir($handle))) {
					if($file != "." && $file != ".." && !is_dir($file) && (substr($file,0,3) != 'tn_')) {    			  					
    					$files[] = $file;
    				}
				}	
					sort($files);
					$this->items = $files;
					closedir($handle);
				}
			}		
		
	
	function parse_tpl(&$tpl, $tpl_name='', $tpl_file = '', $dynamic_block='photo_list') {

			if(!$tpl_name) {
				$tpl_name = 'main';
			}
		
			$tpl->define_dynamic($dynamic_block,  $tpl_name);
      
			$this->print_tpl($tpl, $this->items, $dynamic_block);
		}

		function print_tpl(&$tpl, $items, $dynamic_block) {
	
			if(is_array($items)) {
				foreach ($items as $k=>$v) {

					$tpl->assign($v);
					$tpl->assign(array( 
						"PHOTO_FILE"=>$v,
						"PHOTO_DIR"=>$this->gallery_dir                                              
						));
                          
					$tpl->parse(strtoupper($dynamic_block),'.'.$dynamic_block);  
            
				}        
			}
		}
	
	}
	
	class CGallery {

		var $items;
		var $id;
		var $db;
		var $actual_dir;		
		
		function CGallery(&$db, $id = '') {
			
			$this->id = (int)$id;
			$this->db = $db;		
			
			$handle = opendir(GALLERY_DIR);

			while (false !== ($dir = readdir($handle))) {
 
				if (($dir != ".") && ($dir != "..") && (is_dir(GALLERY_DIR.$dir))) { 

						$items[] = array (
										'name' => $dir
										);					
				} 
			}
			sort($items);
			$this->items = $items;
			closedir($handle);
		}

		function update($param) {			

			require_once('inc/imgresize.fce.inc.php');			
			
			$this->actual_dir = GALLERY_DIR.$param['gallery'];
			$handle = opendir($this->actual_dir);
			
			while (false !== ($file = readdir($handle))) {
				if($file != "." && $file != ".." && !is_dir($file) && (substr($file,0,3) != 'tn_'))    			
    				$files[] = $file;
			}	
			
			foreach ($files as $k => $v) {
				$photo_url = $this->actual_dir."/".$v;
				list($width, $height) = getimagesize($photo_url);
				if ($width > PHOTO_WIDTH || $height > PHOTO_HEIGHT) {
					$img_src = imgresize($photo_url, PHOTO_WIDTH, PHOTO_HEIGHT);
					imagejpeg($img_src, $photo_url);			
				}
				if (!file_exists($this->actual-dir."/tn_".$v)) {

					$img_src = imgresize($photo_url, PHOTO_TN_WIDTH, PHOTO_TN_HEIGHT);
					$photo_url = $this->actual_dir."/tn_".$v;
					imagejpeg($img_src, $photo_url);
				}			
			}			
			closedir($handle);
		
		}

		function parse_tpl(&$tpl, $tpl_name='', $tpl_file = '', $dynamic_block='gallery_list') {

			if(!$tpl_name) {
				$tpl_name = 'main';
			}
		
			$tpl->define_dynamic($dynamic_block,  $tpl_name);
      
			$this->print_tpl($tpl, $this->items, $dynamic_block);
		}

		function print_tpl(&$tpl, $items, $dynamic_block) {
			
			$rs = $this->db->qy("SELECT gallery FROM category WHERE id = ".$this->id." ");			
			$gallery = $rs['rows'][0]['gallery'];			
				
			if(is_array($items)) {
				foreach ($items as $k=>$v) {

					$tpl->assign($v);
					$tpl->assign(array( 
						"GALLERY_NAME"=>$v['name'],
						"GALLERY_SELECTED"=>($gallery == $v['name'])?'selected':''                                              
						));
                          
					$tpl->parse(strtoupper($dynamic_block),'.'.$dynamic_block);  
            
				}        
			}
		}


	}
	
?>