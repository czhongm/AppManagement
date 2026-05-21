<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

class App extends Model
{
    protected $table = 'apps';
    protected $pk = 'id';
    
    protected $fillable = ['app_key', 'app_name', 'package_name', 'icon_url', 'description', 'owner_id', 'status', 'created_at', 'updated_at'];
    
    protected $type = [
        'id' => 'integer',
        'owner_id' => 'integer',
        'status' => 'integer',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];
    
    protected $dateFormat = 'Y-m-d H:i:s';
    
    /**
     * 关联所有者
     */
    public function owner()
    {
        return $this->belongsTo(Account::class, 'owner_id', 'id');
    }
    
    /**
     * 关联版本
     */
    public function versions()
    {
        return $this->hasMany(Version::class, 'app_id', 'id');
    }
    
    /**
     * 获取最新版本
     */
    public function latestVersion()
    {
        return $this->hasOne(Version::class, 'app_id', 'id')
            ->order('version_code', 'desc');
    }
    
    /**
     * 检查应用是否可用
     */
    public function isAvailable(): bool
    {
        return $this->status === 1;
    }
}
