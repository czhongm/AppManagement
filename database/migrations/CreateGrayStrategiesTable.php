<?php

use think\migration\Migrator;

class CreateGrayStrategiesTable extends Migrator
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        $this->schema->create('gray_strategies', function ($table) {
            $table->increments('id');
            $table->unsignedInteger('version_id')->comment('版本ID');
            $table->enum('strategy_type', ['percentage', 'device', 'user'])->comment('策略类型：percentage(百分比), device(设备号), user(用户ID)');
            $table->text('strategy_value')->comment('策略值，JSON格���');
            $table->text('description')->nullable()->comment('描述');
            $table->tinyInteger('status')->default(1)->comment('状态：0(禁用), 1(启用)');
            $table->unsignedInteger('created_user_id')->nullable()->comment('创建人ID');
            $table->unsignedInteger('updated_user_id')->nullable()->comment('修改人ID');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('version_id')->references('id')->on('versions')->onDelete('cascade');
            $table->index('version_id');
            $table->index('strategy_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        $this->schema->dropIfExists('gray_strategies');
    }
}
