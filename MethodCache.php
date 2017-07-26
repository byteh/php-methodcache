<?php

/**
 * Created by PhpStorm.
 * 方法缓存 用于减少DB查询或复杂方法的耗时
 * 使用：对象握有一个MethodCache类（定义一个属性或者setupMethodCache），然后就可以直接使用
 * 实例：
 * 原方法 $taskInfoList = $taskInfoModel->getTaskInfoByIds($task_ids_arr);
 * 使用方法缓存 $taskInfoList = $taskInfoModel->setupMethodCache()->getTaskInfoByIds($task_ids_arr);
 * User: hank.han
 * Date: 16/11/14
 * Time: 17:40
 */
class MethodCache
{
    const EXPIRE_TIME = 3600;  //默认1小时
    protected $cacheDrive;
    protected $callerObject; //调用者对象
    protected $cacheExpireTime;

    public function __construct($callerObject, $cacheDrive = null, $cacheExpireTime = 0)
    {
        if($cacheDrive)
        {
            $this->cacheDrive = $cacheDrive;
        }
        else
        {
            $this->cacheDrive = RedisClient::instance();
        }
        $this->callerObject = $callerObject;
        $this->cacheExpireTime = self::EXPIRE_TIME;
        if(($cacheExpireTime > 0))
        {
            $this->cacheExpireTime = $cacheExpireTime;
        }
    }

    public function changeExpireTime($cacheExpireTime)
    {
        $this->cacheExpireTime = $cacheExpireTime;
    }

    private function getmethodCacheKey($method, $arg)
    {
        $keyName = md5('methodCache'.get_class($this->callerObject).$method.md5(json_encode($arg)));
        return $keyName;
    }

    private function getResultFromCache($method,$arg)
    {
        $keyName = $this->getmethodCacheKey($method,$arg);
        if(false == $this->cacheDrive->connect())
        {
            return false;
        }
        $resultData = $this->cacheDrive->get($keyName);
        return unserialize($resultData);
    }

    private function setResultToCache($method, $arg, $result)
    {
        $keyName = $this->getmethodCacheKey($method,$arg);
        $this->cacheDrive->set($keyName,serialize($result), self::EXPIRE_TIME);
    }

    public function __call($method,$arg)
    {
        $useCache = true;  //控制是否使用缓存
        if(false == $useCache)
        {
            $result = call_user_func_array(array($this->callerObject, $method),$arg);
            return $result;
        }
        $result = $this->getResultFromCache($method,$arg);

        if($result && false == empty($result))
        {
            return $result;
        }
        else
        {
            $result = call_user_func_array(array($this->callerObject, $method),$arg);
        }
        if(false == empty($result))
        {
            $this->setResultToCache($method, $arg, $result);
        }
        return $result;
    }


}
