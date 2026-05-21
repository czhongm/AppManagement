<?php

use think\migration\Migrator;

class CreateAppSharesTable extends Migrator
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        $this->schema->create('app_shares', function ($table) {
            $table->increments('id');
            $table->unsignedInteger('app_id')->comment('应用ID');
            $table->unsignedInteger('user_id')->comment('被分享用户ID');
            $table->enum('permission', ['view', 'edit', 'manage'])->default('view')->comment('权限：view(查看), edit(编辑), manage(管理)');
            $table->unsignedInteger('created_user_id')->nullable()->comment('创建人ID');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('app_id')->references('id')->on('apps')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->unique(['app_id', 'user_id']);
            $table->index('app_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        $this->schema->dropIfExists('app_shares');
    }
}
