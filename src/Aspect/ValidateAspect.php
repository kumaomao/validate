<?php
namespace Kumaomao\Validate\Aspect;

use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Context;
use Kumaomao\Validate\Annotations\Validate;
use Kumaomao\Validate\Exception\ValidateException;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class ValidateAspect
 * @package Kumaomao\Validate\Aspect
 * @Aspect
 */
class ValidateAspect extends AbstractAspect
{
    // 要切入的注解，具体切入的还是使用了这些注解的类，仅可切入类注解和类方法注解
    public $annotations = [
        Validate::class,
    ];

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $request = ApplicationContext::getContainer()->get(ServerRequestInterface::class);
        $validate = '';
        // TODO: Implement process() method.
        foreach ($proceedingJoinPoint->getAnnotationMetadata()->method as $validateMethod){
            if($validateMethod instanceof Validate){
                if(!$validateMethod->validate){
                    throw new ValidateException(500,'validate 不能为空');
                }
                if(class_exists($validateMethod->validate)){
                    //实例化验证方法
                    $validate = new $validateMethod->validate;
                }else{
                    throw new ValidateException(500,$validateMethod->validate.'不存在');
                }

                if ($validateMethod->scene) {
                    $validate = $validate->scene($validateMethod->scene);
                }

                //获取提交表单数据
                $data = $request->all();
                if ($validate->batch($validateMethod->batch)->check($data) === false) {
                    //检测是否抛出异常
                    if($validateMethod->throws){
                        throw new ValidateException(406,$validate->getError());
                    }else{
                        //错误信息写入请求
                        Context::override(ServerRequestInterface::class, function (ServerRequestInterface $request) use ($validate) {
                            return $request->withAttribute('validate', $validate->getError());
                        });

                    }
                }

                //验证通过处理
                if(empty($validate->getError()) && $validateMethod->filter){
                    $rules = $validate->getSceneRule($validateMethod->scene);
                    $new_data = [];
                    foreach ($rules as $key => $value) {
                        if(strstr($key, '|')){
                            $key = explode('|',$key)[0];
                        }
                        if(isset( $data[$key])){
                            $new_data[$key] = $data[$key];
                        }
                    }
                    Context::override(ServerRequestInterface::class, function (ServerRequestInterface $request) use ($new_data) {
                        return $request->withParsedBody($new_data);
                    });
                }
            }
        }
        return $proceedingJoinPoint->process();
    }

    private function getNewArr($scene){
        // 处理场景验证字段
        $array  = [];
        foreach ($scene as $k => $val) {
            if (is_numeric($k)) {
                $array[$val] = 0;
            } else {
                $array[$k]    = 0;
            }
        }
        return $array;
    }

}