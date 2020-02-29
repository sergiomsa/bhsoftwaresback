<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//
// Rotas para Crud CRUD de Áreas de Cursos
// Criar um gerenciamento aonde seja possível Criar, Listar, Editar e Visualizar uma
// área de curso (Biologia, Química, Física, por exemplo).
//

Route::group(array('prefix' => 'cursos'), function () {
  Route::group(array('prefix' => 'listar'), function () {
	    Route::get('/','Api\CursoController@listar_cursos');
  });
  Route::get('/','Api\CursoController@index_cursos');
  Route::get('{id}','Api\CursoController@view_curso');
  Route::post('/','Api\CursoController@store_curso');
  Route::post('{id}','Api\CursoController@update_curso');
  Route::delete('{id}','Api\CursoController@delete_curso');

});

//
// Rotas para CRUD de Alunos
// Criar um gerenciamento aonde seja possível Criar, Listar, Editar e Visualizar um
// Aluno.
//

Route::group(array('prefix' => 'alunos'), function () {
  Route::get('/','Api\AlunoController@index_alunos');
  Route::get('{id}','Api\AlunoController@view_aluno');
  Route::post('/','Api\AlunoController@store_aluno');
  Route::post('{id}','Api\AlunoController@update_aluno');
  Route::delete('{id}','Api\AlunoController@delete_aluno');
});



