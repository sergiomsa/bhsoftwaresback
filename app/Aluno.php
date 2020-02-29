<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Aluno extends Model
{
    protected $table 	= 'alunos';
	protected $fillable = ['nome','email','datadenascimento'];
	protected $appends 	= ['idade'];
	
	public function matriculas()
    {
   	    return $this->belongsToMany('App\Curso', 'matriculas');
    }
	
	public function getIdadeAttribute()
	{
		if ((isset($this->attributes['datadenascimento'])) and ($this->attributes['datadenascimento'] !=""))
		{
			return Carbon::parse($this->attributes['datadenascimento'])->age;
		}
	}
}
