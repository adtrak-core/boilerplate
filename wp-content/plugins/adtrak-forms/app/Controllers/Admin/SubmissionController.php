<?php namespace Adtrak\Forms\Controllers\Admin;

use Adtrak\Forms\View;
use Adtrak\Forms\Models\Form;
use Adtrak\Forms\Models\Submission;

class SubmissionController
{
    protected $id;

    /**
     * OptionsController constructor
     */
    public function __construct()
    {
        $this->id = $_GET['id'];
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function menu()
    {
        add_submenu_page(
            'adtrak-forms',
            'Submissions',
            'Submissions',
            'publish_posts',
            'adtrak-forms-submissions',
            [$this, 'showSubmissionList']
        );
        add_submenu_page(
            'adtrak-forms',
            'View Submissions',
            'View Submissions',
            'publish_posts',
            'adtrak-forms-viewsubmissions',
            [$this, 'showSubmissions']
        );
    }

    /**
     * Find all forms and submissions and display a list
     *
     * @return void
     */
    public function showSubmissionList()
    {
        $forms = Form::all();
        $submission = Submission::all();
        View::render('admin/submissions.twig', [
            'forms' => $forms,
            'submissions' => $submission
        ]);
    }

    /**
     * Find all submissions for a particular form and display as a list
     *
     * @return void
     */
    public function showSubmissions()
    {
        # Check if an ID has been passed, redirect if not
        if (!isset($this->id)) {
            echo '<META HTTP-EQUIV="refresh" content="0;URL=admin.php?page=adtrak-forms-submissions">';
            echo '<script>window.location.href=admin.php?page=adtrak-forms-submissions;</script>';
            die();
        }

        $submissions = Submission::where("form_id", "=", $this->id)->get();

        $form = Form::findOrFail($this->id);

        # Create an array and add the unserialized data to it
        $s = array();
        foreach ($submissions as $data) {
            $a = array(
                'submission_id' => $data->submission_id,
                'form_id' => $data->form_id,
                'data' => unserialize($data->data),
                'ip' => $data->ip,
                'created_at' => $data->created_at
            );
            $s[] = $a;
        }

        View::render('admin/viewsubmissions.twig', [
            'submission_data' => $s,
            'form_name' => $form->name
        ]);
    }
}
