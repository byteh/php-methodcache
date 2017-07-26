<?php
/**
 * 这个是使用 方法缓存 MethodCache 的类
 * User: hank
 * Date: 17/3/8
 * Time: 15:48
 */
class BaseMysqlModel
{
    public $methodCache = null;  //方法缓存对象


    public function __construct()
    {
    }

    /**
     * 返回 方法缓存对象，使用参考 class Helper_MethodCache
     * @param int $cacheExpireTime
     * @return Helper_MethodCache
     */
    public function setupMethodCache($cacheExpireTime = 0)
    {
        if(is_null($this->methodCache))
        {
            $this->methodCache = new Helper_MethodCache($this, $cacheExpireTime);
        }
        return $this->methodCache;
    }
}
