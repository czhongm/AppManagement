<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

class Account extends Model
{
    protected $table = 'accounts';
    protected $pk = 'id';
    
    // 隐藏密码字段
    protected $hidden = ['password'];
    
    // 允许批量赋值
    protected $fillable = ['login_name', 'password', 'nick', 'authority', 'phone', 'address', 'register_time', 'avatar', 'status'];
    
    // 字段类型转换
    protected $type = [
        'id' => 'integer',
        'register_time' => 'timestamp',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];
    
    // 日期字段
    protected $dateFormat = 'Y-m-d H:i:s';
    
    /**
     * 验证权限
     */
    public function isAdmin(): bool
    {
        return $this->authority === 'admin';
    }
    
    /**
     * 验证是否已激活
     */
    public function isActive(): bool
    {
        return $this->status === 1;
    }
    
    /**
     * 关联应用
     */
    public function apps()
    {
        return $this->hasMany(App::class, 'owner_id', 'id');
    }
}
