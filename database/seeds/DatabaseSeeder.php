<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->truncate_all_tables();
        factory(App\Models\Product::class, 1)->create();
        factory(App\Models\TripActivityTicket::class, 1)->create();
        factory(App\User::class, 1)->create();
        factory(App\Merchant\Merchant::class, 1)->create();
    }

    private function truncate_all_tables()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $tables = array_map('current', DB::select('SHOW TABLES'));
        foreach ($tables as $table) {
            // if you don't want to truncate migrations
            if ($table == 'migrations') {
                continue;
            }
            DB::table($table)->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
