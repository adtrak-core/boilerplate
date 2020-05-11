<?php


namespace PGMB\API;


use Exception;


class GoogleAPIError extends Exception {

	protected $googlemessage = '';

	public function __construct( $error ) {
		$this->googlemessage = (string)$error->error->message;
		$this->parse_google_error($error);
		parent::__construct( $this->googlemessage );
	}

	protected function parse_google_error($error){
		if(!isset($error->error->details)){ return; }

		foreach($error->error->details as $detail){
			if(!isset($detail->errorDetails) || !is_array($detail->errorDetails)) {
				continue;
			}

			foreach($detail->errorDetails as $errorDetail){
				$this->googlemessage .= "\n['.(int)$errorDetail->code.']" .
				                        (string)$errorDetail->message.
				                        ' Field: '.(string)$errorDetail->field.
										' Value: '.(string)$errorDetail->value;
			}
		}

	}
}
