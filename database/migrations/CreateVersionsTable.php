<?php

use think\migration\Migrator;

class CreateVersionsTable extends Migrator
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        $this->schema->create('versions', function ($table) {
            $table->increments('id');
            $table->unsignedInteger('app_id')->comment('应用ID');
            $table->string('version_name', 64)->comment('版本名称，如：1.0.0');
            $table->unsignedInteger('version_code')->comment('版本号，如：1');
            $table->text('modify_content')->nullable()->comment('更新日志');
            $table->enum('update_status', ['1', '2', '3'])->default('1')->comment('更新状态：1(正常更新), 2(强制更新), 3(停用)');
            $table->string('apk_file_path', 255)->nullable()->comment('APK文件路径');
            $table->string('apk_size', 64)->nullable()->comment('APK文件大小');
            $table->string('apk_md5', 64)->nullable()->comment('APK文件MD5值');
            $table->string('flavor', 64)->nullable()->comment('渠道版本');
            $table->integer('download_count')->default(0)->comment('下载次数');
            $table->timestamp('publish_time')->nullable()->comment('发布时间');
            $table->unsignedInteger('created_user_id')->nullable()->comment('创建人ID');
            $table->unsignedInteger('updated_user_id')->nullable()->comment('修改人ID');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('app_id')->references('id')->on('apps')->onDelete('cascade');
            $table->unique(['app_id', 'version_code', 'flavor']);
            $table->index('app_id');
            $table->index('version_code');
            $table->index('update_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        $this->schema->dropIfExists('versions');
    }
}
