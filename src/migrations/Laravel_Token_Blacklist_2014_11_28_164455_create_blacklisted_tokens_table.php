<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBlacklistedTokensTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('blacklisted_tokens', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('user_id');
			$table->string('token')->unique();
			$table->datetime('expiry');
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('blacklisted_tokens');
	}

}
