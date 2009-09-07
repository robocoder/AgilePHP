<?php 

class Mailer extends Component {

	  private $name = 'Mailer';
	  
	  private $to;
	  private $toName;
	  private $from;
	  private $fromName;
	  private $subject;
	  private $body;

	  public function __construct() { }

	  /**
	   * (non-PHPdoc)
	   * @see AgilePHP/Component#getName()
	   */
	  public function getName() {

	  	 	 return $this->name;
	  }
	  
	  public function setName( $name ) {
	  	
	  	     $this->name = $name;
	  }
	  
	  public function setTo( $toEmail ) {
	  	
	  		 $this->to = $toEmail;
	  }

	  public function setToName( $toName ) {

	  		 $this->toName = $toName;
	  }

	  public function setFrom( $fromEmail ) {
	  	
	  		 $this->from = $fromEmail;
	  }
	  
	  public function setFromName( $fromName ) {
	  	
	  		 $this->fromName = $fromName;
	  }
	  
	  public function setSubject( $subject ) {
	  	
	  		 $this->subject = $subject;
	  }
	  
	  public function setBody( $body ) {
	  	
	  		 $this->body = $body;
	  }

	  public function send() {

	  		 $headers = 'From: ' . $this->fromName . ' <' . $this->from . '>' . "\n";
	  		 $headers .= 'To: ' . $this->toName . ' <' . $this->to . '>' . "\n";
        	 $headers .= 'Reply-To: ' . $this->from . "\n";
          	 $headers .= 'Return-Path: ' . $this->from . "\n";
        	 $headers .= 'X-mailer: AgilePHP Framework on PHP (' . phpversion() . ')' . "\n";

        	 if( !mail( $this->to, $this->subject, $this->body, $headers ) )
        	 	 throw new AgilePHP_Exception( 'Error sending email' );
	  }
}
?>