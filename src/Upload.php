<?php
/**
 * AgilePHP Framework :: The Rapid "for developers" PHP5 framework
 * Copyright (C) 2009-2010 Make A Byte, inc
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package com.makeabyte.agilephp
 */

/**
 * File upload component
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp
 */
class Upload {

	  private $inputName;
	  private $uploadedFilename;
	  private $localFilename;
	  private $contentType;
	  private $size;
	  private $directory;
	  private $extension;
	  private $allowedExtensions;
	  private $maxSize;

	  /**
	   * Sets the name used in the file form field element.
	   * 
	   * @param String $name The name attribute of the HTML file input element
	   * @return void
	   */
	  public function setInputName($name) {
		   $this->inputName = $name;
	  }

	  /**
	   * Returns the file form name.
	   * 
	   * @return String The name attribute of the HTML file input element
	   */
	  public function getInputName() {
            return $this->inputName;
	  }

	  /**
	   * Total number of bytes which the upload may not exceed
	   * 
	   * @param int $bytes Maximum number of bytes
	   * @return void
	   */
	  public function setMaxSize($bytes) {
	  	   $this->maxSize = $bytes;
	  }

	  /**
	   * Sets the destination directory for the upload.
	   * 
	   * @param String $directory The upload destination directory
	   * @return void
	   */
	  public function setDirectory($directory) {

	  	   $this->directory = $directory;

           if(!file_exists($directory))
              if(!mkdir($directory, true))
                 throw new UploadException('Upload directory does not exist and an attempt to create it failed.');
	  }

	  /**
	   * Returns the mime content type
	   * 
	   * @return String The content type
	   */
	  public function getContentType() {
	  	  return $this->contentType;
	  }

	  /**
	   * Returns the extension of the uploaded file
	   * 
	   * @return String The uploaded file extension
	   */
	  public function getExtension() {
	  	  return $this->extension;
	  }

	  /**
	   * Returns the total size (in bytes) of the upload
	   * 
	   * @return int The total number of bytes
	   */
	  public function getSize() {
	  	  return $this->size;
	  }

	  /**
	   * Returns the uploaded file name (as it appeared to the user)
	   * 
	   * @return String The uploaded file name
	   */
	  public function getUploadedFilename() {
	  	  return $this->uploadedFilename;
	  }

	  /**
	   * Returns the file name as it was saved on the local file system
	   * 
	   * @return String The local file name
	   */
	  public function getLocalFilename() {
	  	  return $this->localFilename;
	  }

	  /**
	   * Returns the destination directory for uploads.
	   * 
	   * @return String The destination directory path
	   */
	  public function getDirectory() {
		    return $this->directory;
	  }

	  /**
	   * Sets the extensions allowed to be uploaded
	   * 
	   * @param array $extensions
	   */
	  public function setAllowedExtensions(array $extensions) {
  	        $this->allowedExtensions = $extensions;
	  }

	  /**
	   * Saves the upload contained in the $_FILES array for the specified
	   * file input $name.
	   *
	   * @param boolean $overwrite Optional flag used to toggle overwriting. Defaults to false (don't overwrite).
	   * @param String $filename Optional file name to save the upload as. Defaults to the name of the uploaded file.
	   * @return String The uploaded file path.
	   * @throws ORMException if any errors occur
	   */
	  public function save($overwrite = false, $filename = null) {

	  	    if(!$filename) {

	  	    	$pieces = explode(DIRECTORY_SEPARATOR, $_FILES[$this->inputName]['name']);
	  	        $filename = $pieces[count($pieces)-1];
	  	        $this->localFilename = $filename;
	  	    }

	  		$pieces = explode('.', $filename);
	  		if(count($pieces) > 1) $this->extension = $pieces[count($pieces) - 1];
	  		$this->uploadedFilename = $_FILES[$this->inputName]['name'];
	  		$this->size = $_FILES[$this->inputName]['size'];
	  		$this->contentType = $_FILES[$this->inputName]['type'];

	  		// Check size restriction if present
	  		if($this->maxSize && $this->size > $this->maxSize)
	  		   throw new UploadException('File too large. Must not exceed ' . $this->maxSize . ' bytes.');

	  		// Check allowed extension(s) if configured
	  		if($this->allowedExtensions && $this->extension)
	  		   if(!in_array(strtolower($this->extension), $this->allowedExtensions))
	  		      throw new UploadException($this->extension . ' files are not allowed.');

			$target = $this->directory . DIRECTORY_SEPARATOR . $filename;

			// Make sure the file doesn't exist if not overwriting
			if(file_exists($target) && $overwrite == false)
			   throw new UploadException('The uploaded file already exists');

			Log::debug('Upload::save Saving upload with input name \'' . $this->inputName . '\' to target path \'' . $target . '\'.');

			if(!move_uploaded_file($_FILES[$this->inputName]['tmp_name'], $target)) {

			 	 switch($_FILES[$this->inputName]['error']) {

			 	 	case 1:
			 	 		$error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini.';
			 	 		break;
			 	 		
			 	 	case 2:
			 	 		 $error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
			 	 		 break;
			 	 		 
			 	 	case 3:
			 	 		 $error = 'The uploaded file was only partially uploaded.';
			 	 		 break;
			 	 		 
			 	 	case 4:
			 	 		 $error = 'No file was uploaded.';
			 	 		 break;
			 	 		 
			 	 	case 5:
			 	 		 $error = 'Missing a temporary folder.'; // Introduced in PHP 4.3.10 and PHP 5.0.3
			 	 		 break;
			 	 		 
			 	 	case 6:
			 	 		 $error = 'Failed to write file to disk.'; // Introduced in PHP 5.1.0
			 	 		 break;
			 	 		 
			 	 	case 7:
			 	 		 $error = 'File upload stopped by extension.'; // Introduced in PHP 5.2.0
			 	 		 break;
			 	 }

			 	 if(!isset($error)) return;

			 	 Log::debug('Upload::save Upload failed with code \'' . $_FILES[$this->inputName]['error'] . '\' and message \'' . $error . '\'.');
			 	 throw new UploadException($error, $_FILES[$this->inputName]['error']);
			}

			chmod($target, 0755);
			Log::debug('Upload::save Upload successfully saved');

			return $target;
	  }

	  /**
	   * Deletes the uploaded file and logs the event.
	   * 
	   * @return void
	   */
	  public function delete() {

	  	   if(!unlink($this->directory .'/' . $_FILES[$this->inputName]['name']))
              Log::debug('Upload::delete Failed to delete upload');
           else
              Log::debug('Upload::delete Delete successful');
	  }
}
?>