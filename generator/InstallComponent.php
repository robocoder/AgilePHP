<?php 

require_once 'util' . DIRECTORY_SEPARATOR . 'AgilePHPGen.php';
require_once 'util' . DIRECTORY_SEPARATOR . 'AppstoreAPI.php';
require_once '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'AgilePHP.php';

class InstallComponent extends AgilePHPGen {

	  private $api;
	  private $config;

	  /**
	   * Initalizes the installer
	   * 
	   * @return void
	   */
	  public function __construct() {

	  		 parent::__construct();

	  		 $file = '.openappstore-config';

	  		 if( !file_exists( $file ) ) {

	  		 	 $defaultEndpoint = 'http://10.255.1.81:8080/appstore/api?wsdl';

	  		 	 $config = new OpenAppstoreConfig();
	  		 	 
	  		 	 $endpoint = $this->prompt( 'Enter the OpenAppstore API endpoint address: (' . $defaultEndpoint . ')' );
	  		 	 if( !$endpoint ) $endpoint = $defaultEndpoint;

	  		 	 $config->setEndpoint( $endpoint );
	  		 	 $config->setUsername( $this->prompt( 'Enter your OpenAppstore username:' ) );
	  		 	 $config->setPassword( $this->prompt( 'Enter your OpenAppstore password:' ) );
	  		 	 $config->setApiKey( $this->prompt( 'Enter your OpenAppstore API key:' ) );
	  		 	 $platformId = $this->prompt( 'Enter the AgilePHP PlatformID in the AppStore: (2)' );
	  		 	 if( !$platformId ) $platformId = '2';
	  		 	 $config->setPlatformId( $platformId );

	  		 	 $h = fopen( $file, 'w' );
	  		 	 fwrite( $h, serialize( $config ) );
	  		 	 fclose( $h );

	  		 	 $this->config = $config;
	  		 }
	  		 else {

	  		 	 $data = '';
	  		 	 $h = fopen( $file, 'r' );
	  		 	 while( !feof( $h ) )
	  		 		$data = fgets( $h, 4096 );
	  		 	 fclose( $h );

	  		 	$this->config = unserialize( $data );
	  		 }
	  }

	  /**
	   * Installs a new AgilePHP component into the web application components directory.
	   * 
	   * @return void
	   */
	  public function testInstallComponent() {

	  		 $this->api = new AppstoreAPI( $this->config->getEndpoint(), array(
   						  		 'trace' => 1,
   						  		 'cache_wsdl' => 0,
   						  		 'soap_version' => SOAP_1_2,
   						  		 'encoding' => 'UTF-8',
   						   		 'style' => SOAP_DOCUMENT,
   						  		 'use' => SOAP_LITERAL,
   						  	 	 'features' => USE_SINGLE_ELEMENT_ARRAYS ));

	  		 PHPUnit_Framework_Assert::assertNotNull( $this->api, 'Error creating AppstoreAPI' );

			 $login = new login();
			 $login->username = $this->config->getUsername();
			 $login->password = $this->config->getPassword();
			 $login->apiKey = $this->config->getApiKey();

			 PHPUnit_Framework_Assert::assertTrue( $this->api->login( $login )->return, 'Error logging into OpenAppstore WebService API' );

			 $pid = new getAppsByPlatformId();
			 $pid->id = $this->config->getPlatformId();

			 $response = $this->api->getAppsByPlatformId( $pid );

			 $this->showApps( $response );
			 $index = $this->prompt( '' );
			 $index--;

			 if( !array_key_exists( $index, $response->return ) )
			 	 PHPUnit_Framework_Assert::fail( 'Invalid selection' );

			 $this->install( $response->return[$index] );
	  }

	  /**
	   * Gives a list of AgilePHP components available for installation.
	   * 
	   * @param getAppsByPlatformIdResponse The response from getAppsByPlatformId call
	   * @return void
	   */
	  private function showApps( getAppsByPlatformIdResponse $response ) {

	  		  echo "Select a component to install:\n";
	  		  for( $i=0; $i<count( $response->return ); $i++ )
			 	   echo '[' . ($i+1) . '] ' . $response->return[$i]->name . "\n";
	  }
	  
	  /**
	   * Downlaods and installs a component to the web application components directory.
	   * 
	   * @return void
	   */
	  private function install( appWS $app ) {

	  		  $file = $this->download( $app->id, $app->appId );

			  if( !$this->unzip( $file ) )
	              PHPUnit_Framework_Assert::fail( 'Could not extract downloaded component \'' . $file . '\'.' );

	          $this->copyControllers( $app->appId );
	  }

	  /**
	   * Downloads a component to the web application components directory
	   * 
	   * @param $id The id of the application in OpenAppstore
	   * @param $appId The appId of the application in OpenAppstore
	   * @return The file path to the downloaded file
	   */
	  private function download( $id, $appId ) {

	  		  echo "Downloading $appId...\n";

	  	 	  $download = new download();
	  	 	  $download->id = $id;

	  		  $data = $this->api->download( $download );
	  		  
	  		  if( !$this->getCache() )
	  		  	  PHPUnit_Framework_Assert::fail( 'You must first have a project created before being able to install components' );

	  		  $file = $this->getCache()->getProjectRoot() . '/components/' . $appId . '.zip';
	  		  $h = fopen( $file, 'w' );	  		  
	  		  fwrite( $h, $data->return );
	  		  fclose( $h );

	  		  $isDownloaded = file_exists( $file );
	  		  PHPUnit_Framework_Assert::assertTrue( $isDownloaded, 'Failed to download component to project directory' );

	  		  return $file;
	  }

	  /**
	   * Unzips a downloaded component
	   * 
	   * @param $file The file path of the archive to extract
	   * @return True if the archive was successfully extracted or false if on failure
	   */
	  private function unzip( $file ) {

	  		  echo "Extracting archive...\n";

			  $zip = new ZipArchive();

              if( $zip->open( $file ) === TRUE ) {

                   $zip->extractTo( $this->getCache()->getProjectRoot() . '/components' );
                   $zip->close();

                   unlink( $file );
                   return true;
              }

              // try to unzip using shell as last resort
              exec( 'cd ' . $this->getCache()->getProjectRoot() . "/components; unzip " . $file, $output );
              if( !$output ) return false;

              unlink( $file );
              return true;
	  }

	  private function copyControllers( $componentName ) {

	  		  $it = new RecursiveDirectoryIterator( $this->getCache()->getProjectRoot() . '/components/' . $componentName );
		 	  foreach( new RecursiveIteratorIterator( $it ) as $file ) {

		   	      	   if( substr( $file, -1 ) != '.' && substr( $file, -2 ) != '..' ) {

				 		   if( strtolower( basename( $file ) ) == 'applicationcontroller.php' ) {

				 		   		copy( $file, $this->getCache()->getProjectRoot() . '/control/' . $componentName . 'Controller.php' );
				 		   		unlink( $file );
				 		   }
		   	      	   }
		 	  }
	  }
}

class OpenAppstoreConfig {

	  private $endpoint;
	  private $username;
	  private $password;
	  private $apikey;
	  private $platformId;
	  
	  public function __construct() { }

	  public function setEndpoint( $endpoint ) {
	  	
	  		 $this->endpoint = $endpoint;
	  }
	  
	  public function getEndpoint() {
	  	
	  		 return $this->endpoint;
	  }

	  public function getUsername() {

	  		 return $this->username;
	  }

	  public function setUsername( $username ) {

	  		 $this->username = $username;
	  }
	  
	  public function setPassword( $password ) {

	  		 $this->password = $password;
	  }
	  
	  public function getPassword() {
	  	
	  		 return $this->password;
	  }
	  
	  public function setApiKey( $key ) {
	  	
	  		 $this->apikey = $key;
	  }
	  
	  public function getApiKey() {

	  		 return $this->apikey;
	  }
	  
	  public function setPlatformId( $id ) {
	  	
	  		 $this->platformId = $id;
	  }
	  
	  public function getPlatformId() {
	  	
	  		 return $this->platformId;
	  }
}
?>