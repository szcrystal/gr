<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveryGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_groups', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('name')->nullable()->default(NULL);
            $table->boolean('open_status')->nullable()->default(NULL);
            $table->integer('capacity')->nullable()->default(NULL);
            $table->float('factor')->nullable()->default(NULL);
            $table->boolean('is_time')->nullable()->default(NULL);
            $table->string('time_table')->nullable()->default(NULL);
//            $table->string('title')->nullable()->default(NULL);
//            $table->text('header')->nullable()->default(NULL);
//            $table->text('footer')->nullable()->default(NULL);
            
            $table->timestamps();
        });
        
        DB::table('delivery_groups')->insert(
            [ 
                'name' => '下草(小)',
                'open_status' => 1,
                'capacity' => 20,
                
                'created_at' => date('Y-m-d H:i:s', time()),
                'updated_at' => date('Y-m-d H:i:s', time()),
            ]
        );
        
        DB::table('delivery_groups')->insert(
            [ 
                'name' => '下草(大)',
                'open_status' => 1,
                'capacity' => 40,
                
                'created_at' => date('Y-m-d H:i:s', time()),
                'updated_at' => date('Y-m-d H:i:s', time()),
            ]
        );
        
        DB::table('delivery_groups')->insert(
            [ 
                'name' => 'モリヤコニファー（小）/低木・這い性コニファー',
                'open_status' => 1,
                'capacity' => 5,
                
                'created_at' => date('Y-m-d H:i:s', time()),
                'updated_at' => date('Y-m-d H:i:s', time()),
            ]
        );
        
        DB::table('delivery_groups')->insert(
            [ 
                'name' => 'モリヤコニファー（大）/高木コニファー',
                'open_status' => 1,
                'capacity' => 10,
                
                'created_at' => date('Y-m-d H:i:s', time()),
                'updated_at' => date('Y-m-d H:i:s', time()),
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_groups');
    }
}
