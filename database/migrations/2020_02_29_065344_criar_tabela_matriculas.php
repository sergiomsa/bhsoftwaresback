<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CriarTabelaMatriculas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matriculas', function (Blueprint $table) {
		    $table->bigInteger('aluno_id')->unsigned()->index();
            $table->bigInteger('curso_id')->unsigned()->index();
            $table->timestamps();
        });
		
		Schema::table('matriculas', function (Blueprint $table)
		{
			$table->foreign('aluno_id')->references('id')->on('alunos');
			$table->foreign('curso_id')->references('id')->on('cursos');
		});
   
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('matriculas', function (Blueprint $table)
		{
			$table->dropForeign('matriculas_aluno_id_foreign');
			$table->dropForeign('matriculas_curso_id_foreign');
		});
        Schema::dropIfExists('matriculas');
    }
}
