<?php

namespace Adtrak\Forms\Controllers;

use Adtrak\Forms\View;
use Adtrak\Forms\Helper;
use Adtrak\Forms\Models\Form;
use Billy\Framework\Models\Option;
use PHPMailer;
use DrewM\MailChimp\MailChimp;

class MailController
{
    protected $referenceNo;
    protected $mail;
    protected $files;
    protected $mailchimp;
    protected $form;

    public $error;
    public $error_message;

    public $fromOver = false;

    /**
     * MailController constructor
     */
    public function __construct($form, $files)
    {
        $this->files = $files;
        $this->error = false;

        $this->form = $form;

        $smtp = Option::where('option_name', '=', 'apcf_options')->get();
        $smtp = unserialize($smtp[0]->option_value);

        $workedData = [];
        foreach ($smtp as $key => $option) {
            $nkey = str_replace("-", "_", $key);
            $workedData[$nkey] = $option;
        }

        $smtp = $workedData;

        $this->noreply = (isset($smtp['smtp_noreply'])) ? filter_var($smtp['smtp_noreply'], FILTER_VALIDATE_BOOLEAN) : false;

        $this->mail = new PHPMailer;
        $this->mail->isSMTP();
        $this->mail->Host = $smtp['smtp_host'];
        $this->mail->Port = $smtp['smtp_port'];
        $this->mail->SMTPAuth = true;
        $this->mail->Username = $smtp['smtp_username'];
        $this->mail->Password = $smtp['smtp_password'];
        if (isset($smtp['smtp_password'])) {
            $this->mail->SMTPSecure = $smtp['smtp_security'];
        } else {
            $this->mail->SMTPSecure = 'tls';
        }

        date_default_timezone_set('Europe/London');
        $date = new \DateTime();
        $this->referenceNo = $date->format('si / hd - my');
    }


    /**
     * Undocumented function
     *
     * @param array $data
     * @return void
     */
    public function sendClientEmail($data)
    {
        # Clear recipients as Mail is potentially used twice
        $this->mail->ClearAllRecipients();
        $this->mail->ClearReplyTos();

        unset($data['sub-type']);
        unset($data['soi']);

        $emailSubject = get_option("blogname"). ' Website - ' . ucfirst($this->form->name) . ' Enquiry';
        $this->mail->Subject = $emailSubject;

        $emails = unserialize($this->form->emails);

        # Check if emailTo exists and split into a comma seperated array then add to the Mail
        if (!empty($emails['emailTo'])) {
            $emailTo = explode(",", $emails['emailTo']);
            foreach ($emailTo as $email) {
                $this->mail->AddAddress($email, $email);
            }
        }
        # Check if emailCC exists and split into a comma seperated array then add to the Mail
        if (!empty($emails['emailCC'])) {
            $emailTo = explode(",", $emails['emailCC']);
            foreach ($emailTo as $email) {
                $this->mail->AddCC($email, $email);
            }
        }
        # Check if emailBCC exists and split into a comma seperated array then add to the Mail
        if (!empty($emails['emailBCC'])) {
            $emailTo = explode(",", $emails['emailBCC']);
            foreach ($emailTo as $email) {
                $this->mail->AddBCC($email, $email);
            }
        }

        foreach ($data as $fields) {
            if (filter_var($fields, FILTER_VALIDATE_EMAIL)) {
                $this->mail->FromName = $fields;
                $this->mail->AddReplyTo($fields, $fields);
                $fromOver = true;
                break;
            }
        }

        if($this->noreply) {
            $this->mail->From = $this->mail->Username;
        } else {
            $this->mail->From = 'noreply@adtrakforms.co.uk';
        }

        $this->mail->FromName = get_option("blogname");

        $build = [];

        $build['site_name'] = get_option("blogname");
        $build['subject'] = $emailSubject;
        $build['emailFrom'] = $data['acpf-email'];
        $build['referenceNo'] = $this->referenceNo;

        $n = [];
        foreach ($data as $key => $value) {
            $nkey = str_replace("acpf-", "", $key);
            $n[$nkey] = $value;
        }

        $build['rows'] = $n;

        $data = json_decode(json_encode($build), false);

        # Create the email

        ob_start();
        $template = $this->templateLocater('emailClient.php');
        include_once $template;
        $msg = mb_convert_encoding(ob_get_contents(), 'Windows-1252', 'UTF-8');
        ob_end_clean();

        $this->mail->MsgHTML($msg);

        # Check if there are attachments then loop through and add to the email
        if ($this->files) {
            for ($i=0; $i < count($this->files["upload"]); $i++) {
                foreach (array_keys($this->files['upload']['name']) as $key) {
                    $source = $this->files['upload']['tmp_name'][$key];
                    $filename = $this->files['upload']['name'][$key];
                    $filesize = $this->files['upload']['size'][$key];
                    $filetype = $this->files['upload']['type'][$key];

                    $this->mail->AddAttachment($source, $filename);
                }
            }
        }

        if (!$this->mail->Send()) {
            $this->error = true;
            $this->error_message = $this->mail->ErrorInfo;
        }
    }

    /**
     * Undocumented function
     *
     * @param array $data
     * @return void
     */
    public function sendCustomerEmail($data)
    {
        # Clear recipients as Mail is used twice
        $this->mail->ClearAllRecipients();
        $this->mail->ClearReplyTos();

        unset($data['sub-type']);
        unset($data['soi']);

        $emailSubject = get_option("blogname") . ' | Thanks for getting in touch!';
        $this->mail->Subject = $emailSubject;
        foreach ($data as $fields) {
            if (filter_var($fields, FILTER_VALIDATE_EMAIL)) {
                $this->mail->AddAddress($fields, $fields);
                break;
            }
        }
        $this->mail->FromName = get_option("blogname");

        $emails = unserialize($this->form->emails);
        $emailTo = explode(",", $emails['emailTo']);
        $this->mail->AddReplyTo($emailTo[0], $emailTo[0]);

        if($this->noreply) {
            $this->mail->From = $this->mail->Username;
        } else {
            $this->mail->From = 'noreply@adtrakforms.co.uk';
        }

        # Create the email

        $build = [];

        $build['site_name'] = get_option("blogname");
        $build['subject'] = $emailSubject;
        $build['emailFrom'] = $data['acpf-email'];
        $build['referenceNo'] = $this->referenceNo;

        $n = [];
        foreach ($data as $key => $value) {
            $nkey = str_replace("acpf-", "", $key);
            $n[$nkey] = $value;
        }

        $build['rows'] = $n;

        $data = json_decode(json_encode($build), false);

        # Create the email

        ob_start();
        $template = $this->templateLocater('emailCustomer.php');
        include_once $template;
        $msg = mb_convert_encoding(ob_get_contents(), 'Windows-1252', 'UTF-8');
        ob_end_clean();

        $this->mail->MsgHTML($msg);

        # Check if there are attachments then loop through and add to the email
        if ($this->files) {
            //attachment information
            for ($i=0; $i < count($this->files["upload"]); $i++) {
                foreach (array_keys($this->files['upload']['name']) as $key) {
                    $source = $this->files['upload']['tmp_name'][$key];
                    $filename = $this->files['upload']['name'][$key];
                    $filesize = $this->files['upload']['size'][$key];
                    $filetype = $this->files['upload']['type'][$key];

                    $this->mail->AddAttachment($source, $filename);
                }
            }
        }

        if (!$this->mail->Send()) {
            $this->error = true;
            $this->error_message = $this->mail->ErrorInfo;
        }
    }

    public function subscribeToList($fields, $data)
    {
        $mc = unserialize($this->form->mailchimp);
                    
        $mailchimp = new MailChimp($mc['mailchimp_key']);
            
        # Post email address to Mailchimp and subscribe the user to the list
        $result = $mailchimp->post("lists/". $mc['mailchimp_id'] ."/members", [
            'email_address' => $data['acpf-email'],
            'status'        => 'pending'
        ]);

        # Get mailchimps hash for that user
        $sub_hash = $mailchimp->subscriberHash($data['acpf-email']);

        # Get Mailchimp fields and put into an array
        $nf = array();
        foreach ($fields as $new) {
            $d = unserialize($new['field_data']);
            if (isset($d['mailchimp'])) {
                $nf[$d['name']] = $d['mailchimp'];
            }
        }

        $mcp = [];
        foreach ($data as $key => $field) {
            $name = str_replace("acpf-", "", $key);
            $nn = $nf[$name];
            $mcp[$nn] = $field;
        }

        # Post merge fields for user to Mailchimp
        $result = $mailchimp->patch("lists/". $mc['mailchimp_id'] ."/members/$sub_hash", [
            'merge_fields' => $mcp
        ]);

        $interests = [];
        foreach($data['sub-type'] as $type) {
            $interests[$type] = true;
        }

        $result = $mailchimp->patch("lists/". $mc['mailchimp_id'] ."/members/$sub_hash", [
            'interests' => $interests
        ]);
    }

    /**
     * Undocumented function
     *
     * @param string $filename
     * @return string
     */
    protected function templateLocater($filename)
    {
        if ($overwrite = locate_template('adtrak-forms/' . $filename)) {
            $template = $overwrite;
        } else {
            $template = Helper::get('templates') . $filename;
        }
        return $template;
    }
}
