<?php

namespace app\service;

use app\model\GrayStrategy;
use think\facade\Db;
use Exception;

class GrayUpdateService
{
    const STRATEGY_PERCENTAGE = 'percentage';      // 百分比
    const STRATEGY_DEVICE = 'device';              // 设备号
    const STRATEGY_USER = 'user';                  // 用户ID
    const STRATEGY_ALL = 'all';                    // 全量

    /**
     * 创建灰度策略
     * @param int $versionId 版本ID
     * @param array $data 策略数据
     * @return int 策略ID
     * @throws Exception
     */
    public function createStrategy(int $versionId, array $data): int
    {
        $strategy = new GrayStrategy();
        $strategy->version_id = $versionId;
        $strategy->strategy_type = $data['strategyType'] ?? self::STRATEGY_ALL;
        $strategy->strategy_value = $data['strategyValue'] ?? '';
        $strategy->description = $data['description'] ?? '';
        $strategy->create_time = time();
        
        if ($strategy->save()) {
            return $strategy->id;
        }
        
        throw new Exception('灰度策略创建失败', 4001);
    }

    /**
     * 检查设备是否应该更新
     * @param int $versionId 版本ID
     * @param string $deviceId 设备ID
     * @param int $userId 用户ID（可选）
     * @return bool
     */
    public function shouldUpdate(int $versionId, string $deviceId, int $userId = 0): bool
    {
        $strategy = GrayStrategy::where('version_id', $versionId)
            ->where('status', 1)
            ->find();
        
        if (!$strategy) {
            // 无灰度策略，默认全量更新
            return true;
        }
        
        switch ($strategy['strategy_type']) {
            case self::STRATEGY_ALL:
                return true;
                
            case self::STRATEGY_PERCENTAGE:
                return $this->checkPercentage($deviceId, $strategy['strategy_value']);
                
            case self::STRATEGY_DEVICE:
                return $this->checkDevice($deviceId, $strategy['strategy_value']);
                
            case self::STRATEGY_USER:
                return $this->checkUser($userId, $strategy['strategy_value']);
                
            default:
                return false;
        }
    }

    /**
     * 检查百分比灰度
     * @param string $deviceId 设备ID
     * @param string $percentage 百分比
     * @return bool
     */
    private function checkPercentage(string $deviceId, string $percentage): bool
    {
        $percent = intval($percentage);
        
        if ($percent <= 0 || $percent >= 100) {
            return $percent == 100;
        }
        
        // 使用设备ID的哈希值作为灰度依据
        $hash = crc32($deviceId);
        $hashPercent = ($hash % 100) + 1;
        
        return $hashPercent <= $percent;
    }

    /**
     * 检查设备号灰度
     * @param string $deviceId 设备ID
     * @param string $deviceList 设备列表（逗号分隔）
     * @return bool
     */
    private function checkDevice(string $deviceId, string $deviceList): bool
    {
        $devices = array_filter(array_map('trim', explode(',', $deviceList)));
        return in_array($deviceId, $devices);
    }

    /**
     * 检查用户ID灰度
     * @param int $userId 用户ID
     * @param string $userList 用户列表（逗号分隔）
     * @return bool
     */
    private function checkUser(int $userId, string $userList): bool
    {
        if ($userId <= 0) {
            return false;
        }
        
        $users = array_filter(array_map('trim', explode(',', $userList)));
        return in_array($userId, $users);
    }

    /**
     * 获取灰度策略
     * @param int $versionId 版本ID
     * @return array|null
     */
    public function getStrategy(int $versionId): ?array
    {
        $strategy = GrayStrategy::where('version_id', $versionId)->find();
        
        if (!$strategy) {
            return null;
        }
        
        return [
            'id' => $strategy['id'],
            'versionId' => $strategy['version_id'],
            'strategyType' => $strategy['strategy_type'],
            'strategyValue' => $strategy['strategy_value'],
            'description' => $strategy['description'],
            'status' => $strategy['status']
        ];
    }

    /**
     * 更新灰度策略
     * @param int $strategyId 策略ID
     * @param array $data 更新数据
     * @return bool
     * @throws Exception
     */
    public function updateStrategy(int $strategyId, array $data): bool
    {
        $strategy = GrayStrategy::find($strategyId);
        
        if (!$strategy) {
            throw new Exception('灰度策略不存在', 4002);
        }
        
        $updateData = [];
        if (isset($data['strategyType'])) $updateData['strategy_type'] = $data['strategyType'];
        if (isset($data['strategyValue'])) $updateData['strategy_value'] = $data['strategyValue'];
        if (isset($data['description'])) $updateData['description'] = $data['description'];
        if (isset($data['status'])) $updateData['status'] = $data['status'];
        
        $updateData['update_time'] = time();
        
        return GrayStrategy::update($updateData, ['id' => $strategyId]);
    }

    /**
     * 删除灰度策略
     * @param int $strategyId 策略ID
     * @return bool
     * @throws Exception
     */
    public function deleteStrategy(int $strategyId): bool
    {
        $strategy = GrayStrategy::find($strategyId);
        
        if (!$strategy) {
            throw new Exception('灰度策略不存在', 4002);
        }
        
        return $strategy->delete();
    }
}
