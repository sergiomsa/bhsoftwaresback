<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Carbon\Carbon;
use DB;

class CursoController extends Controller
{
	//
	// retorna a lista de cursos paginada ordenada e filtrada de acordo 
	// com o campo (nome e email) selecionado
	// 
	
	public function index_cursos(Request $request)
    {
		
        $limite              = $request->input('limite', 10);
		$orderby             = $request->input('orderby', 'titulo');
		$campo				 = $request->input('campo', 'titulo');
		$direction           = $request->input('direction', 'asc');
		$conteudo            = $request->input('conteudo', '');
						
		$cursos          	 = \App\Curso::select('id','titulo','descricao')
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
		
		$cursos->getCollection()->transform(function ($curso) {
			
			$sql                       	= "select count(*) as count from matriculas where curso_id=$curso->id";
			$curso->matriculas        	= DB::select($sql);
			
			return $curso;
		});						
		return \Response::json($cursos, 200);
        
    }
	
	public function view_curso($id)
    {
	   $curso				= \App\Curso::select('id','titulo','descricao')
										  ->find($id);
		   
	   if (!isset($curso->id))
	   {
		  return \Response::json(array('id' => ["ID# $id do curso não encontrado"]), 404);
	   }
        
       return \Response::json($curso, 200);
        
    }
	
	public function store_curso(Request $request)
    {
       
       $valid = validator(
                $request->only('titulo','descricao'),
                [
                    'titulo'                  => 'required|unique:cursos,titulo|max:100',
					'descricao'               => 'required|max:400',
                ]
         );
		 
         if ($valid->fails()) {
            return \Response::json($valid->messages(), 400);
         }
			
         $registro                  	= (object)request()->all();
         $curso                  		= new \App\Curso();
         $curso->titulo        			= $registro->titulo;
	  	 $curso->descricao    			= $registro->descricao;
		 
	     if (!$curso->save())
		 {
			return \Response::json(array('id' => ["Erro na tentativa de inserir o curso"]), 404);
		 }

         return \Response::json($curso->id, 200);
        
    }
	
	public function update_curso($id, Request $request)
    {
       
         $valid = validator(
                $request->only('titulo','descricao'),
                [
                    'titulo'                  => 'required|max:100',
					'descricao'               => 'required|max:400',
                ]
         );
		 
		  $registro                  	= (object)request()->all();
         if ($valid->fails()) {
            return \Response::json($valid->messages(), 400);
         }
		
		 if (\App\Curso::where('titulo', '=', $registro->titulo)->where('id', '<>', $id)->count() > 0) {
            return \Response::json(array('titulo' => ['Nome do curso já cadastrado, por favor verifique']), 400);
         }
		 
		 $curso                  		= \App\Curso::find($id);
		 
         if (!isset($curso->id))
	     {
			 return \Response::json(array('id' => ["ID# $id do curso não encontrado"]), 404);
		 }
		   
	     $curso->titulo        			= $registro->titulo;
	  	 $curso->descricao    			= $registro->descricao;
		 
         if (!$curso->save())
		 {
			return \Response::json(array('id' => ['Erro na tentativa de revisar o curso']), 404);
         }
		
         return \Response::json($curso->id, 200);
        
    }
	
	public function delete_curso($id)
    {
       
         $curso                  		= \App\Curso::withcount('matriculas')->find($id);
		
		 if (!isset($curso->id))
		 {
		    return \Response::json(array('id' => ["ID# $id do curso não encontrado"]), 404);
		 }
		   
		 if ($curso->matriculas_count > 0)
         {   /* exibe a mensagem informando que não pode excluir */
            return \Response::json(array('id' => ["Curso não pode ser excluido, existem: $curso->matriculas_count aluno(s) matriculado(s)"]), 400);
         }
		
         if (!$curso->delete())
		 {
			return \Response::json(array('id' => ['Erro na tentativa de excluir o curso']), 404); 
		 }
		 
         return \Response::json($curso->id, 200);
        
    }
	
	public function listar_cursos()
    {
		
		$cursos          	 = \App\Curso::select('id','titulo')
										  ->orderBY('titulo','asc')
										  ->get();
									 
		return \Response::json($cursos, 200);
        
    }
	
}