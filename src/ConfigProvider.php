<?php


namespace Kumaomao\Validate;


use Kumaomao\Validate\Validate\ValidatorFactory;
use Kumaomao\Validate\Validate\ValidatorFactoryInterface;

class ConfigProvider
{
    public function __invoke(): array
    {
        $languagesPath = BASE_PATH . '/storage/languages';
        $translationConfigFile = BASE_PATH . '/config/autoload/translation.php';
        if (file_exists($translationConfigFile)) {
            $translationConfig = include $translationConfigFile;
            $languagesPath = $translationConfig['path'] ?? $languagesPath;
        }

        return [
            // 合并到  config/autoload/dependencies.php 文件
            'dependencies' => [
            ],
            // 合并到  config/autoload/annotations.php 文件
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            // 默认 Command 的定义，合并到 Hyperf\Contract\ConfigInterface 内，换个方式理解也就是与 config/autoload/commands.php 对应
            'commands' => [],
            'exceptions' => [
                'handler' => [
                    'http' => [
                        \Kumaomao\Validate\Exception\ValidateException::class,
                    ],
                ],
            ],
            // 与 commands 类似
            'listeners' => [],
            // 组件默认配置文件，即执行命令后会把 source 的对应的文件复制为 destination 对应的的文件
            'publish' => [
                [
                    'id' => 'en',
                    'description' => '验证器英语语言包', // 描述
                    // 建议默认配置放在 publish 文件夹中，文件命名和组件名称相同
                    'source' => __DIR__ . '/../publish/en/validation.php',  // 对应的配置文件路径
                    'destination' => $languagesPath . '/en/validation.php', // 复制为这个路径下的该文件
                ],
                [
                    'id' => 'zh_CN',
                    'description' => '验证器中文语言包', // 描述
                    // 建议默认配置放在 publish 文件夹中，文件命名和组件名称相同
                    'source' => __DIR__ . '/../publish/zh_CN/validation.php',  // 对应的配置文件路径
                    'destination' => $languagesPath . '/zh_CN/validation.php', // 复制为这个路径下的该文件
                ],
            ],
            // 亦可继续定义其它配置，最终都会合并到与 ConfigInterface 对应的配置储存器中
        ];
    }
}