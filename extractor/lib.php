<?php
error_reporting(E_ERROR | E_PARSE); 
function create_directories($mainfolder,$dataobject,$key_a=null,$path = null){
	//$dataobject is the one complete chapter object.

	if(!empty($dataobject)){

		$folder_no_a = str_pad(($key_a+1), 2, '0', STR_PAD_LEFT);

		$dataobject->name = $folder_no_a."_".sanitize($dataobject->name);
		//creating the chapter folder.
		if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name)) {
			mkdir('output/'.$mainfolder.'/'.sanitize($dataobject->name));
		}

		foreach ($dataobject->sequences[0]->exercises as $key => $value) {

			$folder_no_b = str_pad(($key+1), 2, '0', STR_PAD_LEFT);

			if(empty($value->title)){
					$value->title = $folder_no_b."_Untitled";
			}else{
					$value->title = $folder_no_b."_".sanitize($value->title);
			}
			

			//Creating the folders as the title from json.
			if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title)) {
				mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title);
			}

			//1. Creating the thumbnais folders.=======================================

			if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/thumbnails')) {
				mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/thumbnails');
			}

			$source = 'uploads/'.$mainfolder.'/'.$value->thumbnail;

			$destination = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/thumbnails/'.$value->id.'.png';

			if(!file_exists($destination)){
				copy($source, $destination);
			}
			//2. Load the other json files and do the file extraction.

			$otherjs = 'uploads/'.$mainfolder.'/datas/getExercise'.$value->id.'.js';
			$getExercisedata = file_get_contents($otherjs);
			$jsencode = json_decode($getExercisedata);
			
			$mediapath = explode('.',$jsencode->exercise->media->{"@url"});

			if(is_array($jsencode->exercise->media)){
				//if the media have multiple entries.
				foreach ($jsencode->exercise->media as $intervalue) {

					if(!empty($intervalue->urls->url)){

						$tempextras = explode('.',$intervalue->{"@url"});
						if($intervalue->{"@type"} == "sound"){

							if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/audio')) {
								mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/audio');
							}
							if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/audio/'.$intervalue->{"@id"})) {
								mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/audio/'.$intervalue->{"@id"});
							}

							$mediapathtxt = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/audio/'.$intervalue->{"@id"}."/meta-data.txt";

							$objarray = get_object_vars($intervalue);

							file_put_contents($mediapathtxt,json_encode($objarray['meta-data']));

							if(!empty($intervalue->{"@url"})){

								if(!empty($tempextras)){
									$source1 = 'uploads/'.$mainfolder.'/medias/'.$tempextras[0];
									$destination1 = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/audio/'.$intervalue->{"@id"};

									extract_copy_files($source1,$destination1);
								}
							}
						}

						if($jsencode->exercise->media[0]->{"@type"} == "movie"){
							if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/img')) {
								mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/img');
							}
							if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/img/'.$intervalue->{"@id"})) {
								mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/img/'.$intervalue->{"@id"});
							}

							$mediapathtxt = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/img/'.$intervalue->{"@id"}."/meta-data.txt";

							$objarray = get_object_vars($intervalue);

							file_put_contents($mediapathtxt,json_encode($objarray['meta-data']));

							if(!empty($tempextras)){

								$source1 = 'uploads/'.$mainfolder.'/medias/'.$tempextras[0];
								$destination1 = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/img/'.$intervalue->{"@id"};

								extract_copy_files($source1,$destination1);
							}
						}

					}
				}
				//ends.
			}else{
				if(!empty($jsencode->exercise->media->urls->url)){

					if($jsencode->exercise->media->{"@detailedtype"} == "sound"){

						if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/audio')) {
							mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/audio');
						}
						if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/audio/'.$jsencode->exercise->media->{"@id"})) {
							mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/audio/'.$jsencode->exercise->media->{"@id"});
						}

						$mediapathtxt = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/audio/'.$jsencode->exercise->media->{"@id"}."/meta-data.txt";

						$objarray = get_object_vars($jsencode->exercise->media);

						file_put_contents($mediapathtxt,json_encode($objarray['meta-data']));

						if(!empty($jsencode->exercise->media->{"@url"})){

							if(!empty($mediapath)){
								$source1 = 'uploads/'.$mainfolder.'/medias/'.$mediapath[0];
								$destination1 = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/audio/'.$jsencode->exercise->media->{"@id"};

								extract_copy_files($source1,$destination1);
							}
						}
					}
					if($jsencode->exercise->media->{"@detailedtype"} == "video"){
						if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/video')) {
							mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/video');
						}
						if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/video/'.$jsencode->exercise->media->{"@id"})) {
							mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/video/'.$jsencode->exercise->media->{"@id"});
						}

						$mediapathtxt = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/video/'.$jsencode->exercise->media->{"@id"}."/meta-data.txt";

						$objarray = get_object_vars($jsencode->exercise->media);

						file_put_contents($mediapathtxt,json_encode($objarray['meta-data']));

						if(!empty($mediapath)){

							$source1 = 'uploads/'.$mainfolder.'/medias/'.$mediapath[0];
							$destination1 = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/video/'.$jsencode->exercise->media->{"@id"};

							extract_copy_files($source1,$destination1);
						}
					}
				}

				if(is_array($jsencode->exercise->media)){
					if($jsencode->exercise->media){
						$mediapath = explode('.',$jsencode->exercise->media[0]->{"@url"});
						if($jsencode->exercise->media[0]->{"@detailedtype"} == "image"){
							if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/img')) {
								mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/img');
							}
							if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/img/'.$jsencode->exercise->media[0]->{"@id"})) {
								mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/img/'.$jsencode->exercise->media[0]->{"@id"});
							}

							$mediapathtxt = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/img/'.$jsencode->exercise->media[0]->{"@id"}."/meta-data.txt";

							$objarray = get_object_vars($jsencode->exercise->media[0]);

							file_put_contents($mediapathtxt,json_encode($objarray['meta-data']));

							if(!empty($mediapath)){

								$source1 = 'uploads/'.$mainfolder.'/medias/'.$mediapath[0];
								$destination1 = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/img/'.$jsencode->exercise->media[0]->{"@id"};

								extract_copy_files($source1,$destination1);
							}
						}
					}

				}

			}

			if(!empty($jsencode->exercise->text)){
				$mediapathtxt = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title."/text.txt";
				if(is_array($jsencode->exercise->text)){
					file_put_contents($mediapathtxt,json_encode(strip_tags($jsencode->exercise->text[1]->{"#text"})));
				}else{
					file_put_contents($mediapathtxt,json_encode(strip_tags($jsencode->exercise->text->{"#text"})));
				}
			}


			//creating the marks files starts.
			if(!empty($jsencode->exercise->marks->mark)){
				$counter = 1;
				foreach ($jsencode->exercise->marks->mark as $mkey => $mvalue) {
					mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/marks/');
					if(!empty($mvalue->media->{"@url"})){
						$topexplode = explode(".",$mvalue->media->{"@url"});	
					}
					if(!empty($mvalue->media->{"@url"})){
						$markfilename = $mvalue->media->{"@url"};
						$explodemark = explode(".", $markfilename);
						$marksource = 'uploads/'.$mainfolder.'/medias/'.$explodemark[0].'.jpg';
						$markdestination = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/marks/'.$mvalue->media->{"@elemnum"}.'/'.$explodemark[0].'.jpg';
						copy($marksource, $markdestination);

					}else{

						if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/marks/'.$mvalue->{"@elemnum"})) {


							mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/marks/'.$mvalue->{"@elemnum"});

							$tempcount = $counter;

							$source1 = 'uploads/'.$mainfolder.'/medias/'.$topexplode[0].'/Q'.$tempcount."_".$topexplode[0].".png";

							$destination1 = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/marks/'.$mvalue->{"@elemnum"}.'/Q'.$tempcount."_".$topexplode[0].".png";
							copy($source1, $destination1);

							$mediapathtxt = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/marks/'.$mvalue->{"@elemnum"}."/text.txt";

							file_put_contents($mediapathtxt,json_encode(strip_tags($mvalue->text->{"#text"})));

						}

						$counter++;

					}

				}
			}

	    //Creating marks file ends.

	    //Creating the body files.
			if(!empty($jsencode->exercise->body)){
				if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/body/'.$jsencode->exercise->body->{"@id"})) {
					mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/body/');
					mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/body/'.$jsencode->exercise->body->{"@id"});

					foreach ($jsencode->exercise->body->media as $bkey => $bvalue) {
						if(!empty($bvalue->urls->url)){
							$bexplode = explode(".",$bvalue->{"@url"});

							$source1 = 'uploads/'.$mainfolder.'/medias/'.$bexplode[0];

							$destination1 = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/body/'.$jsencode->exercise->body->{"@id"};

							extract_copy_files($source1,$destination1);
						}
					}
				}
			}

			if($jsencode->exercise->bulletPoints->bulletPoint){
				foreach ($jsencode->exercise->bulletPoints->bulletPoint as $bpkey => $bpvalue) {

					if(!empty($bpvalue->media->urls->url)){
						$tempextras = explode('.',$bpvalue->media->{"@url"});
						if($bpvalue->media->{"@type"} == "sound"){

							if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/bulletPoint')) {
								mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/bulletPoint');
							}
							if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/bulletPoint/'.$bpvalue->media->{"@id"})) {
								mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/bulletPoint/'.$bpvalue->media->{"@id"});
							}

							$mediapathtxt = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/bulletPoint/'.$bpvalue->media->{"@id"}."/meta-data.txt";


							$objarray = get_object_vars($bpvalue->media);

							file_put_contents($mediapathtxt,json_encode($objarray['meta-data']));

							$bptxt = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/bulletPoint/'.$bpvalue->media->{"@id"}."/text.txt";
							file_put_contents($bptxt,json_encode(strip_tags($bpvalue->text)));


							if(!empty($bpvalue->media->{"@url"})){

								if(!empty($tempextras)){
									$source1 = 'uploads/'.$mainfolder.'/medias/'.$tempextras[0];
									$destination1 = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/bulletPoint/'.$bpvalue->media->{"@id"};

									extract_copy_files($source1,$destination1);
								}
							}
						}
					}
				}
			}

			if(!empty($jsencode->exercise->items->item)){
				foreach ($jsencode->exercise->items->item as $itemkey => $itemvalue) {


					if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/items/'.$itemvalue->media[0]->{"@id"})) {

						if(!empty($itemvalue->media[0]->{"@url"})){

							if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/items')) {
								mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/items');
							}

							mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/items/'.$itemvalue->media[0]->{"@id"});

							$itemexplode = explode(".", $itemvalue->media[0]->{"@url"});

							$itemsource = 'uploads/'.$mainfolder.'/medias/'.$itemexplode[0].'/Q1_'.$itemvalue->media[0]->{"@url"};

							$itemdestination = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/items/'.$itemvalue->media[0]->{"@id"}.'/Q1_'.$itemvalue->media[0]->{"@url"};

							copy($itemsource, $itemdestination);

							$mediapathtxt = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/items/'.$itemvalue->media[0]->{"@id"}."/meta-data.txt";

							$objarray = get_object_vars($itemvalue->media[0]);

							file_put_contents($mediapathtxt,json_encode($objarray['meta-data']));

							$mediapathtxt = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/items/'.$itemvalue->media[0]->{"@id"}."/text.txt";
							file_put_contents($mediapathtxt,json_encode(strip_tags($itemvalue->description->text)));

						}
					}
				}
			}
		}//End of foreach.
	}
}


function extract_copy_files($src, $dst){
	// open the source directory
	$dir = opendir($src); 

    // Make the destination directory if not exist
	@mkdir($dst); 

    // Loop through the files in source directory
	while( $file = readdir($dir) ) { 

		if (( $file != '.' ) && ( $file != '..' )) { 
			if ( is_dir($src . '/' . $file) ) 
			{ 

                // Recursively calling custom copy function
                // for sub directory 
				extract_copy_files($src . '/' . $file, $dst . '/' . $file); 

			} 
			else { 
				copy($src . '/' . $file, $dst . '/' . $file); 
			} 
		} 
	} 

	closedir($dir);
}


function url(){
  return sprintf(
    "%s://%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['SERVER_NAME'],
    $_SERVER['REQUEST_URI']
  );
}

function sanitize($string){
	return preg_replace("/[^a-z0-9\_\-\.]/i", '', $string);
}