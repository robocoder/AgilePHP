<?php 

class FileUtils {

	  /**
	   * Performs a recursive delete
	   * 
	   * @param $src The tree source path to delete (colons substitutes for /)
	   * @return void
	   * @throws AgilePHP_Exception
	   */
	  public static function delete( $src ) {

	  		 Logger::getInstance()->debug( 'FileUtils::delete Performing recursive delete on source \'' . $src . '\'.' );

	  		 $dir = opendir( $src );
			 while( false !== ( $file = readdir( $dir ) ) ) {

			     	if( $file != '.' && $file != '..') {

			            if ( is_dir( $src . '/' . $file) )
			                FileUtils::delete( $src . '/' . $file );
			            else {
			                
			            	if( !unlink( $src . '/' . $file ) ) {
			            		
			            		Logger::getInstance()->debug( 'Failed to delete file ' . $src . '/' . $file );
			                	throw new AgilePHP_Exception( 'Could not delete file ' . $src . '/' . $file );
			            	}
			            }
			        }
			 }
			 return rmdir( $src );
	  }

	  /**
	   * Performs recursive copy
	   * 
	   * @param $src The source to copy
	   * @param $dst The destination
	   * @return void
	   */
	  public static function copy( $src, $dst ) {

		     $dir = opendir( $src );
			 mkdir( $dst );
			 while( false !== ( $file = readdir( $dir ) ) ) {

			      	if( $file != '.' && $file != '..' && substr( $file, 0, 4 ) != '.svn' ) {

			            if( is_dir( $src . DIRECTORY_SEPARATOR . $file ) )

			             	FileUtils::copy( $src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file );
			            else {

			             	copy( $src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file );
			            }
			        }
			 }
			 closedir( $dir );
	  }
}
?>