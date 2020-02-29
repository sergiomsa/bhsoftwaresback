<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Carbon\Carbon;
use DB;

class AlunoController extends Controller
{
	//
	// retorna a lista de alunos paginada ordenada e filtrada de acordo 
	// com o campo (nome e email) selecionado
	// 
	
	public function index_alunos(Request $request)
    {
		
        $limite              = $request->input('limite', 10);
		$orderby             = $request->input('orderby', 'nome');
		$campo				 = $request->input('campo', 'nome');
		$direction           = $request->input('direction', 'asc');
		$conteudo            = $request->input('conteudo', '');
						
		$alunos          	 = \App\Aluno::with('matriculas')
										  ->select('id','nome','email','datadenascimento')
										  ->where(function($query) use ($campo,$conteudo)
										  {
											if ($conteudo !="") 
											{	
												$query->where($campo,'like',"$conteudo%");
											}
											return $query;
										  })
										  ->orderBY($orderby,$direction)
										  ->paginate($limite);
									 
		return \Response::json($alunos, 200);
        
    }
	
	public function view_aluno($id)
    {
	   $aluno				= \App\Aluno::select('id','nome','email','datadenascimento')
										  ->find($id);
	
	   if (!isset($aluno->id))
	   {
		  return \Response::json(array('id' => ["ID# $id do aluno não encontrado"]), 404);
	   }
    
	   // obter os cursos matriculados do aluno 
	
	   $aluno->curso_id 	= $aluno->matriculas()->pluck('curso_id')->all();
	    
       return \Response::json($aluno, 200);
        
    }
	
	public function store_aluno(Request $request)
    {
       //
	   // nome não deve existir e ter no máximo 80 caracteres
	   // email não deve existir ter o formato de email com no máximo 250 caracteres
	   // data de nascimento no formato de dia/mes/ano
	   // deve ser matriculado no minimo em um curso.
       $valid = validator(
                $request->only('nome','email','datadenascimento','curso_id'),
                [
                    'nome'                  => 'required|unique:alunos,nome|max:80',
					'email'                 => 'required|email|unique:alunos,email|max:250',
					'datadenascimento'		=> 'date_format:d/m/Y',
					'curso_id.*'			=> 'required|numeric|distinct|min:1',
                ]
         );
		 
         if ($valid->fails()) {
            return \Response::json($valid->messages(), 400);
         }
		
		//
		// Valida de curso(s) informado está cadastrado
		//
			
		 $selected_cursos 			= $request->get('curso_id', array());
		
		 if (count($selected_cursos) > 0)
		 {
			foreach ($selected_cursos as $curso_id)
			{
			  if (\App\Curso::where('id','=',$curso_id)->count()==0)
			  {
				return \Response::json(array('curso_id' => ["Curso ID: $curso_id informado não cadastrado"]), 404);
			  }
			}
		 } else {
			 return \Response::json(array('curso_id' => ["O aluno deve ser matriculado em um curso"]), 404);
		 }
			
         $registro                  	= (object)request()->all();
		
		 $datadenascimento 			   	= Carbon::createFromFormat('d/m/Y',$registro->datadenascimento)->format('Y-m-d');
		
         $aluno                  		= new \App\Aluno();
         $aluno->nome        			= $registro->nome;
	  	 $aluno->email        			= $registro->email;
	   	 $aluno->datadenascimento 		= $datadenascimento;
		 
         if ($aluno->save())
		 {
			//
			// Cadastra todos os cursos selecionados na tabela matricula
			//
			foreach ($selected_cursos as $curso_id)
			{
			  if (\App\Curso::where('id','=',$curso_id)->count() >0)
			  {
				$curso 				= \App\Curso::find($curso_id);
				$curso->matriculas()->attach($aluno);
			  }
			}
		 } else {
			return \Response::json(array('id' => ['Erro na tentativa de inserir o aluno']), 404);
		 }
		 
         return \Response::json($aluno->id, 200);
        
    }
	
	public function update_aluno($id, Request $request)
    {
       
         $valid = validator(
                $request->only('nome','email','datadenascimento', 'curso_id'),
                [
                    'nome'                  => 'required|max:80',
					'email'                 => 'required|max:250',
					'datadenascimento'		=> 'date_format:d/m/Y',
					'curso_id.*'			=> 'required|numeric|distinct|min:1',
                ]
         );
		 
         if ($valid->fails()) {
            return \Response::json($valid->messages(), 400);
         }
		
		 $registro                  	= (object)request()->all();
		  
		 if (\App\Aluno::where('nome', '=', $registro->nome)->where('id', '<>', $id)->count() > 0) {
            return \Response::json(array('nome' => ['Nome do aluno já cadastrado, por favor verifique']), 400);
         }
		 
		 if (\App\Aluno::where('email', '=', $registro->email)->where('id', '<>', $id)->count() > 0) {
            return \Response::json(array('nome' => ['Email do aluno já cadastrado, por favor verifique']), 400);
         }
			
		 $aluno                  		= \App\Aluno::find($id);
		 
         if (!isset($aluno->id))
	     {
			 return \Response::json(array('id' => ["ID# $id do aluno não encontrado"]), 404);
		 }
		 
		//
		// Valida de curso(s) informado está cadastrado
		//
			
		 $selected_cursos 			= $request->get('curso_id', array());
		
		 if (count($selected_cursos) > 0)
		 {
			foreach ($selected_cursos as $curso_id)
			{
			  if (\App\Curso::where('id','=',$curso_id)->count()==0)
			  {
				return \Response::json(array('curso_id' => ["Curso ID: $curso_id informado não cadastrado"]), 404);
			  }
			}
		 } else {
			 return \Response::json(array('curso_id' => ["O aluno deve ser matriculado em um curso"]), 404);
		 }
		
		 $datadenascimento 			   	= Carbon::createFromFormat('d/m/Y',$registro->datadenascimento)->format('Y-m-d');
         $aluno->nome        			= $registro->nome;
	  	 $aluno->email        			= $registro->email;
	   	 $aluno->datadenascimento 		= $datadenascimento;
		 
         if ($aluno->save())
		 {
			$aluno_cursos 				= $aluno->matriculas()->pluck('curso_id')->all();
			//
			// Linha de balanço, determina quais cursos serão adicionados e/ou excluídos
			// de acordo com os cursos selecionados e já cadastrados 
			//
			$cursos_para_adicionar 		= array_diff($selected_cursos, $aluno_cursos);
			$cursos_para_excluir 		= array_diff($aluno_cursos, $selected_cursos);

			foreach ($cursos_para_adicionar as $curso_id) 
			{
				if (\App\Curso::where('id','=',$curso_id)->count() >0)
				{
					$curso 				= \App\Curso::find($curso_id);
					$curso->matriculas()->attach($aluno);
				}
			}

			foreach ($cursos_para_excluir as $curso_id) {
			  if (\App\Curso::where('id','=',$curso_id)->count() >0)
			  {
				$curso 					= \App\Curso::find($curso_id);
				$curso->matriculas()->detach($aluno);
			  }
			}
		 } else {
			return \Response::json(array('id' => ['Erro na tentativa de revisar o aluno']), 404);
         }
		
         return \Response::json($aluno->id, 200);
        
    }
	
	public function delete_aluno($id)
    {
       
         $aluno                  		= \App\Aluno::find($id);
		
		 if (!isset($aluno->id))
		 {
		    return \Response::json(array('id' => ["ID# $id do aluno não encontrado"]), 404);
		 }
		 
		//
		// Exclui as matriculas do aluno nos cursos
		//
			
		 DB::table('matriculas')
		   ->where('aluno_id','=',$id)
		   ->delete();
		   
	    //
		// Exclui o aluno 
		//
		
	     if (!$aluno->delete())
		 {
			 return \Response::json(array('id' => ['Erro na tentativa de excluir o aluno']), 404); 
		 }
						
         return \Response::json($aluno->id, 200);
        
    }
	
}