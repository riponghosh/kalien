<?php

use Illuminate\Database\Seeder;

class TripActivityRuleInfoTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    protected $data;

    function __construct()
    {
        $this->data = json_decode(File::get("database/data/trip_activity_rule_infos.json"),true);

    }

    public function run()
    {
        DB::table('trip_activity_rule_info_types')->truncate();
        DB::table('trip_activity_rule_info_types')->insert($this->data);
    }
}
