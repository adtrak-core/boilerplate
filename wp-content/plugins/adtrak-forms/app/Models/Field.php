<?php

namespace Adtrak\Forms\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Field extends Eloquent
{
	
	protected $table = 'apcf_fields';
	protected $primaryKey = 'field_id';
	public $timestamps = false;

	protected $fillable = [
		'name',
		'type',
		'sort',
		'classes'
	];

}