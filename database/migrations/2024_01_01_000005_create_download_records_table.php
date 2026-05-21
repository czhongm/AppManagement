<?php

use think\migration\Migrator;
use think\migration\Migration;

class CreateDownloadRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema->create('download_records', function ($table) {
            $table->increments('id');
            $table->unsignedInteger('version_id')->comment('版本ID');
            $table->string('device_id', 100)->comment('设备ID');
            $table->string('ip_address', 50)->nullable()->comment('下载IP地址');
            $table->string('user_agent', 255)->nullable()->comment('用户代理');
            $table->timestamp('download_time')->useCurrent()->comment('下载时间');
            $table->tinyInteger('is_del')->default(0)->comment('是否删除(0:否,1:是)');
            
            $table->index('version_id');
            $table->index('device_id');
            $table->index('download_time');
            $table->comment('下载记录表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->schema->dropIfExists('download_records');
    }
}
