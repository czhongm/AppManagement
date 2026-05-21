<?php

namespace app\service;

use app\model\Account;
use think\facade\Db;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class AccountService
{
    /**
     * 用户登录
     * @param string $loginName 登录名
     * @param string $password 密码
     * @return array
     * @throws Exception
     */
    public function login(string $loginName, string $password): array
    {
        $account = Account::where('login_name', $loginName)->find();
        
        if (!$account) {
            throw new Exception('用户不存在', 1001);
        }
        
        if (!password_verify($password, $account['password'])) {
            throw new Exception('密码错误', 1002);
        }
        
        // 生成 JWT token
        $token = $this->generateToken($account);
        
        // 更新最后登录时间
        $account->last_login_time = time();
        $account->save();
        
        return [
            'token' => $token,
            'user' => [
                'id' => $account['id'],
                'loginName' => $account['login_name'],
                'nick' => $account['nick'],
                'authority' => $account['authority'],
                'avatar' => $account['avatar'] ?? ''
            ]
        ];
    }

    /**
     * 用户注册
     * @param string $loginName 登录名
     * @param string $password 密码
     * @param string $nick 昵称
     * @param string $phone 手机号
     * @param string $address 地址
     * @return array
     * @throws Exception
     */
    public function register(string $loginName, string $password, string $nick, string $phone = '', string $address = ''): array
    {
        // 检查登录名是否已存在
        $exists = Account::where('login_name', $loginName)->find();
        if ($exists) {
            throw new Exception('用户名已存在', 1003);
        }
        
        $account = new Account();
        $account->login_name = $loginName;
        $account->password = password_hash($password, PASSWORD_BCRYPT);
        $account->nick = $nick;
        $account->phone = $phone;
        $account->address = $address;
        $account->authority = 'editor'; // 默认普通用户
        $account->register_time = time();
        $account->create_time = time();
        
        if ($account->save()) {
            return ['id' => $account->id, 'loginName' => $loginName, 'nick' => $nick];
        }
        
        throw new Exception('注册失败', 1004);
    }

    /**
     * 获取用户信息
     * @param int $userId 用户ID
     * @return array
     * @throws Exception
     */
    public function getInfo(int $userId): array
    {
        $account = Account::find($userId);
        
        if (!$account) {
            throw new Exception('用户不存在', 1001);
        }
        
        return [
            'id' => $account['id'],
            'loginName' => $account['login_name'],
            'nick' => $account['nick'],
            'authority' => $account['authority'],
            'avatar' => $account['avatar'] ?? '',
            'phone' => $account['phone'],
            'address' => $account['address'],
            'registerTime' => $account['register_time']
        ];
    }

    /**
     * 分页查询用户
     * @param int $pageNum 页码
     * @param int $pageSize 每页数量
     * @return array
     */
    public function getPagingAccounts(int $pageNum = 1, int $pageSize = 10): array
    {
        $total = Account::count();
        $list = Account::page($pageNum, $pageSize)
            ->order('id', 'desc')
            ->select()
            ->toArray();
        
        $data = array_map(function($item) {
            return [
                'id' => $item['id'],
                'loginName' => $item['login_name'],
                'nick' => $item['nick'],
                'authority' => $item['authority'],
                'phone' => $item['phone'],
                'address' => $item['address'],
                'registerTime' => $item['register_time'],
                'lastLoginTime' => $item['last_login_time'] ?? 0
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
     * 更新用户信息
     * @param int $userId 用户ID
     * @param array $data 更新数据
     * @return bool
     * @throws Exception
     */
    public function updateInfo(int $userId, array $data): bool
    {
        $account = Account::find($userId);
        
        if (!$account) {
            throw new Exception('用户不存在', 1001);
        }
        
        $updateData = [];
        if (isset($data['nick'])) $updateData['nick'] = $data['nick'];
        if (isset($data['phone'])) $updateData['phone'] = $data['phone'];
        if (isset($data['address'])) $updateData['address'] = $data['address'];
        if (isset($data['authority'])) $updateData['authority'] = $data['authority'];
        if (isset($data['avatar'])) $updateData['avatar'] = $data['avatar'];
        
        $updateData['update_time'] = time();
        
        return Account::update($updateData, ['id' => $userId]);
    }

    /**
     * 删除用户
     * @param int $userId 用户ID
     * @return bool
     * @throws Exception
     */
    public function delete(int $userId): bool
    {
        $account = Account::find($userId);
        
        if (!$account) {
            throw new Exception('用户不存在', 1001);
        }
        
        return $account->delete();
    }

    /**
     * 生成 JWT token
     * @param mixed $account 账户对象
     * @return string
     */
    private function generateToken($account): string
    {
        $secret = env('JWT_SECRET', 'your_jwt_secret_key');
        $expire = intval(env('JWT_EXPIRE', 604800));
        
        $issuedAt = time();
        $expire = $issuedAt + $expire;
        
        $payload = [
            'iat' => $issuedAt,
            'exp' => $expire,
            'userId' => $account['id'],
            'loginName' => $account['login_name'],
            'authority' => $account['authority']
        ];
        
        return JWT::encode($payload, $secret, 'HS256');
    }
}
