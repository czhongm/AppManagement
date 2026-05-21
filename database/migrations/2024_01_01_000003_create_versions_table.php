<?php

use think\migration\Migrator;
use think\migration\Migration;

class CreateVersionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema->create('versions', function ($table) {
            $table->increments('id');
            $table->unsignedInteger('app_id')->comment('应用ID');
            $table->string('version_name', 50)->comment('版本名称');
            $table->integer('version_code')->comment('版本号');
            $table->string('apk_file_path', 255)->comment('APK文件路径');
            $table->integer('apk_file_size')->default(0)->comment('APK文件大小(字节)');
            $table->string('apk_md5', 100)->nullable()->comment('APK文件MD5');
            $table->text('modify_content')->nullable()->comment('更新日志');
            $table->enum('update_status', ['1', '2', '3'])->default('1')->comment('更新状态(1:正常更新,2:强制更新,3:停用)');
            $table->string('flavor', 50)->default('default')->comment('渠道/Flavor');
            $table->timestamp('upload_time')->useCurrent()->comment('上传时间');
            $table->timestamp('created_at')->useCurrent()->comment('创建时间');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('更新时间');
            $table->integer('download_count')->default(0)->comment('下载次数');
            $table->tinyInteger('is_del')->default(0)->comment('是否删除(0:否,1:是)');
            
            $table->index('app_id');
            $table->index('version_code');
            $table->index('flavor');
            $table->unique(['app_id', 'version_code', 'flavor']);
            $table->comment('版本表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->schema->dropIfExists('versions');
    }
}
