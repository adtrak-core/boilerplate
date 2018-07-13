<?php namespace Adtrak\Forms\Controllers\Admin;

use Adtrak\Forms\View;
use Adtrak\Forms\Models\Form;
use Adtrak\Forms\Models\Field;

class FormController
{
    protected $id;
    protected $notice;

    /**
     * FormController constructor
     */
    public function __construct()
    {
        $this->id = $_GET['id'];
    }

    /**
     * Setup menu items
     *
     * @return void
     */
    public function menu()
    {
        add_submenu_page(
            'adtrak-forms',
            'Manage Form',
            'New Form',
            'manage_options',
            'adtrak-forms-newform',
            [$this, 'showNewForm']
        );
        add_submenu_page(
            'adtrak-forms',
            'Manage Form',
            'Edit Form',
            'manage_options',
            'adtrak-forms-editform',
            [$this, 'showEditForm']
        );
        add_submenu_page(
            'adtrak-forms',
            'Manage Form',
            'Duplicate Form',
            'manage_options',
            'adtrak-forms-duplicateform',
            [$this, 'duplicateForm']
        );
        add_submenu_page(
            'adtrak-forms',
            'Manage Form',
            'Delete Form',
            'manage_options',
            'adtrak-forms-deleteform',
            [$this, 'deleteForm']
        );
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function showNewForm()
    {
        # Check if data has been posted to the Controller
        if (!empty($_POST)) {
            # Create a new form Object, add a name and save
            $form = new Form();
            $form->name = stripslashes($_REQUEST['form-name']);
            $form->submit_name = stripslashes($_REQUEST['submit_name']);
            $form->success_message = stripslashes($_REQUEST['success_message']);
            // dd($_POST);
            $form->save();

            # Get the field data from the POST
            $data = $_REQUEST['fields'];

            # Begin iterating
            $i = 0;

            # Go through each field and create/setup with a Field Object
            foreach ($data as $field) {
                $f = new Field();

                $form->fields()->save($f);

                $f = $this->createField($i, $f, $field);

                $form->fields()->save($f);

                $i++;
            }

            $form->emails = $this->createEmails($_POST);
            $form->mailchimp = $this->createMailchimp($_POST);

            try {
                $form->save();
                $this->returnNotice('success', 'Form Saved');
            } catch (Exception $e) {
                $this->returnNotice('error', 'Could not save form');
                Sentry::captureException($e);
            }

            $this->killAndReturn();
        }

        View::render('admin/form.twig', []);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function showEditForm()
    {
        # Check if an ID has been passed through
        if (!isset($this->id)) {
            $this->killAndReturn();
        }

        # Find the form based on the passed through ID
        $form = Form::findOrFail($this->id);

        # Check if data has been posted to the Controller
        if (!empty($_POST)) {
            # Send form name to Form object
            $form->name = stripslashes($_REQUEST['form-name']);
            $form->submit_name = stripslashes($_REQUEST['submit_name']);
            $form->success_message = stripslashes($_REQUEST['success_message']);
            $form->updated_at = date("Y-m-d H:i:s");

            # Get the field data from the POST
            $data = $_REQUEST['fields'];

            # Get fields based on Form object created
            $allFields = $form->fields()->get();

            # Create arrays for checking which fields have been delete
            $allID = array();
            $existID = array();

            # Loop through all fields and add ID to allID
            foreach ($allFields as $field) {
                array_push($allID, $field['attributes']['field_id']);
            }

            # Begin iterating
            $i = 0;

            # Go through each field and create/edit with a Field Object
            foreach ($data as $field) {
                $f = $form->fields()->firstOrNew(['field_id' => $field['id']]);

                # Loop through all POST'd fields and add to existID
                array_push($existID, $field['id']);

                try {
                    $form->fields()->save($f);
                } catch (Exception $e) {
                    Sentry::captureException($e);
                }

                $f = $this->createField($i, $f, $field);

                try {
                    $form->fields()->save($f);
                } catch (Exception $e) {
                    Sentry::captureException($e);
                }

                $i++;
            }

            # Compare ID's and remove duplicates
            $allID = array_diff($allID, $existID);

            # Delete ID's left in arrays
            foreach ($allID as $id) {
                $form->fields()->findOrFail($id)->delete();
            }

            $form->emails = $this->createEmails($_POST);
            $form->mailchimp = $this->createMailchimp($_POST);

            try {
                $form->save();
                $this->returnNotice('success', 'Form Saved');
            } catch (Exception $e) {
                $this->returnNotice('error', 'Could not save form');
                Sentry::captureException($e);
            }
        }

        # Get fields for the given form
        $fields = $form->fields()->orderBy('sort', 'ASC')->getResults();

        # Replace hypens with underscore within field_data
        $processedData = [];

        foreach ($fields as $field) {
            $nfield = unserialize($field->field_data);
            $d = [];
            foreach ($nfield as $key => $nf) {
                $nkey = str_replace("-", "_", $key);
                if ($key != "validation-options" && $nf != "None") {
                    if (!is_array($nf)) {
                        $d[$nkey] = stripslashes($nf);
                    } else {
                        $d[$nkey] = $nf;
                    }
                } else {
                    $d[$nkey] = $nf;
                }
            }
            $processedData[] = $d;
        }

        View::render('admin/edit-form.twig', [
            'form_data' => $form,
            'field_data' => $processedData,
            'emails' => unserialize($form->emails),
            'mailchimp' => unserialize($form->mailchimp),
            'notice' => $this->notice
        ]);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function duplicateForm()
    {
        # Check if ID has been passed to page
        if (isset($_GET['id'])) {
            $id = $this->id;

            # Find form to duplicate
            $form = Form::find($id);

            $form->load('fields');

            # Clone that form and alter name
            $dupeform = $form->replicate();
            $dupeform->name = $dupeform->name . " COPY";

            # Save duplicate
            try {
                $dupeform->save();
            } catch (Exception $e) {
                Sentry::captureException($e);
            }

            foreach ($form->getRelations() as $relation => $fields) {
                foreach ($fields as $field) {
                    unset($field->field_id);
                    $f = new Field();
                    $f->form_id = $dupeform->form_id;
                    $f->sort = $field->sort;
                    $f->field_data = $field->field_data;
                    $f->save();
                }
            }

            # Save duplicate
            try {
                $dupeform->push();
                $this->returnNotice('success', 'Form Duplicated');
            } catch (Exception $e) {
                $this->returnNotice('error', 'Could not duplicate form');
                Sentry::captureException($e);
            }
        }

        $this->killAndReturn();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function deleteForm()
    {
        # Check if ID has been passed to page
        if (isset($_GET['id'])) {
            $id = $this->id;
            
            # Find form
            $form = Form::findOrFail($this->id);

            # Delete form
            $form->delete();
            $this->returnNotice('success', 'Form Deleted');
        }

        $this->killAndReturn();
    }

    /**
     * Create a notice array and store in the Controllers notice variable
     *
     * @param string $type
     * @param string $message
     * @return void
     */
    public function returnNotice($type, $message)
    {
        $notice = [];
        $notice['type'] = $type;
        $notice['message'] = $message;
        $this->notice = $notice;
    }

    /**
     * Get notice from data supplied and return as an array
     *
     *
     * @param array $data
     * @return array
     */
    public function getNotice($data)
    {
        $notice = [];

        if (isset($data['notice-type'])) {
            $notice['type'] = $data['notice-type'];
        }
        if (isset($data['notice-type'])) {
            $notice['message'] = $data['notice-message'];
        }

        return $notice;
    }

    /**
     * Create array based on given data and return as array
     *
     * @param  array $data
     * @return string $emails
     */
    public function createEmails($data)
    {
        if (!isset($data['customerCopy'])) {
            $data['customerCopy'] = 0;
        }
        $emails = array(
                'emailTo' => stripslashes($data['emailTo']),
                'emailCC' => stripslashes($data['emailCC']),
                'emailBCC' => stripslashes($data['emailBCC']),
                'customer_copy' => stripslashes($data['customerCopy'])
            );
        return serialize($emails);
    }

    /**
     * Create array based on given data and return as array
     *
     * @param  array $data mailchimp data
     * @return string $mc
     */
    public function createMailchimp($data)
    {
        $mc = array(
                'mailchimp_id' => stripslashes($data['mcID']),
                'mailchimp_key' => stripslashes($data['mcKey']),
                'mailchimp_label' => stripslashes($data['mcLabel']),
                'soi_check' => $data['soiCheck'],
                'soi_label' => stripslashes($data['soiLabel'])
            );
        return serialize($mc);
    }

    /**
     * Edit field object based on given data and return
     *
     * @param int $id
     * @param object $fieldObject
     * @param array $data
     * @return Array $fieldObject
     */
    public function createField($id, $fieldObject, $data)
    {
        $fieldObject->sort = $id;
        $data['id'] = $fieldObject->field_id;
        $fieldObject->field_data = serialize($data);
        return $fieldObject;
    }

    /**
     * Redirect the page to the dashboard
     *
     * @return void
     */
    public function killAndReturn()
    {
        echo '<META HTTP-EQUIV="refresh" content="0;URL=admin.php?page=adtrak-forms&notice-type='.$this->notice['type'].'&notice-message='.$this->notice['message'].'">';
        echo '<script>window.location.href=admin.php?page=adtrak-forms&notice-type='.$this->notice['type'].'&notice-message='.$this->notice['message'].'</script>';
        die();
    }
}
