
 **快速引入** 

```
composer require kumaomao/validate
```


 **hyperf验证器 移植于ThinkPHP验证器** 

普通使用方法和tp一样

 **同时本组件也支持hyperf的注解** 

@Validate()

参数

validate 验证器 例：validate=AdminValidate::class

scene 场景

batch 是否批量验证 默认false

throws 是否主动抛出错误 默认true,当值为false不会主动抛出错误，可通过获取$this->request->getAttribute('validate')的值来手动抛出错误，该值为null时表示通过验证

filter 是否过滤多余字段 默认true 只获取验证的字段 通过$this->request->getParsedBody()获取

例:
发布验证器语言文件
```
@validate(validate=AdminValidate::class,scene="login",throws=false)
```

2020/11/26 
新增对hyperf国际化组件支持
发布验证器语言文件
```
 php bin/hyperf.php vendor:publish kumaomao/validate
```
默认发布后语言包地址 

```
/storage
    /languages
        /en
            validation.php
        /zh_CN
            validation.php
```


