<?php

namespace Adtrak\Forms\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Form extends Eloquent
{

	protected $table = 'apcf_forms';
	protected $primaryKey = 'form_id';
	// public $timestamps = true;

	protected $fillable = [
		'name',
		'emails',
		'mailchimp',
		'submit_name',
		'success_message'
	];

	/**
	* Get the submissions for this Form
	*/
	public function submissions() 
	{
		return $this->hasMany('Adtrak\Forms\Models\Submission', 'form_id');
	}

	/**
	* Get the fields for this Form
	*/
	public function fields() 
	{
		return $this->hasMany('Adtrak\Forms\Models\Field', 'form_id');
	}

	/**
	* Get a form by name
	*/
	public function findByName($name)
	{
	    return Form::where('name', $name)->get();
	}

}