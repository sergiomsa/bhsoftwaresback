<?php

use Illuminate\Database\Seeder;

class MatriculasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         factory(App\Aluno::class, 1000)->create()->each(function(App\Aluno $aluno){
			$aluno->matriculas()->attach([
			   rand(1,5),
			   rand(6,10),
			   rand(11,20),
			]);
		 });
	}
}
