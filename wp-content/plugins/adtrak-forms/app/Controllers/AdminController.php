<?php namespace Adtrak\Forms\Controllers;

use Adtrak\Forms\View;
use Adtrak\Forms\Controllers\Admin\FormController;
use Adtrak\Forms\Controllers\Admin\OptionsController;
use Adtrak\Forms\Controllers\Admin\SubmissionController;
use Adtrak\Forms\Models\Form;

class AdminController
{
    protected $form;
    protected $options;
    protected $submission;

    /**
     *  AdminController constructor
     */
    public function __construct()
    {
        $this->form = new FormController();
        $this->options = new OptionsController();
        $this->submission = new SubmissionController();
    }

    /**
     * Add Scripts
     *
     * @return void
     */
    public function addScripts()
    {
        wp_enqueue_script("jquery-ui-sortable");
    }

    /**
     * Setup menu item
     *
     * @return void
     */
    public function menu()
    {
        add_menu_page(
            'Forms',
            'Forms',
            'manage_options',
            'adtrak-forms',
            [$this, 'displayFormList'],
            'dashicons-email-alt'
        );

        $this->form->menu();
        $this->submission->menu();
        $this->options->menu();
    }

    /**
     * Return list of forms and display view
     *
     * @return void
     */
    public function displayFormList()
    {
        $forms = Form::all();
        View::render('admin/dashboard.twig', [
            'forms' => $forms,
            'notice' => $this->getNotice($_GET)
        ]);
    }

    /**
     * @param  string $type
     * @param  string $message
     *
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
     * @param  array $data
     *
     * @return array $notice
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
}
