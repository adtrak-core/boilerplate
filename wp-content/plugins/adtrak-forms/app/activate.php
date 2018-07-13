<?php 

/** @var  \Billy\Framework\Enqueue $enqueue */

use \Illuminate\Database\Capsule\Manager as Capsule;
use Adtrak\Forms\Helper;

# Get the version
$version = get_option('apcf_version', false);

/**
 * If the version returns false, i.e. first install run the code to
 * this will check if tables exist.
 */
if ($version === false) {
    if (!Capsule::schema()->hasTable('apcf_forms')) {
        Capsule::schema()->create('apcf_forms', function ($table) {
            $table->increments('form_id');
            $table->string('name');
            $table->string('mailchimp')->nullable();
            $table->string('logo')->nullable();
            $table->text('emails')->nullable();
            $table->text('success_message')->nullable();
            $table->string('submit_name')->nullable();
            $table->timestamps();
        });
    }

    if (!Capsule::schema()->hasTable('apcf_fields')) {
        Capsule::schema()->create('apcf_fields', function ($table) {
            $table->increments('field_id');
            $table->integer('form_id')->unsigned();
            $table->foreign('form_id')->references('form_id')->on('apcf_forms')->onDelete('cascade');
            $table->integer('sort')->nullable();
            $table->text('field_data')->nullable();
        });
    }

    if (!Capsule::schema()->hasTable('apcf_submissions')) {
        Capsule::schema()->create('apcf_submissions', function ($table) {
            $table->increments('submission_id');
            $table->integer('form_id')->unsigned();
            $table->foreign('form_id')->references('form_id')->on('apcf_forms')->onDelete('cascade');
            $table->text('data')->nullable();
            $table->string('ip')->nullable();
            $table->timestamps();
        });
    }

    add_option('apcf_version', Helper::get('version'));
    add_option('apcf_old_analytics', false);

    # Get the version
    $version = get_option('apcf_version', false);
}

// Only runs if not first activate

if ($version < "1.1.1") {
    Capsule::schema()->table('apcf_forms', function ($table) {
        $table->string('mailchimp')->nullable()->change();
    });
}

if ($version < "1.1.2") {
    Capsule::schema()->table('apcf_forms', function ($table) {
        $table->text('success_message');
    });
}

if ($version < "1.2.1") {
    Capsule::schema()->table('apcf_forms', function ($table) {
        $table->string('logo')->nullable()->change();
    });
}

if ($version < "1.2.2") {
    Capsule::schema()->table('apcf_forms', function ($table) {
        $table->text('emails')->nullable()->change();
        $table->text('success_message')->nullable()->change();
        $table->string('submit_name')->nullable()->change();
    });

    Capsule::schema()->table('apcf_fields', function ($table) {
        $table->integer('sort')->nullable()->change();
        $table->text('field_data')->nullable()->change();
    });

    Capsule::schema()->table('apcf_submissions', function ($table) {
        $table->text('data')->nullable()->change();
        $table->string('ip')->nullable()->change();
    });
}

if ($version < "1.5.9") {
    add_option('apcf_old_analytics', true);
}


update_option('apcf_version', Helper::get('version'));
