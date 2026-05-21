<?php

use think\migration\Migrator;

class CreateAccountsTable extends Migrator
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        $this->schema->create('accounts', function ($table) {
            $table->increments('id');
            $table->string('login_name', 64)->unique()->comment('登录用户名');
            $table->string('password', 255)->comment('密码');
            $table->string('nick', 128)->comment('昵称');
            $table->enum('authority', ['admin', 'editor'])->default('editor')->comment('权限：admin(管理员), editor(普通用户)');
            $table->string('phone', 20)->nullable()->comment('手机号');
            $table->string('address', 255)->nullable()->comment('地址');
            $table->string('avatar', 255)->nullable()->comment('头像URL');
            $table->text('remarks')->nullable()->comment('备注');
            $table->tinyInteger('status')->default(1)->comment('账户状态：0(禁用), 1(正常)');
            $table->unsignedInteger('created_user_id')->nullable()->comment('创建人ID');
            $table->unsignedInteger('updated_user_id')->nullable()->comment('修改人ID');
            $table->timestamps();
            $table->softDeletes();
            $table->index('login_name');
            $table->index('authority');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        $this->schema->dropIfExists('accounts');
    }
}
