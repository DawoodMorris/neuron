<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Endpoint: MailService version 2
 * Contract: Sends emails...
 * Required Data: What this endpoint needs to complete the required actions
 **/

//load deps
require NEURON_ROOT.'lib/External/PHPMailer/Exception.php';
require NEURON_ROOT.'lib/External/PHPMailer/PHPMailer.php';
require NEURON_ROOT.'lib/External/PHPMailer/SMTP.php';

class MailService {
	//data for this endpoint to work with
	private object $data;

	//results of the the requested action on this endpoint
	private array $results = [];

	private object $phpMailer;

	private bool $mailReady;

	//headers
	private string $headers;

	function __construct(object $data) {
		$this->data = $data;
		$this->results['status'] = false;
		$this->phpMailer = new PHPMailer(true);
		$this->mailReady = $this->init();
	}

	/**
	 * Initialize settings
	 **/
	private function init(): bool {
		try {
			$this->phpMailer->SMTPDebug = SMTP::DEBUG_OFF;
		    $this->phpMailer->isSMTP();
		    $this->phpMailer->Host = getenv('MAIL_HOST');
		    $this->phpMailer->SMTPAuth = true;
		    $this->phpMailer->Username = getenv('SYS_MAIL_USER');
		    $this->phpMailer->Password = getenv('SYS_MAIL_PWD');
		    $this->phpMailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
		    $this->phpMailer->Port = 465;
		    return true;
		} catch (Exception $e) {
			$this->results['errorMessage'] = $e->getMessage();
			return false;
		}
	}

	/**
	 * Process the action
	 **/
	public function process(): array {
		$action = method_exists(get_class($this), ($this->data->action??'noAction')) ? $this->data->action : 'inValidAction';
		return $this->$action();
	}

	/**
	 * Respond to an invalid given action
	 **/
	private function inValidAction(): array {
		return [
			'status' => false,
			'error' => 'inValidAction',
			'errorMessage' => MESSAGES['inValidAction']
		];
	}

	/**
	 * We have an email template, we use it to compose a Topuphome email
	 * @param $content (array) Associative array containing the contents keys as targetPlaceHolders 
	 * and values as the content. Basically we interpolate the [emailContent] and [disclaimerContent]
	 * in the email template.
	 * Thus the $contents values of the keys should already be composed
	 * @return $templateEmail (string) The composed final email
	 **/
	function interpolateEmailContent(array $contents): string {
		$templateEmail = '';
		$emailTemplatePath = NEURON_ROOT.'/lib/html/email.template.html';
		$templateEmail = @file_get_contents($emailTemplatePath);
		foreach($contents as $key => $value) {
			$templateEmail = str_replace($key, $value, $templateEmail);
		}
		return $templateEmail;
	}

	/**
	 * We have an email template, we use it to compose a Topuphome email
	 * @param $content (array) Associative array containing the contents keys as targetPlaceHolders 
	 * and values as the content. Basically we interpolate the [emailContent] and [disclaimerContent]
	 * in the email template.
	 * Thus the $contents values of the keys should already be composed
	 * @return $templateEmail (string) The composed final email
	 **/
	function interpolateEmailCustomizedContent(array $contents): string {
		$templateEmail = '';
		$emailTemplatePath = NEURON_ROOT.'/lib/html/receipt.email.template.html';
		$templateEmail = @file_get_contents($emailTemplatePath);
		foreach($contents as $key => $value) {
			$templateEmail = str_replace($key, $value, $templateEmail);
		}
		return $templateEmail;
	}

	/************************************************
	 * Action implementations start here...
	 ************************************************/

	/***
	 * Send an email, the normal email
	 * */
	private function sendMail(): array {
		if($this->mailReady) {
			$mailContent = [
				'[emailContent]' => $this->data->emailContent,
				'[disclaimerContent]' => $this->data->disclaimerContent
			];
			try {
				$recipientName = "{$this->data->recipientFirstname} {$this->data->recipientLastname}";
				$this->phpMailer->setFrom($this->data->fromAddress, $this->data->fromName);
			    $this->phpMailer->addAddress($this->data->recipientAddress, $recipientName);
			    if($this->data->repliable) {
			    	$this->phpMailer->addReplyTo($this->data->replyToAddress, $this->data->replyToName);
			    }
			    if(isset($this->data->cc) && $this->data->cc) {
			    	if(strrpos($this->data->ccEmail, '<')) {
			    		$ccMetaData = explode('<', $this->data->ccEmail);
			    		$name = $ccMetaData[0];
			    		$emailAddress = str_replace('>','',$ccMetaData[1]);
			    		$this->phpMailer->addCC($emailAddress,$name);
			    	} else {
			    		$this->phpMailer->addCC($this->data->ccEmail);
			    	}
				}
				if(isset($this->data->bcc) && $this->data->bcc) {
					if(strrpos($this->data->bccEmail, '<') !== false) {
			    		$ccMetaData = explode('<', $this->data->bccEmail);
			    		$name = $ccMetaData[0];
			    		$emailAddress = str_replace('>','',$ccMetaData[1]);
			    		$this->phpMailer->addCC($emailAddress,$name);
			    	} else {
			    		$this->phpMailer->addBCC($this->data->bccEmail);
			    	}
				}

				if(isset($this->data->attachments) && $this->data->attachments) {
					foreach($this->data->attachments as $attachment) {
					    $this->phpMailer->addAttachment($attachment);
					}
				}
				$this->phpMailer->isHTML(true);
			    $this->phpMailer->Subject = $this->data->subject;
			    $this->phpMailer->Body = $this->interpolateEmailContent($mailContent);
			    $this->phpMailer->send();
			    $this->results['status'] = true;
				$this->results['message'] = MESSAGES['emailSent'] ?? 'could not get message';
			} catch(Exception $e) {
				$this->results['errorMessage'] = $e->getMessage();
				$this->results['errorDetails'] = $this->phpMailer->ErrorInfo;
			}
		}
		return $this->results;
	}
}
?>