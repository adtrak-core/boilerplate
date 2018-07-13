<?php

namespace Adtrak\Forms\Controllers;

use Adtrak\Forms\View;
use Adtrak\Forms\Models\Form;
use Adtrak\Forms\Models\Submission;
use Billy\Framework\Models\Option;
use DrewM\MailChimp\MailChimp;

use Adtrak\Forms\Controllers\MailController;

class FrontController
{

    /**
     * FrontController constructor
     */
    public function __construct()
    {
    }

    /**
     * Add scripts
     *
     * @return void
     */
    public function addScripts()
    {
        wp_enqueue_script("jquery-ui-datepicker");
    }

    /**
     * Add the shortcode
     *
     * @return void
     */
    public function addShortcode()
    {
        add_shortcode('adtrak_forms', array( $this, 'displayForm' ), 10);
    }

    /**
     * Get the fields and display the form based on ID given within shortcode
     *
     * @param array $atts
     * @return void
     */
    public function displayForm($atts)
    {
        $error = null;
        @$id = $atts['id'];
        @$form = Form::find($id);

        if (!$id) {
            $error = "No ID supplied.";
        }
        if (!$form && !$error) {
            $error = "Could not find form, check your ID and try again.";
        }
        // if(!$fields) $error = "No Fields found, check you've setup your form and try again.";

        if ($error) {
            return View::render('front/error.twig', [
                'error' => $error
            ], false);
        }

        $fields = $form->fields()->orderBy('sort', 'ASC')->getResults();

        # Replace hypens with underscore within field_data
        $processedData = [];
        foreach ($fields as $field) {
            $nfield = unserialize($field->field_data);
            $d = [];
            foreach ($nfield as $key => $nf) {
                $nkey = str_replace("-", "_", $key);
                $d[$nkey] = $nf;
            }
            $processedData[] = $d;
        }

        $mc = unserialize($form->mailchimp);
        $chimp = [];

        if($mc['mailchimp_key']) {
            $mailchimp = new MailChimp($mc['mailchimp_key']);
                
            # Post email address to Mailchimp and subscribe the user to the list
            $result = $mailchimp->get("lists/". $mc['mailchimp_id'] ."/interest-categories");
            
            if(!empty($result)) {
                foreach($result['categories'] as $interest) {
                    $fetch = $mailchimp->get("lists/". $mc['mailchimp_id'] ."/interest-categories//" . $interest['id'].'/interests');
                    $chimp = ['id' => $interest['id'] ,'title' => $interest['title'], 'items' => []];
                    foreach($fetch['interests'] as $int) {
                        $chimp['items'][] = ['id' => $int['id'], 'name' => $int['name']];
                    }
                    // $chimp[] = $build;
                }
            } else {
                $chimp['error'] = 'failed';
            }
        }

        $soi = [];

        if(isset($mc['soi_check']))
            $soi['label'] = $mc['soi_label'];

        return View::render('front/show-form.twig', [
            'form_data' => $form,
            'field_data' => $processedData,
            'chimp' => $chimp,
            'soi' => $soi
        ], false);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function setBaseUrl()
    {
        wp_localize_script('forms-front-js', 'CFajax', ['ajaxurl' => admin_url('admin-ajax.php')]);
    }
    
    /**
     * Send emails based on POST'd data from the frontend form
     * @return void
     */
    public function sendMail()
    {
        // $permission = check_ajax_referer('form_send_nonce', 'nonce', false);
        // if ($permission === false) {
        // 	echo 'Permission Denied';
        // } else {
        // 	echo 'Works';
        // }

        $name = $_REQUEST['id'];

        # Unset the elements which are no longer needed
        unset($_POST['action']);
        unset($_POST['id']);
        
        $chimp = $_POST['sub-type'];
        // unset($_POST['sub-type']);

        # Find the form on the given form name
        $form = Form::where('name', '=', $name)->first();
        $fields = $form->fields()->orderBy('sort', 'ASC')->getResults();
        
        $data = [];
        foreach ($_POST as $key => $field) {
            if($key == 'sub-type') {
                $data[$key] = $field;
            } else {
                $data[$key] = stripslashes($field);
            }
        }
        $customerCopy = unserialize($form->emails);
        $customerCopy = $customerCopy['customer_copy'];

        $mail = new MailController($form, $_FILES);
        $mail->sendClientEmail($data);

        # Check if a customer copy is needed
        if (isset($customerCopy) && $customerCopy != 0) {
            $mail->sendCustomerEmail($data);
        }

        # Check if we need to subscribe the user
        if (isset($chimp)) {
            $mail->subscribeToList($fields, $data);
        }

        $submission = new Submission();

        $submission->form_id = $form->form_id;
        $submission->data = serialize($data);
        $submission->ip = $_SERVER['REMOTE_ADDR'];

        $submission->save();
        $analytics = filter_var(get_option('apcf_old_analytics'), FILTER_VALIDATE_BOOLEAN);

        if ($mail->error) {
            echo '{"type": "error", "message": "'. $mail->error_message .'"}';
        } else {
            echo '{"type": "success", "message": "'. $form->success_message .'", "old_analytics": "'. $analytics .'"}';
        }

        exit();
    }
}
