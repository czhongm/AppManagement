<?php

namespace app\service;

use app\model\App;
use think\facade\Db;
use Exception;

class AppService
{
    /**
     * 创建应用
     * @param int $userId 用户ID
     * @param array $data 应用数据
     * @return int 应用ID
     * @throws Exception
     */
    public function createApp(int $userId, array $data): int
    {
        // 检查应用标识是否唯一
        $exists = App::where('app_key', $data['appKey'])->find();
        if ($exists) {
            throw new Exception('应用标识已存在', 2001);
        }
        
        $app = new App();
        $app->user_id = $userId;
        $app->app_key = $data['appKey'];
        $app->app_name = $data['appName'] ?? '';
        $app->package_name = $data['packageName'] ?? '';
        $app->description = $data['description'] ?? '';
        $app->icon_url = $data['iconUrl'] ?? '';
        $app->create_time = time();
        
        if ($app->save()) {
            return $app->id;
        }
        
        throw new Exception('应用创建失败', 2002);
    }

    /**
     * 获取应用列表
     * @param int $userId 用户ID
     * @param int $pageNum 页码
     * @param int $pageSize 每页数量
     * @return array
     */
    public function getAppList(int $userId, int $pageNum = 1, int $pageSize = 10): array
    {
        $query = App::where('user_id', $userId);
        
        $total = $query->count();
        $list = $query->page($pageNum, $pageSize)
            ->order('id', 'desc')
            ->select()
            ->toArray();
        
        $data = array_map(function($item) {
            return [
                'id' => $item['id'],
                'appKey' => $item['app_key'],
                'appName' => $item['app_name'],
                'packageName' => $item['package_name'],
                'description' => $item['description'],
                'iconUrl' => $item['icon_url'],
                'createTime' => $item['create_time']
            ];
        }, $list);
        
        return [
            'total' => $total,
            'pageNum' => $pageNum,
            'pageSize' => $pageSize,
            'array' => $data
        ];
    }

    /**
     * 获取应用详情
     * @param int $appId 应用ID
     * @param int $userId 用户ID
     * @return array
     * @throws Exception
     */
    public function getAppDetail(int $appId, int $userId): array
    {
        $app = App::where('id', $appId)
            ->where('user_id', $userId)
            ->find();
        
        if (!$app) {
            throw new Exception('应用不存在或无权限访问', 2003);
        }
        
        return [
            'id' => $app['id'],
            'appKey' => $app['app_key'],
            'appName' => $app['app_name'],
            'packageName' => $app['package_name'],
            'description' => $app['description'],
            'iconUrl' => $app['icon_url'],
            'createTime' => $app['create_time']
        ];
    }

    /**
     * 更新应用
     * @param int $appId 应用ID
     * @param int $userId 用户ID
     * @param array $data 更新数据
     * @return bool
     * @throws Exception
     */
    public function updateApp(int $appId, int $userId, array $data): bool
    {
        $app = App::where('id', $appId)
            ->where('user_id', $userId)
            ->find();
        
        if (!$app) {
            throw new Exception('应用不存在或无权限访问', 2003);
        }
        
        $updateData = [];
        if (isset($data['appName'])) $updateData['app_name'] = $data['appName'];
        if (isset($data['description'])) $updateData['description'] = $data['description'];
        if (isset($data['iconUrl'])) $updateData['icon_url'] = $data['iconUrl'];
        
        $updateData['update_time'] = time();
        
        return App::update($updateData, ['id' => $appId]);
    }

    /**
     * 删除应用
     * @param int $appId 应用ID
     * @param int $userId 用户ID
     * @return bool
     * @throws Exception
     */
    public function deleteApp(int $appId, int $userId): bool
    {
        $app = App::where('id', $appId)
            ->where('user_id', $userId)
            ->find();
        
        if (!$app) {
            throw new Exception('应用不存在或无权限访问', 2003);
        }
        
        return $app->delete();
    }
}
