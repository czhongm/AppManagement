<?php

use think\migration\Migrator;

class CreateAppsTable extends Migrator
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        $this->schema->create('apps', function ($table) {
            $table->increments('id');
            $table->string('app_key', 128)->unique()->comment('应用唯一标识(包名)');
            $table->string('app_name', 128)->comment('应用名称');
            $table->string('icon_url', 255)->nullable()->comment('应用图标URL');
            $table->text('description')->nullable()->comment('应用描述');
            $table->string('platform', 32)->default('android')->comment('应用平台：android, ios');
            $table->unsignedInteger('owner_id')->comment('应用所有者ID');
            $table->tinyInteger('status')->default(1)->comment('状态：0(禁用), 1(正常)');
            $table->timestamps();
            $table->softDeletes();
            $table->index('app_key');
            $table->index('owner_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        $this->schema->dropIfExists('apps');
    }
}
