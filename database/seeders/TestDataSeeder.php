<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $password = Hash::make('123456789');
        $users = [
            ['name'=>'Maninderjeet','email'=>'mani@gmail.com','email_verified_at'=>$now,'password'=>$password,'role'=>'1','status'=>'1','created_at'=>$now,'updated_at'=>$now],
            ['name'=>'Creator','email'=>'creator@gmail.com','email_verified_at'=>$now,'password'=>$password,'role'=>'2','status'=>'1','created_at'=>$now,'updated_at'=>$now],
            ['name'=>'John Doe','email'=>'john@gmail.com','email_verified_at'=>$now,'password'=>$password,'role'=>'3','status'=>'1','created_at'=>$now,'updated_at'=>$now],
        ];

        $carbnDate = Carbon::now();
        $modifiedMutable = $carbnDate->add(3, 'day');

        //'field_name'=>'field_value','field_name'=>'field_value'
        $events = [
            ['user_id'=>2,'title'=>'Learn Laravel 12','description'=>'Learn Laravel 12 from Basic','event_type'=>'1','date'=>$modifiedMutable,'created_at'=>$now,'updated_at'=>$now]
        ];
        $slots = [
            ['event_id'=>1,'start_time'=>'10:00:00','end_time'=>'10:30:00','capacity'=>'10','zoom_meeting_id'=>'zoom_meeting_id_dummy','zoom_meeting_url'=>'https://www.zoom.com/','created_at'=>$now,'updated_at'=>$now],
            ['event_id'=>1,'start_time'=>'10:30:00','end_time'=>'11:00:00','capacity'=>'10','zoom_meeting_id'=>'zoom_meeting_id_dummy','zoom_meeting_url'=>'https://www.zoom.com/','created_at'=>$now,'updated_at'=>$now],
            
            ['event_id'=>1,'start_time'=>'11:00:00','end_time'=>'11:30:00','capacity'=>'10','zoom_meeting_id'=>'zoom_meeting_id_dummy','zoom_meeting_url'=>'https://www.zoom.com/','created_at'=>$now,'updated_at'=>$now],
            ['event_id'=>1,'start_time'=>'11:30:00','end_time'=>'12:00:00','capacity'=>'10','zoom_meeting_id'=>'zoom_meeting_id_dummy','zoom_meeting_url'=>'https://www.zoom.com/','created_at'=>$now,'updated_at'=>$now],
        ];

        DB::table('users')->insert($users);
        DB::table('events')->insert($events);
        DB::table('slots')->insert($slots);




    }
}
