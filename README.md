# php-methodcache
without change any existing code, use method cache accelerate your  application.

方法缓存 不修改原业务代码，为原function增加缓存，用于减少DB查询或复杂方法的耗时
使用：对象握有一个MethodCache类（定义一个属性或者setupMethodCache），然后就可以直接使用
实例：
原方法 $taskInfoList = $taskInfoModel->getTaskInfoByIds($task_ids_arr);
使用方法缓存 $taskInfoList = $taskInfoModel->setupMethodCache()->getTaskInfoByIds($task_ids_arr,120);
