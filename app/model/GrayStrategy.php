<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

class GrayStrategy extends Model
{
    protected $table = 'gray_strategies';
    protected $pk = 'id';
    
    protected $fillable = ['version_id', 'strategy_type', 'strategy_value', 'description', 'status', 'created_at', 'updated_at'];
    
    protected $type = [
        'id' => 'integer',
        'version_id' => 'integer',
        'status' => 'integer',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];
    
    protected $dateFormat = 'Y-m-d H:i:s';
    
    // 策略类型常量
    const STRATEGY_TYPE_PERCENTAGE = 'percentage'; // 按百分比
    const STRATEGY_TYPE_DEVICE = 'device';         // 按设备号
    const STRATEGY_TYPE_USER = 'user';             // 按用户ID
    
    /**
     * 关联版本
     */
    public function version()
    {
        return $this->belongsTo(Version::class, 'version_id', 'id');
    }
    
    /**
     * 检查设备是否符合灰度策略
     */
    public function matchDevice(string $deviceId): bool
    {
        if ($this->strategy_type === self::STRATEGY_TYPE_DEVICE) {
            $deviceList = json_decode($this->strategy_value, true) ?? [];
            return in_array($deviceId, $deviceList);
        }
        return false;
    }
    
    /**
     * 检查用户是否符合灰度策略
     */
    public function matchUser(int $userId): bool
    {
        if ($this->strategy_type === self::STRATEGY_TYPE_USER) {
            $userList = json_decode($this->strategy_value, true) ?? [];
            return in_array($userId, $userList);
        }
        return false;
    }
    
    /**
     * 检查是否符合百分比灰度
     */
    public function matchPercentage(string $deviceId): bool
    {
        if ($this->strategy_type === self::STRATEGY_TYPE_PERCENTAGE) {
            $percentage = intval($this->strategy_value);
            // 通过设备ID的哈希值来实现伪随机
            $hash = crc32($deviceId);
            $value = ($hash % 100) + 1;
            return $value <= $percentage;
        }
        return false;
    }
}
