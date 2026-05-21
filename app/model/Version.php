<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

class Version extends Model
{
    protected $table = 'versions';
    protected $pk = 'id';
    
    protected $fillable = ['app_id', 'version_name', 'version_code', 'modify_content', 'apk_url', 'apk_size', 'apk_md5', 'update_status', 'flavor', 'upload_time', 'status', 'created_at', 'updated_at'];
    
    protected $type = [
        'id' => 'integer',
        'app_id' => 'integer',
        'version_code' => 'integer',
        'apk_size' => 'integer',
        'update_status' => 'integer',
        'status' => 'integer',
        'upload_time' => 'timestamp',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];
    
    protected $dateFormat = 'Y-m-d H:i:s';
    
    // 更新状态常量
    const UPDATE_STATUS_NORMAL = 1;     // 正常更新
    const UPDATE_STATUS_FORCE = 2;      // 强制更新
    const UPDATE_STATUS_DISABLED = 3;   // 停用
    
    /**
     * 关联应用
     */
    public function app()
    {
        return $this->belongsTo(App::class, 'app_id', 'id');
    }
    
    /**
     * 关联灰度策略
     */
    public function grayStrategies()
    {
        return $this->hasMany(GrayStrategy::class, 'version_id', 'id');
    }
    
    /**
     * 关联下载记录
     */
    public function downloadRecords()
    {
        return $this->hasMany(DownloadRecord::class, 'version_id', 'id');
    }
    
    /**
     * 是否强制更新
     */
    public function isMustUpdate(): bool
    {
        return $this->update_status === self::UPDATE_STATUS_FORCE;
    }
    
    /**
     * 是否已停用
     */
    public function isDisabled(): bool
    {
        return $this->update_status === self::UPDATE_STATUS_DISABLED;
    }
    
    /**
     * 获取下载次数
     */
    public function getDownloadCount(): int
    {
        return DownloadRecord::where('version_id', $this->id)->count();
    }
}
