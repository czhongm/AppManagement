<?php

use think\migration\Migrator;
use think\migration\Migration;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema->create('accounts', function ($table) {
            $table->increments('id');
            $table->string('login_name', 100)->unique()->comment('登录用户名');
            $table->string('password', 255)->comment('密码');
            $table->string('nick', 100)->comment('昵称');
            $table->enum('authority', ['admin', 'editor'])->default('editor')->comment('权限(admin:管理员,editor:普通用户)');
            $table->string('phone', 20)->nullable()->comment('手机号');
            $table->string('address', 255)->nullable()->comment('地址');
            $table->string('avatar', 255)->nullable()->comment('头像');
            $table->timestamp('register_time')->useCurrent()->comment('注册时间');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('更新时间');
            $table->tinyInteger('is_del')->default(0)->comment('是否删除(0:否,1:是)');
            
            $table->index('login_name');
            $table->index('authority');
            $table->comment('用户表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->schema->dropIfExists('accounts');
    }
}
