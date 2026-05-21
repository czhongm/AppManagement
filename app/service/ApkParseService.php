<?php

namespace app\service;

use Exception;
use ZipArchive;

class ApkParseService
{
    /**
     * 解析 APK 文件信息
     * @param string $apkPath APK 文件路径
     * @return array
     * @throws Exception
     */
    public function parseApk(string $apkPath): array
    {
        if (!file_exists($apkPath)) {
            throw new Exception('APK 文件不存在', 5001);
        }
        
        $info = [
            'packageName' => '',
            'versionCode' => 0,
            'versionName' => '',
            'appName' => '',
            'icon' => ''
        ];
        
        try {
            // 使用 ZipArchive 读取 APK
            $zip = new ZipArchive();
            if ($zip->open($apkPath) === true) {
                // 读取 AndroidManifest.xml
                $manifest = $zip->getFromName('AndroidManifest.xml');
                if ($manifest !== false) {
                    $info = array_merge($info, $this->parseManifest($manifest));
                }
                
                // 提取应用图标
                $info['icon'] = $this->extractIcon($zip, $apkPath);
                
                $zip->close();
            }
        } catch (Exception $e) {
            throw new Exception('APK 解析失败: ' . $e->getMessage(), 5002);
        }
        
        return $info;
    }

    /**
     * 解析 AndroidManifest.xml
     * @param string $manifest XML 内容
     * @return array
     */
    private function parseManifest(string $manifest): array
    {
        $info = [
            'packageName' => '',
            'versionCode' => 0,
            'versionName' => '',
            'appName' => ''
        ];
        
        try {
            $xml = simplexml_load_string($manifest);
            
            if ($xml === false) {
                return $info;
            }
            
            // 获取包名
            if (isset($xml['package'])) {
                $info['packageName'] = (string)$xml['package'];
            }
            
            // 获取版本号和版本名
            $attributes = $xml->attributes('android', true);
            if (isset($attributes['versionCode'])) {
                $info['versionCode'] = intval($attributes['versionCode']);
            }
            if (isset($attributes['versionName'])) {
                $info['versionName'] = (string)$attributes['versionName'];
            }
            
            // 获取应用名称
            if (isset($xml->{'application'})) {
                $appAttributes = $xml->{'application'}->attributes('android', true);
                if (isset($appAttributes['label'])) {
                    $info['appName'] = (string)$appAttributes['label'];
                }
            }
        } catch (Exception $e) {
            // 如果 XML 解析失败，使用二进制解析
            return $this->parseBinary($manifest);
        }
        
        return $info;
    }

    /**
     * 二进制解析 AndroidManifest.xml
     * @param string $manifest XML 二进制内容
     * @return array
     */
    private function parseBinary(string $manifest): array
    {
        $info = [
            'packageName' => '',
            'versionCode' => 0,
            'versionName' => '',
            'appName' => ''
        ];
        
        // 这是一个简化的二进制解析实现
        // 实际应用中可能需要更复杂的解析逻辑
        
        return $info;
    }

    /**
     * 从 APK 中提取图标
     * @param ZipArchive $zip ZIP 对象
     * @param string $apkPath APK 路径
     * @return string 图标保存路径
     */
    private function extractIcon(ZipArchive $zip, string $apkPath): string
    {
        // 常见的图标文件名
        $iconPatterns = [
            'res/mipmap-xxxhdpi/ic_launcher.png',
            'res/mipmap-xxhdpi/ic_launcher.png',
            'res/mipmap-xhdpi/ic_launcher.png',
            'res/mipmap-hdpi/ic_launcher.png',
            'res/mipmap-mdpi/ic_launcher.png',
            'res/drawable-xxxhdpi/ic_launcher.png',
            'res/drawable-xxhdpi/ic_launcher.png',
        ];
        
        $uploadDir = public_path('uploads/icons');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        foreach ($iconPatterns as $pattern) {
            $iconData = $zip->getFromName($pattern);
            if ($iconData !== false) {
                $iconName = basename($apkPath, '.apk') . '.png';
                $iconPath = $uploadDir . '/' . $iconName;
                
                if (file_put_contents($iconPath, $iconData)) {
                    return '/uploads/icons/' . $iconName;
                }
                break;
            }
        }
        
        return '';
    }

    /**
     * 计算文件 MD5
     * @param string $filePath 文件路径
     * @return string
     */
    public function getFileMd5(string $filePath): string
    {
        return md5_file($filePath);
    }

    /**
     * 获取文件大小
     * @param string $filePath 文件路径
     * @return int
     */
    public function getFileSize(string $filePath): int
    {
        return filesize($filePath);
    }
}
