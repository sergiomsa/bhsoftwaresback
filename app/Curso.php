<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    protected $table 	= 'cursos';
    protected $fillable = ['titulo','descricao'];
	
	public function matriculas()
    {
   	    return $this->belongsToMany('App\Aluno', 'matriculas');
    }
}
