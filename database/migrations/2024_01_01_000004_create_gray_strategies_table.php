<?php

use think\migration\Migrator;
use think\migration\Migration;

class CreateGrayStrategiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema->create('gray_strategies', function ($table) {
            $table->increments('id');
            $table->unsignedInteger('version_id')->comment('版本ID');
            $table->enum('strategy_type', ['percentage', 'device', 'user'])->comment('策略类型(percentage:百分比,device:设备号,user:用户ID)');
            $table->text('strategy_value')->comment('策略值');
            $table->string('description', 255)->nullable()->comment('策略描述');
            $table->tinyInteger('is_enabled')->default(1)->comment('是否启用(0:否,1:是)');
            $table->timestamp('created_at')->useCurrent()->comment('创建时间');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('更新时间');
            $table->tinyInteger('is_del')->default(0)->comment('是否删除(0:否,1:是)');
            
            $table->index('version_id');
            $table->index('strategy_type');
            $table->comment('灰度策略表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->schema->dropIfExists('gray_strategies');
    }
}
