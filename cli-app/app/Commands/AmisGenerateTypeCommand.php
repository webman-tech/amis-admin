<?php

namespace App\Commands;

use App\Constants;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Finder\Finder;
use WebmanTech\AmisAdmin\Amis\ComponentMaker;

class AmisGenerateTypeCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'amis:generate-type {--update-git : 是否更新 git 仓库}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = '生产 amis 的 type';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        require_once base_path(Constants::COMPONENT_MAKER_CLASS_FILE);

        if ($this->option('update-git')) {
            $this->info('update git...');
            $this->call('amis:git-update');
        }

        $componentTypes = $this->findMdDocTypes(base_path(Constants::AMIS_GIT_PATH . '/docs/zh-CN/components'), [
            'index',
            'radios',
        ]);
        $formTypes = $this->findMdDocTypes(base_path(Constants::AMIS_GIT_PATH . '/docs/zh-CN/components/form'), [
            'formitem',
        ], [
            'index' => 'form',
            'nestedselect' => 'nested-select',
            'treeselect' => 'tree-select',
        ]);
        $componentTypes['form'] = $formTypes['index'];
        unset($formTypes['index']);
        ksort($componentTypes);
        ksort($formTypes);

        // formMethods
        $formMethods = array_column($formTypes, 'method');
        $this->writeFormMethods($formMethods);

        // comment
        $comments = [
            '/**',
            ' * 以下方法通过 cli-app 的 amis:generate-type 生成',
            ' *',
            ' * amis 组件',
            ' * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/index',
            ' *',
        ];
        foreach ($componentTypes as $name => $config) {
            $link = "https://aisuda.bce.baidu.com/amis/zh-CN/components/{$name}";
            if ($name === 'form') {
                $link .= '/index';
            }
            $comments[] = $this->getMethodComment($config['method'], $config['title'], $link);
        }

        $comments[] = ' *';
        $comments[] = ' * amis 表单';
        foreach ($formTypes as $name => $config) {
            $comments[] = $this->getMethodComment($config['method'], $config['title'], "https://aisuda.bce.baidu.com/amis/zh-CN/components/form/{$name}");
        }

        $comments[] = ' *';
        $comments[] = ' * 自定义';
        foreach (array_keys(ComponentMaker::getExtComponents()) as $name) {
            $comments[] = $this->getMethodComment($name, '', '');
        }

        $comments[] = ' */';
        $this->writeComments($comments);

        return self::SUCCESS;
    }

    private function findMdDocTypes(string $path, array $skipTypes = [], array $nameTypeMap = []): array
    {
        $types = [];
        foreach (Finder::create()->depth(0)->in($path)->name('*.md') as $file) {
            $name = $file->getBasename('.md');
            if (in_array($name, $skipTypes, true)) {
                continue;
            }
            $content = $file->getContents();
            preg_match('/^---\ntitle: (.*?)\n/s', $content, $matches);
            $title = $matches[1] ?? '';
            $type = $nameTypeMap[$name] ?? $name;
            $types[$name] = [
                'title' => $title,
                'type' => $type,
                'method' => $this->getMethodNameByType($type),
            ];
        }
        return $types;
    }

    private function getMethodNameByType(string $type): string
    {
        return 'type' . str_replace('-', '', ucwords($type, '-'));
    }

    private function getMethodComment(string $name, string $title, string $link): string
    {
        $responseClass = '\\' . ComponentMaker::getDefaultClassByName($name);
        return " * @method static {$responseClass} {$name}(array \$schema = []) {$title}    {$link}";
    }

    private function writeFormMethods(array $formMethods): void
    {
        $filename = base_path(Constants::FORM_TYPES_FILE);
        $formMethods = var_export($formMethods, true);
        $content = <<<PHP
<?php
/**
 * 该文件由 cli-app 的 amis:generate-type 生成
 */

return $formMethods;

PHP;
        file_put_contents($filename, $content);
        $this->info("write {$filename} done");
    }

    private function writeComments(array $comments): void
    {
        $filename = base_path(Constants::TYPE_COMPONENT_FILE);
        $content = file_get_contents($filename);
        $comments = implode("\n", $comments);
        $content = preg_replace('/\/\*\*.*?\*\//s', $comments, $content, 1);
        file_put_contents($filename, $content);
        $this->info("replace {$filename} comment done");
    }
}
