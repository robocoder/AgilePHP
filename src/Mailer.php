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
 * Sends plain text, html, and multipart emails with support for multiple attachments.
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp
 */
class Mailer {

      private $contentType;  // 1 = text, 2 = html, 3 = multipart/mixed, 4 = multipart/alternative

      private $to;
      private $toName;
      private $from;
      private $fromName;
      private $cc;
      private $bcc;
      private $subject;
      private $html;
      private $text;
      private $attachments = array();
      private $listener;

      /**
       * Sets the email address of the person receiving the email
       *
       * @param string $email The recipients email address
       * @return void
       */
      public function setTo($email) {
          $this->to = $email;
      }

      /**
       * Sets the name of the person receiving the email
       *
       * @param string $name The recipients name
       * @return void
       */
      public function setToName($name) {
          $this->name = $name;
      }

      /**
       * Sets the email of the person sending the email
       *
       * @param string $email The senders email address
       * @return void
       */
      public function setFrom($email) {
          $this->from = $email;
      }

      /**
       * Sets the name of the person sending the email
       *
       * @param string $name The senders name
       * @return void
       */
      public function setFromName($name) {
          $this->fromName = $name;
      }

      /**
       * Sets the subject line of the email
       *
       * @param string $subject The text to display in the subject line
       * @return void
       */
      public function setSubject($subject) {
          $this->subject = $subject;
      }

      /**
       * Sets the list of carbon copy recipeints
       *
       * @param String $ccList Comma separated list of email recipeients
       * @return void
       */
      public function setCC($ccList) {
          $this->cc = $ccList;
      }

      /**
       * Sets the list of blind carbon copy recipeints
       *
       * @param String $ccList Comma separated list of email recipeients
       * @return void
       */
      public function setBCC($bccList) {
          $this->bcc = $bccList;
      }

	  /**
       * Sets the body text of the email. Alias for setText.
       *
       * @param String $text The email body text
       * @return void
       */
      public function setBody($text) {
          $this->contentType = 1;
          $this->text = $text;
      }

      /**
       * Sets the specified text as the email body. If the email is sent as multipart,
       * this text will be used in the plain text part of the email.
       * 
       * @param String $text The text to send in the email
       * @return void
       */
      public function setText($text) {
          $this->contentType = 1;
          $this->text = $text;
      }

      /**
       * Sets the specified HTML as the email body. If the email is sent as multipart,
       * this HTML will be used in the HTML part of the email.
       * 
       * @param String $html The HTML to send in the email
       * @return void
       */
      public function setHtml($html) {
          $this->contentType = 2;
          $this->html = $html;
      }

      /**
       * Attaches the specified file to the email
       *
       * @param MailerAttachment $attachment The email attachment to add
       * @return void
       */
      public function addAttachment(MailerAttachment $attachment) {
          array_push($this->attachments, $attachment);
      }

      /**
       * Attaches the specified list of file attachments to the email
       * 
       * @param array<MailerAttachment> $attachments The list of attachments
       * @return void 
       */
      public function setAttachments(array $attachments) {
          
          foreach($attachments as $attachment)
              if(!$attachment instanceof MailerAttachment)
                 throw new EmailException('Attachment must be an instance of MailerAttachment', 100);

          $this->attachments = $attachments;
      }

      /**
       * Sets the email event handler responsible for handling raised email events
       * 
       * @param EmailEventListener $listener The email listener responsible for raised events
       * @return void
       */
      public function setListener(EmailEventListener $listener) {
          $this->listener = $listener;
      }

      /**
       * Gets the email event handler
       * 
       * @return The email event handler
       */
      public function getListener() {
          return $this->listener;
      }

      /**
       * Sends a simple plain text or HTML email without any attachments.
       *
       * @return void
       * @throws FrameworkException if there was an error sending the email
       */
      public function send() {

          $headers = $this->getCommonHeaders();

          // Process as multipart if any attachments have been defined
          if(isset($this->attachments[0])) {
             $this->sendMultipart();
             return;
          }

          if($this->contentType == 1) {

             $headers .= "Content-Type: text/plain\r\n";
             $message = $this->text;
          }
          elseif($this->contentType == 2) {

             $headers .= "MIME-Version: 1.0\r\n";
             $headers .= "Content-Type: text/html\r\n";
             $message = $this->html;
          }
          elseif($this->contentType == 3 || $this->contentType == 4) {

              $this->sendMultipart();
              return;
          }
          else {

              throw new MailerException('Invalid contentType: ' . $this->contentType, 101);
          }

          if(!mail($this->to, $this->subject, $message, $headers))
             throw new MailerException('Error sending email', 101);
      }

      /**
       * Sends a multipart email with 0 or more attachments.
       * 
       * @param String $contentType The content-type to send the email as defined in RFC 2046.
       * @param String $charset The character set used in the email composition
       * @return void
       * @throws FrameworkException if there was an error sending the email
       */
      public function sendMultipart($contentType = 'multipart-mixed', $charset = 'utf-8') {

             $boundary = md5(date('r', time()));

             $subject = ($contentType == 'utf-8') ? '=?UTF-8?B?' . base64_encode($this->subject) . "?=\r\n" : $this->subject;
             $message = "This is a multipart message in MIME format.\r\n\r\n";

             $headers = $this->getCommonHeaders();
             $headers .= "MIME-Version: 1.0\r\n";
             $headers .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n";

             // Add text part if present
             if($this->text)
                $message .= "--{$boundary}\r\n" .
                    		 "Content-Type: text/plain; charset=\"{$charset}\"\r\n\r\n" .
                             $this->text . "\r\n\r\n";

             // Add HTML part if present
             if($this->html) {

                // Append tracking code if a listener has been defined
                if($this->listener) {

                   $this->html .= '<img border="0" src="' . $this->listener->getTrackingUrl() .
                           '/onOpen/' . $this->listener->getTrackingId() . '" width="1" height="1">';
                }

                $message .= "--{$boundary}\r\n" .
                     		"Content-Type: text/html; charset=\"{$charset}\"\r\n\r\n" .
                            $this->html . "\r\n\r\n";
             }

             // Add attachments
             foreach($this->attachments as $attachment) {

                 $relativeFilePath = $attachment->getFile();
                 $pieces = explode(DIRECTORY_SEPARATOR, $relativeFilePath);
                 $filename = array_pop($pieces);
                 $name = $attachment->getName() ? $attachment->getName() : $filename; 
                 $path = AgilePHP::getWebRoot() . DIRECTORY_SEPARATOR . $relativeFilePath;

                 if(!file_exists($path))
                    throw new MailerException('The specified attachment does not exist at \'' . $path . '\'.', 102);

                 $bytes = chunk_split(base64_encode(file_get_contents($path)));

                 if(strtolower($charset) == 'utf-8') {

                     $message .= "--{$boundary}\r\n" .
                          "Content-Type: " . $attachment->getContentType() . ";\r\n" .
                          " name=\"=?UTF-8?B?" . base64_encode($name) . "?=\"\r\n" .
                          "Content-Disposition: attachment;\r\n" .
                          "filename=\"=?UTF-8?B?" . base64_encode($name) . "?=\"\r\n" .
                          "Content-Transfer-Encoding: base64\r\n\r\n" . $bytes . "\r\n\r\n";
                 }
                 else {

                     $message .= "--{$boundary}\n" .
                          "Content-Type: " . $attachment->getContentType() . "; name=\"{$name}\"\r\n" .
                          "Content-Disposition: attachment;\r\n" .
                          "filename=\"{$name}\"\r\n" .
                          "Content-Transfer-Encoding: base64\r\n\r\n" . $bytes . "\r\n";
                 }
             }

             $message .= "--{$boundary}--\r\n";

             if(!mail($this->to, $subject, $message, $headers))
                throw new MailerException('Error sending email', 103);
      }

	  /**
       * Initializes common SMTP headers.
       * 
       * @return void
       */
      private function getCommonHeaders() {

             $headers = 'From: ' . $this->fromName . ' <' . $this->from . '>' . "\r\n";
             $headers .= 'Reply-To: ' . $this->from . "\r\n";
             $headers .= 'Return-Path: ' . $this->from . "\r\n";
             $headers .= 'X-mailer: AgilePHP Framework on PHP (' . phpversion() . ')' . "\r\n";

             if($this->cc) {

                if(strpos($this->cc, ',') !== false) {

                    $recipients = explode(',', $this->cc);
                    foreach($recipients as $recipient)
                        $headers .= "CC: {$recipient}\r\n";
                }
                else
                    $headers .= "CC: {$this->cc}\r\n";
             }

             if($this->bcc) {

                $recipients = explode(',', $this->bcc);
                foreach($recipients as $recipient)
                   $headers .= "BCC: {$recipient}\r\n";
             }

             return $headers;
      }
}
?>