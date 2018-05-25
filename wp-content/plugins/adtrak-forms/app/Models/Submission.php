<?php

namespace Adtrak\Forms\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Submission extends Eloquent
{

	protected $table = 'apcf_submissions';
	protected $primaryKey = 'submission_id';

	protected $fillable = [
		'form_id',
		'data',
		'ip'
	];

}