<?php

/**
 *	Process input for "After Douglas Davis"
 *	@info	This script is custom, though it attempts to stay true to original functionality of the project
 *	@author	Owen Mundy
 *	@date	5-Sept-2013
 */
class Process {

	public function processInput(){
	
		// if input exists
		if (isset($_POST['sentence']) && $_POST['sentence'] != ''){
			
			// object to hold data
			$data = new stdClass();
				
			// get current sentence page number and filename
			$data->filenum = trim(file_get_contents('info.txt'));
			$data->filename = 'sentence'. $data->filenum .'.html';
			
			// clean input
			require_once 'htmlpurifier-4.5.0/library/HTMLPurifier.auto.php';
			$purifier = new HTMLPurifier();
			$data->input = $purifier->purify( $_POST['sentence'] );
			
			// combine and store sentence
			$data->sentence = file_get_contents($data->filename) ."<p>$data->input</p>\n\n";
			file_put_contents($data->filename, $data->sentence);
			
			// count chars
			$data->count = strlen(file_get_contents($data->filename));
				
			// make new page if current one is too big
			if ($data->count > 20000){
				
				// finish last file
				$data->nextfile = 'sentence'. ++$data->filenum .'.html';
				file_put_contents($data->filename, "$data->sentence\n\n"
								  ."<p><a href='$data->nextfile'>Next part of Sentence</a></p></body></html>\n\n");
				// make new file
				file_put_contents($data->nextfile, "<!DOCTYPE html><html><head><meta charset='utf-8'></head><body>\n\n"
								  ."<p><a href='$data->filename'>previous part of sentence</a></p>\n\n");
				// update info.txt
				file_put_contents('info.txt', $data->filenum);
			}
			return $data;
		}
	}
}
$process = new Process();
if ($data = $process->processInput()){
	//print_r($data);
	header('Location: '.$data->filename);
	exit;
} else {
	die ("no input received");	
}


?>


