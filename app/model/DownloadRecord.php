<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

class DownloadRecord extends Model
{
    protected $table = 'download_records';
    protected $pk = 'id';
    
    protected $fillable = ['version_id', 'device_id', 'user_id', 'ip_address', 'user_agent', 'download_time'];
    
    protected $type = [
        'id' => 'integer',
        'version_id' => 'integer',
        'user_id' => 'integer',
        'download_time' => 'timestamp',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];
    
    protected $dateFormat = 'Y-m-d H:i:s';
    
    /**
     * 关联版本
     */
    public function version()
    {
        return $this->belongsTo(Version::class, 'version_id', 'id');
    }
    
    /**
     * 关联用户
     */
    public function user()
    {
        return $this->belongsTo(Account::class, 'user_id', 'id');
    }
}
