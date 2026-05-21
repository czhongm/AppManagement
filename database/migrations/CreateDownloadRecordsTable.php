<?php

use think\migration\Migrator;

class CreateDownloadRecordsTable extends Migrator
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        $this->schema->create('download_records', function ($table) {
            $table->increments('id');
            $table->unsignedInteger('version_id')->comment('版本ID');
            $table->string('device_id', 255)->nullable()->comment('设备ID');
            $table->string('user_id', 128)->nullable()->comment('用户ID');
            $table->string('ip_address', 45)->nullable()->comment('下载IP地址');
            $table->string('user_agent', 500)->nullable()->comment('用户代理信息');
            $table->timestamp('download_time')->useCurrent()->comment('下载时间');
            $table->timestamps();
            $table->foreign('version_id')->references('id')->on('versions')->onDelete('cascade');
            $table->index('version_id');
            $table->index('device_id');
            $table->index('user_id');
            $table->index('download_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        $this->schema->dropIfExists('download_records');
    }
}
