<?php

namespace app\service;

use app\model\Version;
use app\model\App;
use think\facade\Db;
use Exception;

class VersionService
{
    // 更新状态常量
    const UPDATE_STATUS_NORMAL = 1;     // 正常更新
    const UPDATE_STATUS_FORCE = 2;      // 强制更新
    const UPDATE_STATUS_DISABLED = 3;   // 停用

    /**
     * 创建版本
     * @param int $userId 用户ID
     * @param int $appId 应用ID
     * @param array $data 版本数据
     * @return int 版本ID
     * @throws Exception
     */
    public function createVersion(int $userId, int $appId, array $data): int
    {
        // 验证应用所有权
        $app = App::where('id', $appId)
            ->where('user_id', $userId)
            ->find();
        
        if (!$app) {
            throw new Exception('应用不存在或无权限访问', 3001);
        }
        
        $version = new Version();
        $version->app_id = $appId;
        $version->version_code = $data['versionCode'];
        $version->version_name = $data['versionName'];
        $version->modify_content = $data['modifyContent'] ?? '';
        $version->update_status = $data['updateStatus'] ?? self::UPDATE_STATUS_NORMAL;
        $version->apk_file_path = $data['apkFilePath'] ?? '';
        $version->apk_size = $data['apkSize'] ?? 0;
        $version->apk_md5 = $data['apkMd5'] ?? '';
        $version->flavor = $data['flavor'] ?? '';
        $version->upload_time = time();
        $version->create_time = time();
        
        if ($version->save()) {
            return $version->id;
        }
        
        throw new Exception('版本创建失败', 3002);
    }

    /**
     * 获取版本列表
     * @param int $userId 用户ID
     * @param int $appId 应用ID
     * @param int $pageNum 页码
     * @param int $pageSize 每页数量
     * @return array
     * @throws Exception
     */
    public function getVersionList(int $userId, int $appId, int $pageNum = 1, int $pageSize = 10): array
    {
        // 验证应用所有权
        $app = App::where('id', $appId)
            ->where('user_id', $userId)
            ->find();
        
        if (!$app) {
            throw new Exception('应用不存在或无权限访问', 3001);
        }
        
        $query = Version::where('app_id', $appId);
        
        $total = $query->count();
        $list = $query->page($pageNum, $pageSize)
            ->order('version_code', 'desc')
            ->select()
            ->toArray();
        
        $data = array_map(function($item) {
            return [
                'id' => $item['id'],
                'appId' => $item['app_id'],
                'versionCode' => $item['version_code'],
                'versionName' => $item['version_name'],
                'modifyContent' => $item['modify_content'],
                'updateStatus' => $item['update_status'],
                'apkFileSize' => $item['apk_size'],
                'apkMd5' => $item['apk_md5'],
                'flavor' => $item['flavor'],
                'uploadTime' => $item['upload_time'],
                'downloadCount' => 0
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
     * 获取版本详情
     * @param int $versionId 版本ID
     * @param int $userId 用户ID
     * @return array
     * @throws Exception
     */
    public function getVersionDetail(int $versionId, int $userId): array
    {
        $version = Version::field('v.*, a.id as app_id')
            ->alias('v')
            ->join('app a', 'v.app_id = a.id')
            ->where('v.id', $versionId)
            ->where('a.user_id', $userId)
            ->find();
        
        if (!$version) {
            throw new Exception('版本不存在或无权限访问', 3003);
        }
        
        return [
            'id' => $version['id'],
            'appId' => $version['app_id'],
            'versionCode' => $version['version_code'],
            'versionName' => $version['version_name'],
            'modifyContent' => $version['modify_content'],
            'updateStatus' => $version['update_status'],
            'apkFileSize' => $version['apk_size'],
            'apkMd5' => $version['apk_md5'],
            'flavor' => $version['flavor'],
            'uploadTime' => $version['upload_time']
        ];
    }

    /**
     * 更新版本
     * @param int $versionId 版本ID
     * @param int $userId 用户ID
     * @param array $data 更新数据
     * @return bool
     * @throws Exception
     */
    public function updateVersion(int $versionId, int $userId, array $data): bool
    {
        $version = Version::field('v.*, a.user_id')
            ->alias('v')
            ->join('app a', 'v.app_id = a.id')
            ->where('v.id', $versionId)
            ->find();
        
        if (!$version || $version['user_id'] != $userId) {
            throw new Exception('版本不存在或无权限访问', 3003);
        }
        
        $updateData = [];
        if (isset($data['versionName'])) $updateData['version_name'] = $data['versionName'];
        if (isset($data['modifyContent'])) $updateData['modify_content'] = $data['modifyContent'];
        if (isset($data['updateStatus'])) $updateData['update_status'] = $data['updateStatus'];
        
        $updateData['update_time'] = time();
        
        return Version::update($updateData, ['id' => $versionId]);
    }

    /**
     * 删除版本
     * @param int $versionId 版本ID
     * @param int $userId 用户ID
     * @return bool
     * @throws Exception
     */
    public function deleteVersion(int $versionId, int $userId): bool
    {
        $version = Version::field('v.*, a.user_id')
            ->alias('v')
            ->join('app a', 'v.app_id = a.id')
            ->where('v.id', $versionId)
            ->find();
        
        if (!$version || $version['user_id'] != $userId) {
            throw new Exception('版本不存在或无权限访问', 3003);
        }
        
        return $version->delete();
    }

    /**
     * 获取最新版本
     * @param int $appId 应用ID
     * @param string $flavor 渠道
     * @return array|null
     */
    public function getLatestVersion(int $appId, string $flavor = ''): ?array
    {
        $query = Version::where('app_id', $appId)
            ->where('update_status', '<>', self::UPDATE_STATUS_DISABLED);
        
        if (!empty($flavor)) {
            $query->where(function($query) use ($flavor) {
                $query->where('flavor', '')
                    ->orWhere('flavor', $flavor);
            });
        } else {
            $query->where('flavor', '');
        }
        
        $version = $query->order('version_code', 'desc')
            ->find();
        
        if (!$version) {
            return null;
        }
        
        return [
            'id' => $version['id'],
            'versionCode' => $version['version_code'],
            'versionName' => $version['version_name'],
            'modifyContent' => $version['modify_content'],
            'updateStatus' => $version['update_status'],
            'apkSize' => $version['apk_size'],
            'apkMd5' => $version['apk_md5']
        ];
    }
}
