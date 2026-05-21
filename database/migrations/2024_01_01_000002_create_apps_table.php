<?php

use think\migration\Migrator;
use think\migration\Migration;

class CreateAppsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema->create('apps', function ($table) {
            $table->increments('id');
            $table->unsignedInteger('account_id')->comment('账户ID');
            $table->string('app_key', 100)->unique()->comment('应用唯一标识(包名)');
            $table->string('app_name', 100)->comment('应用名称');
            $table->string('icon_url', 255)->nullable()->comment('应用图标URL');
            $table->string('description', 500)->nullable()->comment('应用描述');
            $table->string('developer', 100)->nullable()->comment('开发者');
            $table->string('version_name', 50)->default('1.0.0')->comment('当前版本名');
            $table->integer('version_code')->default(1)->comment('当前版本号');
            $table->timestamp('created_at')->useCurrent()->comment('创建时间');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('更新时间');
            $table->tinyInteger('is_del')->default(0)->comment('是否删除(0:否,1:是)');
            
            $table->index('account_id');
            $table->index('app_key');
            $table->comment('应用表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->schema->dropIfExists('apps');
    }
}
