<?php

/**
 * Utility class for working with event upload files.
 * 
 * @author wtaylor
 *
 */
class FileUtil
{
	/**
	 * called when an event is initially created.
	 * @param $event
	 */
	public static function createEventDir($event) {
		$dir = self::getEventFilesDir($event);
		mkdir($dir);
		chmod($dir, 0775);
	}
	
	/**
	 * called when the code for an event is changed.
	 * @param $event
	 */
	public static function renameEventDir($oldEvent, $event) {
		if($oldEvent['code'] !== $event['code']) {
			$oldPath = self::getEventFilesDir($oldEvent);
			$newPath = self::getEventFilesDir($event);
			
			// if the event files dir exists then rename it.
			// if the files dir doesn't exist for whatever
			// reason, then create it.
			if(file_exists($oldPath)) {
				return rename($oldPath, $newPath);
			}
			else {
				return self::createEventDir($event);
			}
		}
		
		return true;
	}
	
	public static function deleteEventDir($event) {
		$path = self::getEventFilesDir($event);
		$dirContents = scandir($path);
		
		if($dirContents === FALSE) { return;}
			
		foreach($dirContents as $entry) {
			if(in_array($entry, array('.', '..'))) { continue;}

			unlink($path.'/'.$entry);
		}
		
		rmdir($path);
	}
	
	public static function saveEventFile($event, $file) {
		$path = self::getEventFilesDir($event).'/'.basename($file['name']);
		
		return move_uploaded_file($file['tmp_name'], $path);	
	}
	
	public static function deleteEventFile($event, $fileName) {
		$path = self::getEventFilesDir($event).'/'.basename($fileName);
		
		return unlink($path);
	}
	
	public static function getEventFiles($event) {
		$files = array();
		
		foreach(scandir(self::getEventFilesDir($event)) as $f) {
			if($f !== '.' && $f !== '..') {
				$files[] = $f;
			}
		}
		
		return $files;
	}
	
	public static function getEventFilesDir($event) {
		if(empty($event)) {
			throw new Exception('Error getting event files dir.');
		}
		
		return Reggie::$PATH.'/files/'.$event['code'];
	}
}

?>