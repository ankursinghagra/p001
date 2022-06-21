<?php
error_reporting(E_ERROR | E_PARSE); 
function create_directories($mainfolder,$dataobject,$key_a=null,$path = null){
	//$dataobject is the one complete chapter object.

	if(!empty($dataobject)){

		$folder_no_a = str_pad(($key_a+1), 2, '0', STR_PAD_LEFT);

		$dataobject->name = $folder_no_a."_".sanitize($dataobject->name);
		//creating the chapter folder.
		if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name)) {
			mkdir('output/'.$mainfolder.'/'.sanitize($dataobject->name).'/');
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
				mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/');
			}

			//creating calltoaction text file 
			if(isset($value->metadatas)){
				foreach($value->metadatas as $md){
					if($md->key == 'callToAction' && !empty($md->value)){
						wrt2file('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/callToAction.txt' , stripp($md->value));
					}
					if($md->key == 'bgImage.media' && isset($md->value->media)){

						$key_folder = explode('.',$md->value->media->{"@url"});
						$source1 = 'uploads/'.$mainfolder.'/medias/'.$key_folder[0];
						$destination1 = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/background';
						if(!is_dir($destination1)) {
							mkdir($destination1);
						}
						extract_copy_files($source1,$destination1);
					}
				}
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

					if(!empty($intervalue->urls->url) && !empty($intervalue->{"@url"})){

						$tempextras = explode('.',$intervalue->{"@url"});
						if($intervalue->{"@type"} == "sound"){

							if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/audio')) {
								mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/audio');
							}
							if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/audio/'.$intervalue->{"@id"})) {
								mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/audio/'.$intervalue->{"@id"}.'/');
							}

							$mediapathtxt = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/audio/'.$intervalue->{"@id"}."/meta-data.txt";

							$objarray = get_object_vars($intervalue);

							wrt2file($mediapathtxt,json_encode($objarray['meta-data']));

							if(!empty($intervalue->{"@url"})){

								if(!empty($tempextras)){
									$source1 = 'uploads/'.$mainfolder.'/medias/'.$tempextras[0];
									$destination1 = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/audio/'.$intervalue->{"@id"}.'/';

									extract_copy_files($source1,$destination1);
								}
							}
						}

						if($intervalue->{"@type"} == "movie"){
							$tempextras2 = explode('.',$intervalue->{"@url"});
							if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/img')) {
								mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/img');
							}
							if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/img/'.$intervalue->{"@id"})) {
								mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/img/'.$intervalue->{"@id"}.'/');
							}

							$mediapathtxt = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/img/'.$intervalue->{"@id"}."/meta-data.txt";

							$objarray = get_object_vars($intervalue);

							wrt2file($mediapathtxt,json_encode($objarray['meta-data']));

							if(!empty($tempextras2)){

								$source1 = 'uploads/'.$mainfolder.'/medias/'.$tempextras2[0];
								$destination1 = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/img/'.$intervalue->{"@id"};

								extract_copy_files($source1,$destination1);
							}
						}

						//----------------------
						if($intervalue->{"@detailedtype"} == "image"){

							$mediapath = explode('.',$intervalue->{"@url"});
							if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/img')) {
								mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/img');
							}
							if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/img/'.$intervalue->{"@id"})) {
								mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/img/'.$intervalue->{"@id"}.'/');
							}

							$mediapathtxt = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/img/'.$intervalue->{"@id"}."/meta-data.txt";

							$objarray = get_object_vars($intervalue);

							wrt2file($mediapathtxt,json_encode($objarray['meta-data']));

							if(!empty($mediapath)){

								$source1 = 'uploads/'.$mainfolder.'/medias/'.$mediapath[0];
								$destination1 = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/img/'.$intervalue->{"@id"}.'/';

								extract_copy_files($source1,$destination1);
							}

						}

						if($intervalue->{"@detailedtype"} == "video"){

							$mediapath = explode('.',$intervalue->{"@url"});
							if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/video')) {
								mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/video');
							}
							if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/video/'.$intervalue->{"@id"})) {
								mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/video/'.$intervalue->{"@id"}.'/');
							}

							$mediapathtxt = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/video/'.$intervalue->{"@id"}."/meta-data.txt";

							$objarray = get_object_vars($intervalue);

							wrt2file($mediapathtxt,json_encode($objarray['meta-data']));

							if(!empty($mediapath)){

								$source1 = 'uploads/'.$mainfolder.'/medias/'.$mediapath[0];
								$destination1 = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/video/'.$intervalue->{"@id"}.'/';

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
							mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/audio/'.$jsencode->exercise->media->{"@id"}.'/');
						}

						$mediapathtxt = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/audio/'.$jsencode->exercise->media->{"@id"}."/meta-data.txt";

						$objarray = get_object_vars($jsencode->exercise->media);

						wrt2file($mediapathtxt,json_encode($objarray['meta-data']));

						if(!empty($jsencode->exercise->media->{"@url"})){

							if(!empty($mediapath)){
								$source1 = 'uploads/'.$mainfolder.'/medias/'.$mediapath[0];
								$destination1 = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/audio/'.$jsencode->exercise->media->{"@id"}.'/';

								extract_copy_files($source1,$destination1);
							}
						}
					}
					if($jsencode->exercise->media->{"@detailedtype"} == "video"){
						if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/video')) {
							mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/video');
						}
						if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/video/'.$jsencode->exercise->media->{"@id"})) {
							mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/video/'.$jsencode->exercise->media->{"@id"}.'/');
						}

						$mediapathtxt = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/video/'.$jsencode->exercise->media->{"@id"}."/meta-data.txt";

						$objarray = get_object_vars($jsencode->exercise->media);

						wrt2file($mediapathtxt,json_encode($objarray['meta-data']));

						if(!empty($mediapath)){

							$source1 = 'uploads/'.$mainfolder.'/medias/'.$mediapath[0];
							$destination1 = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/video/'.$jsencode->exercise->media->{"@id"}.'/';

							extract_copy_files($source1,$destination1);
						}
					}
				}
			}


			$mediapathtxt = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title."/text.txt";

			if(isset($jsencode->exercise->title)){
				if(isset($jsencode->exercise->title->{'#text'}) && !empty($jsencode->exercise->title->{'#text'}) && is_string($jsencode->exercise->title->{'#text'})){
					//wrt2file($mediapathtxt,json_encode(stripp($jsencode->exercise->title->{'#text'})));
					wrt2file($mediapathtxt,stripp($jsencode->exercise->title->{'#text'}));
				}else if(isset($jsencode->exercise->title) && !empty($jsencode->exercise->title) && is_string($jsencode->exercise->title)){
					//wrt2file($mediapathtxt,json_encode(stripp($jsencode->exercise->title)));
					wrt2file($mediapathtxt,stripp($jsencode->exercise->title));
				}
			}
			if(isset($jsencode->exercise->instruction)){
				if(isset($jsencode->exercise->instruction->{'#text'}) && !empty($jsencode->exercise->instruction->{'#text'}) && is_string($jsencode->exercise->instruction->{'#text'})){
					wrt2file($mediapathtxt,stripp($jsencode->exercise->instruction->{'#text'}));
				}else if(isset($jsencode->exercise->instruction) && !empty($jsencode->exercise->instruction) && is_string($jsencode->exercise->instruction)){
					wrt2file($mediapathtxt,stripp($jsencode->exercise->instruction));
				}
			}
			if(isset($jsencode->exercise->question)){
				if(isset($jsencode->exercise->question->{'#text'}) && !empty($jsencode->exercise->question->{'#text'}) && is_string($jsencode->exercise->question->{'#text'})){
					wrt2file($mediapathtxt,stripp($jsencode->exercise->question->{'#text'}));
				}else if(isset($jsencode->exercise->question) && !empty($jsencode->exercise->question) && is_string($jsencode->exercise->question)){
					wrt2file($mediapathtxt,stripp($jsencode->exercise->question));
				}
			}
			if(isset($jsencode->exercise->introduction)){
				if(isset($jsencode->exercise->introduction->{'#text'}) && !empty($jsencode->exercise->introduction->{'#text'}) && is_string($jsencode->exercise->introduction->{'#text'})){
					wrt2file($mediapathtxt,stripp($jsencode->exercise->introduction->{'#text'}));
				}else if(isset($jsencode->exercise->introduction) && !empty($jsencode->exercise->introduction) && is_string($jsencode->exercise->introduction)){
					wrt2file($mediapathtxt,stripp($jsencode->exercise->introduction));
				}else if(isset($jsencode->exercise->introduction->text) && !empty($jsencode->exercise->introduction->text) && is_string($jsencode->exercise->introduction->text)){
					wrt2file($mediapathtxt,stripp($jsencode->exercise->introduction->text));
				}
			}
			if(isset($jsencode->exercise->intro)){
				if(isset($jsencode->exercise->intro->text) && !empty($jsencode->exercise->intro->text) && is_string($jsencode->exercise->intro->text)){
					wrt2file($mediapathtxt,stripp($jsencode->exercise->intro->text));
				}else if(isset($jsencode->exercise->intro) && !empty($jsencode->exercise->intro) && is_string($jsencode->exercise->intro)){
					wrt2file($mediapathtxt,stripp($jsencode->exercise->intro));
				}else if(isset($jsencode->exercise->intro->text) && !empty($jsencode->exercise->intro->text) && is_string($jsencode->exercise->intro->text)){
					wrt2file($mediapathtxt,stripp($jsencode->exercise->intro->text));
				}
			}
			if(isset($jsencode->exercise->graphiccaption)){
				if(isset($jsencode->exercise->graphiccaption->text) && !empty($jsencode->exercise->graphiccaption->text) && is_string($jsencode->exercise->graphiccaption->text)){
					wrt2file($mediapathtxt,stripp($jsencode->exercise->graphiccaption->text));
				}else if(isset($jsencode->exercise->graphiccaption) && !empty($jsencode->exercise->graphiccaption) && is_string($jsencode->exercise->graphiccaption)){
					wrt2file($mediapathtxt,stripp($jsencode->exercise->graphiccaption));
				}
			}
			if(isset($jsencode->exercise->graphictitle)){
				if(isset($jsencode->exercise->graphictitle->text) && !empty($jsencode->exercise->graphictitle->text) && is_string($jsencode->exercise->graphictitle->text)){
					wrt2file($mediapathtxt,stripp($jsencode->exercise->graphictitle->text));
				}else if(isset($jsencode->exercise->graphictitle) && !empty($jsencode->exercise->graphictitle) && is_string($jsencode->exercise->graphictitle)){
					wrt2file($mediapathtxt,stripp($jsencode->exercise->graphictitle));
				}
			}
			if(isset($jsencode->exercise->conclusion)){
				if(isset($jsencode->exercise->conclusion->text) && !empty($jsencode->exercise->conclusion->text) && is_string($jsencode->exercise->conclusion->text)){
					wrt2file($mediapathtxt,stripp($jsencode->exercise->conclusion->text));
				}else if(isset($jsencode->exercise->conclusion) && !empty($jsencode->exercise->conclusion) && is_string($jsencode->exercise->conclusion)){
					wrt2file($mediapathtxt,stripp($jsencode->exercise->conclusion));
				}else if(isset($jsencode->exercise->conclusion->text) && !empty($jsencode->exercise->conclusion->text) && is_string($jsencode->exercise->conclusion->text)){
					wrt2file($mediapathtxt,stripp($jsencode->exercise->conclusion->text));
				}
			}
			if(isset($jsencode->exercise->score)){
				if(isset($jsencode->exercise->score->text) && !empty($jsencode->exercise->score->text) && is_string($jsencode->exercise->score->text)){
					wrt2file($mediapathtxt,stripp(str_replace('[X]','',$jsencode->exercise->score->text)));
				}else if(isset($jsencode->exercise->score) && !empty($jsencode->exercise->score) && is_string($jsencode->exercise->score)){
					wrt2file($mediapathtxt,stripp(str_replace('[X]','',$jsencode->exercise->score)));
				}
			}
			if(!empty($jsencode->exercise->text)){
				if(isset($jsencode->exercise->text) && is_array($jsencode->exercise->text)){
					$text_g = '';
					foreach($jsencode->exercise->text as $text){
						if(isset($text->{'#text'}) && !empty($text->{'#text'})){
							$text_g.=stripp($text->{'#text'})."\n";
						}
					}
					if(!empty($text_g)){
						wrt2file($mediapathtxt,$text_g);
					}
				}else{

					if(isset($jsencode->exercise->text->{"#text"}) && !empty($jsencode->exercise->text->{"#text"}) && is_string($jsencode->exercise->text->{"#text"})){
						wrt2file($mediapathtxt,stripp($jsencode->exercise->text->{"#text"}));
					}
					if(isset($jsencode->exercise->text) && !empty($jsencode->exercise->text) && is_string($jsencode->exercise->text)){
						wrt2file($mediapathtxt,stripp($jsencode->exercise->text));
					}
					
				}
			}

			if(isset($jsencode->exercise->texts)&&!empty($jsencode->exercise->texts)){
				if(isset($jsencode->exercise->texts->zone) && is_array($jsencode->exercise->texts->zone)){
					if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/texts/')){
						mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/texts/');
					}
					foreach($jsencode->exercise->texts->zone as $k_z => $zone){
						$zone_folder = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/texts/'.($k_z+1).'/';
						if(!is_dir($zone_folder)){mkdir($zone_folder);}
						if(isset($zone->{"input-text"}->alternatives->alternative)){
							if(is_array($zone->{"input-text"}->alternatives->alternative)){
								foreach($zone->{"input-text"}->alternatives->alternative as $k_alt => $alternative){
									if(isset($alternative->{"#text"}) && is_string($alternative->{"#text"}) && !empty($alternative->{"#text"})){
										wrt2file($zone_folder.'/alt_text.txt' , stripp($alternative->{"#text"}));
									}

									if((isset($alternative->text)&&is_string($alternative->text)&&!empty($alternative->text))){
										wrt2file($zone_folder.'/alt_text.txt' , stripp($alternative->text));
									}

									if((isset($alternative->text->{"#text"})&&is_string($alternative->text->{"#text"})&&!empty($alternative->text->{"#text"}))){
										wrt2file($zone_folder.'/alt_text.txt' , stripp($alternative->text->{"#text"}));
									}
								}
							}
						}elseif(isset($zone->text)){
							if((isset($zone->text)&&is_string($zone->text)&&!empty($zone->text))){
								wrt2file($zone_folder.'/alt_text.txt' , stripp($zone->text));
							}
						}
					}
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
						$markdestination = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/marks/'.($mkey+1).'/'.$explodemark[0].'.jpg';
						copy($marksource, $markdestination);

					}else{

						if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/marks/'.($mkey+1))) {


							mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/marks/'.($mkey+1));

							$tempcount = $counter;

							$source1 = 'uploads/'.$mainfolder.'/medias/'.$topexplode[0].'/Q'.$tempcount."_".$topexplode[0].".png";

							$destination1 = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/marks/'.($mkey+1).'/Q'.$tempcount."_".$topexplode[0].".png";
							copy($source1, $destination1);

							$mediapathtxt = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/marks/'.($mkey+1)."/text.txt";

							wrt2file($mediapathtxt,stripp($mvalue->text->{"#text"}));

						}

						$counter++;

					}

				}
			}

	    	//Creating marks file ends.

	    	//Creating the body files.
			if(!empty($jsencode->exercise->body)){
				if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/body/')) {
					mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/body/');
					
					if(is_array($jsencode->exercise->body->media)){

						foreach ($jsencode->exercise->body->media as $bkey => $bvalue) {
							if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/body/'.($bkey+1).'/')){
								mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/body/'.($bkey+1).'/');
							}
							$mediapathtxt = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/body/'.($bkey+1)."/meta-data.txt";

							$objarray = get_object_vars($jsencode->exercise->body->media);

							wrt2file($mediapathtxt,json_encode($objarray['meta-data']));

							if(!empty($bvalue->urls->url)){
								$bexplode = explode(".",$bvalue->{"@url"});

								$source1 = 'uploads/'.$mainfolder.'/medias/'.$bexplode[0];

								$destination1 = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/body/'.($bkey+1).'/';

								extract_copy_files($source1,$destination1);
							}
						}
					}elseif(isset($jsencode->exercise->body->media->{"@type"})){

						if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/body/')){
							mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/body/');
						}
						$mediapathtxt = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/body/meta-data.txt';

						$objarray = get_object_vars($jsencode->exercise->body->media);

						wrt2file($mediapathtxt,json_encode($objarray['meta-data']));

						if(!empty($jsencode->exercise->body->media->urls->url)){
							$bexplode = explode(".",$jsencode->exercise->body->media->{"@url"});

							$source1 = 'uploads/'.$mainfolder.'/medias/'.$bexplode[0];

							$destination1 = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/body/';

							extract_copy_files($source1,$destination1);
						}
					}
				}
			}

			if(isset($jsencode->exercise->hotspots->hotspot)){
				if(is_array($jsencode->exercise->hotspots->hotspot)){
					foreach($jsencode->exercise->hotspots->hotspot as $key_h => $hotspot){

						if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/hotspot')) {
							mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/hotspot');
						}

						if(isset($hotspot->alternatives->alternative->text->{"#text"})&&!empty($hotspot->alternatives->alternative->text->{"#text"})){
							wrt2file('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/hotspot/text.txt' , stripp($hotspot->alternatives->alternative->text->{"#text"}));
						}
						if(isset($hotspot->alternatives->alternative->response->text->{"#text"})&&!empty($hotspot->alternatives->alternative->response->text->{"#text"})){
							wrt2file('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/hotspot/text.txt' , stripp($hotspot->alternatives->alternative->response->text->{"#text"}));
						}
					}
				}else{
					$hotspot = $jsencode->exercise->hotspots->hotspot;
					if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/hotspot')) {
						mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/hotspot');
					}

					if(isset($hotspot->alternatives->alternative->text->{"#text"})&&!empty($hotspot->alternatives->alternative->text->{"#text"})){
						wrt2file('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/hotspot/text.txt' , stripp($hotspot->alternatives->alternative->text->{"#text"}));
					}
					if(isset($hotspot->alternatives->alternative->response->text->{"#text"})&&!empty($hotspot->alternatives->alternative->response->text->{"#text"})){
						wrt2file('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/hotspot/text.txt' , stripp($hotspot->alternatives->alternative->response->text->{"#text"}));
					}
				}
			}

			if($jsencode->exercise->bulletPoints->bulletPoint){
				foreach ($jsencode->exercise->bulletPoints->bulletPoint as $bpkey => $bpvalue) {

					if(isset($bpvalue->text)&&is_string($bpvalue->text)&&!empty($bpvalue->text)){
						if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/bulletPoint')) {
							mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/bulletPoint');
						}
						if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/bulletPoint/'.($bpkey+1))) {
							mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/bulletPoint/'.($bpkey+1));
						}
						wrt2file('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/bulletPoint/'.($bpkey+1).'/text.txt',stripp($bpvalue->text));
					}
					if(!empty($bpvalue->media->urls->url)){
						$tempextras = explode('.',$bpvalue->media->{"@url"});
						if($bpvalue->media->{"@type"} == "sound"){

							if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/bulletPoint')) {
								mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/bulletPoint');
							}
							if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/bulletPoint/'.($bpkey+1))) {
								mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/bulletPoint/'.($bpkey+1));
							}

							$mediapathtxt = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/bulletPoint/'.($bpkey+1)."/meta-data.txt";


							$objarray = get_object_vars($bpvalue->media);

							wrt2file($mediapathtxt,json_encode($objarray['meta-data']));

							/*$bptxt = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/bulletPoint/'.($bpkey+1)."/text.txt";
							wrt2file($bptxt,stripp($bpvalue->text));*/


							if(!empty($bpvalue->media->{"@url"})){

								if(!empty($tempextras)){
									$source1 = 'uploads/'.$mainfolder.'/medias/'.$tempextras[0];
									$destination1 = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/bulletPoint/'.($bpkey+1);

									extract_copy_files($source1,$destination1);
								}
							}
						}
					}
				}
			}

			if(!empty($jsencode->exercise->items->item)){
				foreach ($jsencode->exercise->items->item as $itemkey => $itemvalue) {
					
					if(is_array($itemvalue->media)){

						foreach($itemvalue->media as $km => $media){

							if(!empty($media->{"@url"})){

								if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/items')) {
									mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/items');
								}

								if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/items/'.($itemkey+1))) {
									mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/items/'.($itemkey+1));
								}

								if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/items/'.($itemkey+1).'/'.$media->{"@detailedtype"})) {
									mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/items/'.($itemkey+1).'/'.$media->{"@detailedtype"});
								}


								$itemexplode = explode(".", $media->{"@url"});

								$itemsource = 'uploads/'.$mainfolder.'/medias/'.$itemexplode[0];

								$itemdestination = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/items/'.($itemkey+1).'/'.$media->{"@detailedtype"};

								extract_copy_files($itemsource,$itemdestination);

								$mediapathtxt = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/items/'.($itemkey+1)."/meta-data.txt";

								$objarray = get_object_vars($media);

								wrt2file($mediapathtxt,json_encode($objarray['meta-data']));

								$mediapathtxt = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/items/'.($itemkey+1)."/text.txt";
								if(isset($itemvalue->title) && !empty($itemvalue->title) && is_string($itemvalue->title)){
									wrt2file($mediapathtxt,stripp($itemvalue->title));
								}
								if(isset($itemvalue->description->text) && !empty($itemvalue->description->text) && is_string($itemvalue->description->text)){
									wrt2file($mediapathtxt,stripp($itemvalue->description->text));
								}

							}
						}

					}else{

						if(!empty($itemvalue->media->{"@url"})){

							if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/items')) {
								mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/items');
							}

							if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/items/'.($itemkey+1))) {
								mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/items/'.($itemkey+1));
							}

							if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/items/'.($itemkey+1).'/'.$itemvalue->media->{"@detailedtype"})) {
								mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/items/'.($itemkey+1).'/'.$itemvalue->media->{"@detailedtype"});
							}

							$itemexplode = explode(".", $itemvalue->media->{"@url"});

							$itemsource = 'uploads/'.$mainfolder.'/medias/'.$itemexplode[0];

							$itemdestination = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/items/'.($itemkey+1).'/'.$itemvalue->media->{"@detailedtype"};

							extract_copy_files($itemsource,$itemdestination);

							$mediapathtxt = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/items/'.($itemkey+1)."/meta-data.txt";

							$objarray = get_object_vars($itemvalue->media);

							wrt2file($mediapathtxt,json_encode($objarray['meta-data']));

							$mediapathtxt = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/items/'.($itemkey+1)."/text.txt";
							if(isset($itemvalue->title) && !empty($itemvalue->title) && is_string($itemvalue->title)){
								wrt2file($mediapathtxt,stripp($itemvalue->title));
							}
							if(isset($itemvalue->description->text) && !empty($itemvalue->description->text) && is_string($itemvalue->description->text)){
								wrt2file($mediapathtxt,stripp($itemvalue->description->text));
							}

						}
					}
				}
			}

			if(isset($jsencode->exercise->alternatives->alternative)){

				if(is_array($jsencode->exercise->alternatives->alternative)){
					foreach($jsencode->exercise->alternatives->alternative as $key_alt => $alt){

						if( isset($alt->text->{"#text"})|| (isset($alt->text)&&is_string($alt->text)&&!empty($alt->text)) || (isset($alt->media)&&isset($alt->media->{"@detailedtype"})) || isset($alt->label) || isset($alt->response)){

							if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/alternative')) {
								mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/alternative');
							}
							if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/alternative/'.($key_alt+1))) {
								mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/alternative/'.($key_alt+1));
							}
						}

						if(isset($alt->text->{"#text"})){
							wrt2file('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/alternative/'.($key_alt+1).'/text.txt' , stripp($alt->text->{"#text"}));
						}

						if((isset($alt->text)&&is_string($alt->text)&&!empty($alt->text))){
							wrt2file('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/alternative/'.($key_alt+1).'/text.txt' , stripp($alt->text));
						}

						if(isset($alt->media)&&isset($alt->media->{"@detailedtype"})){
								$key_folder = explode('.',$alt->media->{"@url"});
								$source1 = 'uploads/'.$mainfolder.'/medias/'.$key_folder[0];
								if($alt->media->{"@detailedtype"} == 'image'){
									$destination1 = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/alternative/'.($key_alt+1).'/img';
								}else if($alt->media->{"@detailedtype"} == 'sound'){
									$destination1 = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/alternative/'.($key_alt+1).'/audio';
								}else{
									$destination1 = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/alternative/'.($key_alt+1).'/media';
								}
								if(!is_dir($destination1)) {
									mkdir($destination1);
								}
								extract_copy_files($source1,$destination1);
								if(isset($alt->media->{'meta-data'})){
									wrt2file($destination1.'/meta_data.txt' , json_encode($alt->media->{'meta-data'}));
								}
						}

						if(isset($alt->label) && !empty($alt->label) && is_string($alt->label)){
							wrt2file('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/alternative/'.($key_alt+1).'/text.txt' , stripp($alt->label));							
						}

						if(isset($alt->response)){
							if(isset($alt->response->media) && is_array($alt->response->media)){
								foreach($alt->response->media as $media){
									if(isset($media->{"@url"}) && is_string($media->{"@url"}) && !empty($media->{"@url"})){										
										$key_folder = explode('.',$media->{"@url"});
										$source1 = 'uploads/'.$mainfolder.'/medias/'.$key_folder[0];
										if($media->{"@detailedtype"} == 'image'){
											$destination1 = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/alternative/'.($key_alt+1).'/img';
										}else if($media->{"@detailedtype"} == 'sound'){
											$destination1 = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/alternative/'.($key_alt+1).'/audio';
										}else{
											$destination1 = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/alternative/'.($key_alt+1).'/media';
										}
										if(!is_dir($destination1)) {
											mkdir($destination1);
										}
										extract_copy_files($source1,$destination1);

										if(isset($media->{'meta-data'})){
											wrt2file($destination1.'/response_meta_data.txt' ,json_encode($media->{'meta-data'}));
										}
									}
								}

							}elseif(isset($alt->response->media)&&isset($alt->response->media->{"@detailedtype"})){
								if(isset($alt->response->media->{"@url"}) && is_string($alt->response->media->{"@url"}) && !empty($alt->response->media->{"@url"})){

									$key_folder = explode('.',$alt->response->media->{"@url"});
									$source1 = 'uploads/'.$mainfolder.'/medias/'.$key_folder[0];
									if($alt->response->media->{"@detailedtype"} == 'image'){
										$destination1 = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/alternative/'.($key_alt+1).'/img';
									}else if($alt->response->media->{"@detailedtype"} == 'sound'){
										$destination1 = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/alternative/'.($key_alt+1).'/audio';
									}else{
										$destination1 = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/alternative/'.($key_alt+1).'/media';
									}
									if(!is_dir($destination1)) {
										mkdir($destination1);
									}
									extract_copy_files($source1,$destination1);

									if(isset($alt->response->media->{'meta-data'})){
										wrt2file($destination1.'/response_meta_data.txt' ,json_encode($alt->response->media->{'meta-data'}));
									}
								}
							}
							if(isset($alt->response->text->{"#text"})){
								wrt2file('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/alternative/'.($key_alt+1).'/response_text.txt' , stripp($alt->response->text->{"#text"}));
							}else if(isset($alt->response->text)&&is_string($alt->response->text)&&!empty($alt->response->text)){
								wrt2file('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/alternative/'.($key_alt+1).'/response_text.txt' , stripp($alt->response->text));

							}else if(isset($alt->response)&&is_string($alt->response)&&!empty($alt->response)){
								wrt2file('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/alternative/'.($key_alt+1).'/response_text.txt' , stripp($alt->response));
							}
							if(isset($alt->response->graphiccaption)){
								if(isset($alt->response->graphiccaption->text) && !empty($alt->response->graphiccaption->text) && is_string($alt->response->graphiccaption->text)){
									wrt2file('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/alternative/'.($key_alt+1).'/text.txt' , stripp($alt->response->graphiccaption->text));
								}else if(isset($alt->response->graphiccaption) && !empty($alt->response->graphiccaption) && is_string($alt->response->graphiccaption)){
									wrt2file('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/alternative/'.($key_alt+1).'/text.txt' , stripp($alt->response->graphiccaption));
								}
							}
							if(isset($alt->response->graphictitle)){
								if(isset($alt->response->graphictitle->text) && !empty($alt->response->graphictitle->text) && is_string($alt->response->graphictitle->text)){
									wrt2file('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/alternative/'.($key_alt+1).'/text.txt' , stripp($alt->response->graphictitle->text));
								}else if(isset($alt->response->graphictitle) && !empty($alt->response->graphictitle) && is_string($alt->response->graphictitle)){
									wrt2file('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/alternative/'.($key_alt+1).'/text.txt' , stripp($alt->response->graphictitle));
								}
							}
						}
					}
				}else{
					$alt = $jsencode->exercise->alternatives->alternative;

					if( isset($alt->text->{"#text"}) || (isset($alt->media)&&isset($alt->media->{"@detailedtype"})) || (isset($alt->response)&&isset($alt->response->media)&&isset($alt->response->media->{"@detailedtype"}))){

							if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/alternative')) {
								mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/alternative');
							}
						}

						if(isset($alt->text->{"#text"})){
							wrt2file('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/alternative/text.txt' , stripp($alt->text->{"#text"}));
						}

						if(isset($alt->media)&&isset($alt->media->{"@detailedtype"})){
								$key_folder = explode('.',$alt->media->{"@url"});
								$source1 = 'uploads/'.$mainfolder.'/medias/'.$key_folder[0];
								if($alt->media->{"@detailedtype"} == 'image'){
									$destination1 = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/alternative/img';
								}else if($alt->media->{"@detailedtype"} == 'sound'){
									$destination1 = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/alternative/audio';
								}else{
									$destination1 = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/alternative/media';
								}
								if(!is_dir($destination1)) {
									mkdir($destination1);
								}
								extract_copy_files($source1,$destination1);
								if(isset($alt->media->{'meta-data'})){
									wrt2file($destination1.'/meta_data.txt' , json_encode($alt->media->{'meta-data'}));
								}
						}
						if(isset($alt->response)){
							if(isset($alt->response->media)&&isset($alt->response->media->{"@detailedtype"})){
								$key_folder = explode('.',$alt->response->media->{"@url"});
								$source1 = 'uploads/'.$mainfolder.'/medias/'.$key_folder[0];
								if($alt->response->media->{"@detailedtype"} == 'image'){
									$destination1 = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/alternative/img';
								}else if($alt->response->media->{"@detailedtype"} == 'sound'){
									$destination1 = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/alternative/audio';
								}else{
									$destination1 = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/alternative/media';
								}
								if(!is_dir($destination1)) {
									mkdir($destination1);
								}
								extract_copy_files($source1,$destination1);

								if(isset($alt->response->media->{'meta-data'})){
									wrt2file($destination1.'/response_meta_data.txt' ,json_encode($alt->response->media->{'meta-data'}));
								}
							}
							if(isset($alt->response->text->{"#text"})){
								wrt2file('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/alternative/response_text.txt' , stripp($alt->response->text->{"#text"}));
							}else if(isset($alt->response->text)&&is_string($alt->response->text)&&!empty($alt->response->text)){
								wrt2file('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/alternative/response_text.txt' , stripp($alt->response->text));

							}else if(isset($alt->response)&&is_string($alt->response)&&!empty($alt->response)){
								wrt2file('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/alternative/response_text.txt' , stripp($alt->response));
							}
						}
				}
			}

			if(isset($jsencode->exercise->assessment)){
				$assessment = $jsencode->exercise->assessment;
				if((isset($assessment->text)&&!empty($assessment->text)&&is_string($assessment->text)) || (isset($assessment->text)&&is_array($assessment->text))){
					if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/assessment')) {
						mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/assessment');
					}
				}
				if(isset($assessment->text)&&!empty($assessment->text)&&is_string($assessment->text)){
					wrt2file('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/assessment/text.txt' , stripp($assessment->text));
				}else if( isset($assessment->text)&&is_array($assessment->text) ){
					foreach($assessment->text as $key_as => $as){
						wrt2file('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/assessment/text.txt' , stripp($as));
					}
				}
			}

			if(isset($jsencode->exercise->containers->container)){

				if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/containers')) {
					mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/containers');
				}		

				if (is_array($jsencode->exercise->containers->container)) {
					// code...
					foreach($jsencode->exercise->containers->container as $k_c => $container){

						$container_folder = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/containers/'.($k_c+1).'/';
						
						$mediapathtxt = 'output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/containers/'.($k_c+1);
						if(isset($container->text)){
							if(isset($container->text) && is_array($container->text)){
								$text_g = '';
								foreach($container->text as $text){
									if(isset($text->{'#text'}) && !empty($text->{'#text'})){
										$text_g.=stripp($text->{'#text'})."\n";
									}
								}
								if(!empty($text_g)){
									if(!is_dir($container_folder)) {mkdir($container_folder);}
									wrt2file($mediapathtxt.'/text.txt',$text_g);
								}
							}else{
								if(isset($container->text->{"#text"}) && !empty($container->text->{"#text"}) && is_string($container->text->{"#text"})){
									if(!is_dir($container_folder)) {mkdir($container_folder);}
									wrt2file($mediapathtxt.'/text.txt',stripp($container->text->{"#text"}));
								}
								if(isset($container->text) && !empty($container->text) && is_string($container->text)){
									if(!is_dir($container_folder)) {mkdir($container_folder);}
									wrt2file($mediapathtxt.'/text.txt',stripp($container->text));
								}								
							}
						}
						if(isset($container->bubble)){
							if(isset($container->bubble->text) && is_string($container->bubble->text) && !empty($container->bubble->text)){
								if(!is_dir($container_folder)) {mkdir($container_folder);}
								wrt2file($mediapathtxt.'/text.txt',stripp($container->bubble->text));
							}
						}

						if(isset($container->graphiccaption)){
							if(isset($container->graphiccaption->text) && !empty($container->graphiccaption->text) && is_string($container->graphiccaption->text)){
								if(!is_dir($container_folder)) {mkdir($container_folder);}
								wrt2file($mediapathtxt.'/text.txt',stripp($container->graphiccaption->text));
							}else if(isset($container->graphiccaption) && !empty($container->graphiccaption) && is_string($container->graphiccaption)){
								if(!is_dir($container_folder)) {mkdir($container_folder);}
								wrt2file($mediapathtxt.'/text.txt',stripp($container->graphiccaption));
							}
						}
						if(isset($container->graphictitle)){
							if(isset($container->graphictitle->text) && !empty($container->graphictitle->text) && is_string($container->graphictitle->text)){
								if(!is_dir($container_folder)) {mkdir($container_folder);}
								wrt2file($mediapathtxt.'/text.txt',stripp($container->graphictitle->text));
							}else if(isset($container->graphictitle) && !empty($container->graphictitle) && is_string($container->graphictitle)){
								if(!is_dir($container_folder)) {mkdir($container_folder);}
								wrt2file($mediapathtxt.'/text.txt',stripp($container->graphictitle));
							}
						}
						if(isset($container->navigationButtonLabel)){
							if(isset($container->navigationButtonLabel->text) && !empty($container->navigationButtonLabel->text) && is_string($container->navigationButtonLabel->text)){
								if(!is_dir($container_folder)) {mkdir($container_folder);}
								wrt2file($mediapathtxt.'/text.txt',stripp($container->navigationButtonLabel->text));
							}else if(isset($container->navigationButtonLabel) && !empty($container->navigationButtonLabel) && is_string($container->navigationButtonLabel)){
								if(!is_dir($container_folder)) {mkdir($container_folder);}
								wrt2file($mediapathtxt.'/text.txt',stripp($container->navigationButtonLabel));
							}
						}


						if(isset($container->media)){
							if(is_array($container->media)){
								//if the media have multiple entries.
								if(!is_dir($container_folder)) {mkdir($container_folder);}
								foreach ($container->media as $intervalue) {

									if(!empty($intervalue->urls->url)){

										$tempextras = explode('.',$intervalue->{"@url"});
										if($intervalue->{"@type"} == "sound"){

											if(!is_dir($mediapathtxt.'/audio')) {
												mkdir($mediapathtxt.'/audio');
											}
											if(!is_dir($mediapathtxt.'/audio/'.$intervalue->{"@id"})) {
												mkdir($mediapathtxt.'/audio/'.$intervalue->{"@id"}.'/');
											}

											$mediapathtxt_ = $mediapathtxt.'/audio/'.$intervalue->{"@id"}."/meta-data.txt";

											$objarray = get_object_vars($intervalue);

											wrt2file($mediapathtxt_,json_encode($objarray['meta-data']));

											if(!empty($intervalue->{"@url"})){

												if(!empty($tempextras)){
													$source1 = 'uploads/'.$mainfolder.'/medias/'.$tempextras[0];
													$destination1 = $mediapathtxt.'/audio/'.$intervalue->{"@id"}.'/';

													extract_copy_files($source1,$destination1);
												}
											}
										}

										if($intervalue->{"@type"} == "movie"){
											$tempextras2 = explode('.',$intervalue->{"@url"});
											if(!is_dir($mediapathtxt.'/img')) {
												mkdir($mediapathtxt.'/img');
											}
											if(!is_dir($mediapathtxt.'/img/'.$intervalue->{"@id"})) {
												mkdir($mediapathtxt.'/img/'.$intervalue->{"@id"}.'/');
											}

											$mediapathtxt_ = $mediapathtxt.'/img/'.$intervalue->{"@id"}."/meta-data.txt";

											$objarray = get_object_vars($intervalue);

											wrt2file($mediapathtxt_,json_encode($objarray['meta-data']));

											if(!empty($tempextras2)){

												$source1 = 'uploads/'.$mainfolder.'/medias/'.$tempextras2[0];
												$destination1 = $mediapathtxt.'/img/'.$intervalue->{"@id"};

												extract_copy_files($source1,$destination1);
											}
										}

										//----------------------
										if($intervalue->{"@detailedtype"} == "image"){

											$mediapath = explode('.',$intervalue->{"@url"});
											if(!is_dir($mediapathtxt.'/img')) {
												mkdir($mediapathtxt.'/img');
											}
											if(!is_dir($mediapathtxt.'/img/'.$intervalue->{"@id"})) {
												mkdir($mediapathtxt.'/img/'.$intervalue->{"@id"}.'/');
											}

											$mediapathtxt_ = $mediapathtxt.'/img/'.$intervalue->{"@id"}."/meta-data.txt";

											$objarray = get_object_vars($intervalue);

											wrt2file($mediapathtxt_,json_encode($objarray['meta-data']));

											if(!empty($mediapath)){

												$source1 = 'uploads/'.$mainfolder.'/medias/'.$mediapath[0];
												$destination1 = $mediapathtxt.'/img/'.$intervalue->{"@id"}.'/';

												extract_copy_files($source1,$destination1);
											}

										}

									}
								}
								//ends.
							}else{
								if(!empty($container->media->urls->url)){
									if(!is_dir($container_folder)) {mkdir($container_folder);}
									if($container->media->{"@detailedtype"} == "sound"){

										if(!is_dir($mediapathtxt.'/audio')) {
											mkdir($mediapathtxt.'/audio');
										}
										if(!is_dir($mediapathtxt.'/audio/'.$container->media->{"@id"})) {
											mkdir($mediapathtxt.'/audio/'.$container->media->{"@id"}.'/');
										}

										$mediapathtxt_ = $mediapathtxt.'/audio/'.$container->media->{"@id"}."/meta-data.txt";

										$objarray = get_object_vars($container->media);

										wrt2file($mediapathtxt_,json_encode($objarray['meta-data']));

										if(!empty($container->media->{"@url"})){

											if(!empty($mediapath)){
												$source1 = 'uploads/'.$mainfolder.'/medias/'.$mediapath[0];
												$destination1 = $mediapathtxt.'/audio/'.$container->media->{"@id"}.'/';

												extract_copy_files($source1,$destination1);
											}
										}
									}
									if($container->media->{"@detailedtype"} == "video"){
										if(!is_dir($mediapathtxt.'/video')) {
											mkdir($mediapathtxt.'/video');
										}
										if(!is_dir($mediapathtxt.'/video/'.$container->media->{"@id"})) {
											mkdir($mediapathtxt.'/video/'.$container->media->{"@id"}.'/');
										}

										$mediapathtxt_ = $mediapathtxt.'/video/'.$container->media->{"@id"}."/meta-data.txt";

										$objarray = get_object_vars($container->media);

										wrt2file($mediapathtxt_,json_encode($objarray['meta-data']));

										if(!empty($mediapath)){

											$source1 = 'uploads/'.$mainfolder.'/medias/'.$mediapath[0];
											$destination1 = $mediapathtxt.'/video/'.$container->media->{"@id"}.'/';

											extract_copy_files($source1,$destination1);
										}
									}
								}
							}
						}

						if(isset($container->image->media)){
							if(!empty($container->image->media->urls->url)){
								if(!is_dir($container_folder)) {mkdir($container_folder);}

								$mediapath = explode('.',$container->image->media->{"@url"});

								if($container->image->media->{"@detailedtype"} == "image"){

									if(!is_dir($mediapathtxt.'/image')) {
										mkdir($mediapathtxt.'/image');
									}
									if(!is_dir($mediapathtxt.'/image/'.$container->image->media->{"@id"})) {
										mkdir($mediapathtxt.'/image/'.$container->image->media->{"@id"}.'/');
									}

									$mediapathtxt_ = $mediapathtxt.'/image/'.$container->image->media->{"@id"}."/meta-data.txt";

									$objarray = get_object_vars($container->image->media);

									wrt2file($mediapathtxt_,json_encode($objarray['meta-data']));

									if(!empty($container->image->media->{"@url"})){

										if(!empty($mediapath)){
											$source1 = 'uploads/'.$mainfolder.'/medias/'.$mediapath[0];
											$destination1 = $mediapathtxt.'/image/'.$container->image->media->{"@id"}.'/';

											extract_copy_files($source1,$destination1);
										}
									}
								}
								if($container->image->media->{"@detailedtype"} == "sound"){

									if(!is_dir($mediapathtxt.'/audio')) {
										mkdir($mediapathtxt.'/audio');
									}
									if(!is_dir($mediapathtxt.'/audio/'.$container->image->media->{"@id"})) {
										mkdir($mediapathtxt.'/audio/'.$container->image->media->{"@id"}.'/');
									}

									$mediapathtxt_ = $mediapathtxt.'/audio/'.$container->image->media->{"@id"}."/meta-data.txt";

									$objarray = get_object_vars($container->image->media);

									wrt2file($mediapathtxt_,json_encode($objarray['meta-data']));

									if(!empty($container->image->media->{"@url"})){

										if(!empty($mediapath)){
											$source1 = 'uploads/'.$mainfolder.'/medias/'.$mediapath[0];
											$destination1 = $mediapathtxt.'/audio/'.$container->image->media->{"@id"}.'/';

											extract_copy_files($source1,$destination1);
										}
									}
								}
								if($container->image->media->{"@detailedtype"} == "video"){
									if(!is_dir($mediapathtxt.'/video')) {
										mkdir($mediapathtxt.'/video');
									}
									if(!is_dir($mediapathtxt.'/video/'.$container->image->media->{"@id"})) {
										mkdir($mediapathtxt.'/video/'.$container->image->media->{"@id"}.'/');
									}

									$mediapathtxt_ = $mediapathtxt.'/video/'.$container->image->media->{"@id"}."/meta-data.txt";

									$objarray = get_object_vars($container->image->media);

									wrt2file($mediapathtxt_,json_encode($objarray['meta-data']));

									if(!empty($mediapath)){

										$source1 = 'uploads/'.$mainfolder.'/medias/'.$mediapath[0];
										$destination1 = $mediapathtxt.'/video/'.$container->image->media->{"@id"}.'/';

										extract_copy_files($source1,$destination1);
									}
								}
							}
						}

						if(isset($container->alternatives->alternative)){
							if(is_array($container->alternatives->alternative)){
								if(!is_dir($container_folder)) {mkdir($container_folder);}
								if(!is_dir($mediapathtxt.'/alternative/')) {
									mkdir($mediapathtxt.'/alternative/');
								}
								foreach($container->alternatives->alternative as $k_alt => $alternative){
									$alt_folder = $mediapathtxt.'/alternative/'.($k_alt+1);
									if(!is_dir($alt_folder.'/')) {
										mkdir($alt_folder.'/');
									}
									$text_file = $alt_folder.'/text.txt';

									if(isset($alternative->text) && is_array($alternative->text)){
										$text_g = '';
										foreach($alternative->text as $text){
											if(isset($text->{'#text'}) && !empty($text->{'#text'})){
												$text_g.=stripp($text->{'#text'})."\n";
											}
										}
										if(!empty($text_g)){
											wrt2file($text_file,$text_g);
										}
									}else{

										if(isset($alternative->text->{"#text"}) && !empty($alternative->text->{"#text"}) && is_string($alternative->text->{"#text"})){
											wrt2file($text_file,stripp($alternative->text->{"#text"}));
										}
										if(isset($alternative->{"#text"}) && !empty($alternative->{"#text"}) && is_string($alternative->{"#text"})){
											wrt2file($text_file,stripp($alternative->{"#text"}));
										}
										if(isset($alternative->text) && !empty($alternative->text) && is_string($alternative->text)){
											wrt2file($text_file,stripp($alternative->text));
										}
									}
								}
							}else{
								if(!is_dir($container_folder)) {mkdir($container_folder);}
								if(!is_dir($mediapathtxt.'/alternative/')) {
									mkdir($mediapathtxt.'/alternative/');
								}
								$alternative = $container->alternatives->alternative;
								$k_alt=0;
								$alt_folder = $mediapathtxt.'/alternative/'.($k_alt+1);
									if(!is_dir($alt_folder.'/')) {
										mkdir($alt_folder.'/');
									}
									$text_file = $alt_folder.'/text.txt';

									if(isset($alternative->text) && is_array($alternative->text)){
										$text_g = '';
										foreach($alternative->text as $text){
											if(isset($text->{'#text'}) && !empty($text->{'#text'})){
												$text_g.=stripp($text->{'#text'})."\n";
											}
										}
										if(!empty($text_g)){
											wrt2file($text_file,$text_g);
										}
									}else{

										if(isset($alternative->text->{"#text"}) && !empty($alternative->text->{"#text"}) && is_string($alternative->text->{"#text"})){
											wrt2file($text_file,stripp($alternative->text->{"#text"}));
										}
										if(isset($alternative->{"#text"}) && !empty($alternative->{"#text"}) && is_string($alternative->{"#text"})){
											wrt2file($text_file,stripp($alternative->{"#text"}));
										}
										if(isset($alternative->text) && !empty($alternative->text) && is_string($alternative->text)){
											wrt2file($text_file,stripp($alternative->text));
										}
									}
							}
						}

					}
				}
			}

			if(isset($jsencode->exercise->embedded)){
				if(isset($jsencode->exercise->embedded->{"@src"})&&!empty($jsencode->exercise->embedded->{"@src"})){
					if(!is_dir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/embedded')) {
						mkdir('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/embedded');
					}
					wrt2file('output/'.$mainfolder.'/'.$dataobject->name.'/'.$value->title.'/embedded/src.txt' , $jsencode->exercise->embedded->{"@src"});
				}
			}
		}//End of foreach.

	}
}

function copy_theme_images($mainfolder){
	// find images in themes folder
	$all_images = glob('uploads/'.$mainfolder.'/themes/custom/**/res/images/*.*');
	$all_images = array_unique($all_images);
	if(!is_dir('output/'.$mainfolder.'/theme_images') && count($all_images)>0){
		mkdir('output/'.$mainfolder.'/theme_images');
	}
	foreach($all_images as $img){
		$extension = pathinfo($img, PATHINFO_EXTENSION);
		$filename = basename($img, '.'.$extension);
		copy($img, 'output/'.$mainfolder.'/theme_images/'.$filename.'.'.$extension);
	}
	$preview_image = glob('uploads/'.$mainfolder.'/themes/custom/**/*.*');
	foreach($preview_image as $img){
		$extension = pathinfo($img, PATHINFO_EXTENSION);
		$filename = basename($img, '.'.$extension);
		copy($img, 'output/'.$mainfolder.'/theme_images/'.$filename.'.'.$extension);
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
	$string = str_replace('.','',$string);
	$string = preg_replace("/[^a-z0-9\_\-\.]/iu", '', $string);
	if(strlen($string)>50){
		$string = substr($string, 0, 50);
	}
	return $string;
}

// remove directory and sub itemss
function rmrf($dir) {
    foreach (glob($dir) as $file) {
        if (is_dir($file)) { 
            rmrf($file."/*");
            rmdir($file);
        } else {
            unlink($file);
        }
    }
}

// replace p tags with new line
function stripp($string){
	$string = str_replace('<P>', '', $string);
	$string = str_replace('<LI>', '-- ', $string);
	$string = str_replace('<br>', PHP_EOL, $string);
	$string = str_replace(array('</P>','</LI>'), PHP_EOL , $string);
	return strip_tags($string);
}

// write to file
function wrt2file($file, $text){

	if(!file_exists($file)){
		$myfile = fopen($file, "w+")  ;
	}else{
		$myfile = fopen($file, "a")  ;
	}

	fwrite($myfile, htmlspecialchars_decode(clean_uni($text))."\n");	
	fclose($myfile);
}
function clean_uni($string){
	return str_ireplace(array('&nbsp;'), ' ', $string);
}