> TIPS

[YAF帮助文档](http://www.laruence.com/manual/index.html)

> 环境配置
    本地：php.ini添加：
    [yaf]
    yaf.environ = develop
    测试环境的php.ini添加：
    [yaf]
    yaf.environ = test


项目目录结构参考于：（请认真阅读）

[php yaf框架开发扩展实践](http://www.01happy.com/php-yaf-ext-preface/)

不同的地方有：
> 业务层的目录名改为了Service，模型层是按数据库为粒度划分的。

数据库连接使用的是：Laravel的Eloquent,相关的文档见：

https://docs.golaravel.com/docs/5.0/eloquent/
https://laravel.com/docs/5.5/database

> 目录结构说明

/application/controllers：

    MVC的C层（Controller-负责请求转发，对请求进行处理，主要作用：
    1.起到一个路由的作用
    2.起到报文信息格式转化的作用）
    
/application/models/Service：    

    业务逻辑处理层，这里采用是一个业务一个文件方式，虽然随着业务的增长，
    文件会越来越多，但这种方式便于后期扩展，能有效的减少多人并行开发时的冲突率。
    
/application/models/Dao：

    DAO全称就是Data Access Object，通俗的讲就是数据访问对象。该层提供统一对外的数据访问接口，具体是要调用数据库、http、redis的数据由DAO决定处理    
    
> 命名规范建议
    
    PHP
        类的方法名和属性名采用驼峰命名方式，类中的变量采用驼峰命名方式，名字最好是见名知义（不要在乎长短）
        类中的private，protected方法和属性前面加_(下划线)
    JavaScript
        函数名和变量名均采用均采用驼峰命名方式    
      
> 使用建议
    
    最好使用getQuery(),getPost()等来替代$_GET，$_POST等这些全局变量    
    
> js和css文件命名方式建议

    采用controller.action.js,controller.action.css，有些共用css也可以集中写到一个文件里面。
