<?php

use Illuminate\Database\Seeder;

class LanguagesTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('languages')->insert([
			[
				'id' => 1,
				'language_name' => 'Chinese',
			],
			[
				'id' => 2,
				'language_name' => 'English',
			],
			[
				'id' => 3,
				'language_name' => 'Japanese',
			],
			[
				'id' => 4,
				'language_name' => 'Korean',
			],
			[
				'id' => 5,
				'language_name' => 'Cantonese',
			],
			[
				'id' => 6,
				'language_name' => 'Thai',
			],
			[
				'id' => 7,
				'language_name' => 'Malay',
			],
			[
				'id' => 8,
				'language_name' => 'Portuguese',
			],
			[
				'id' => 9,
				'language_name' => 'French',
			],
			[
				'id' => 10,
				'language_name' => 'Taiwanese',
			],

		]);

	}
}
