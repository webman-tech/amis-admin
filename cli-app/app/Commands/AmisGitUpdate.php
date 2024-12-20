<?php

namespace App\Commands;

use App\Constants;
use Illuminate\Filesystem\Filesystem;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Process\Process;

class AmisGitUpdate extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'amis:git-update';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = '更新 amis 的 git 仓库';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $files = new Filesystem();

        $gitPath = base_path(Constants::AMIS_GIT_PATH);
        $files->ensureDirectoryExists($gitPath);

        if (!$files->exists($gitPath . '/.git')) {
            $this->info('clone...');
            $amisGitUrl = Constants::AMIS_GIT_URL;
            $cmd = "git clone  --progress {$amisGitUrl} .";
            $process = Process::fromShellCommandline($cmd, $gitPath);
        } else {
            $this->info('pull...');
            $cmd = 'git pull --progress';
            $process = Process::fromShellCommandline($cmd, $gitPath);
        }
        $process->setTimeout(86400);
        $process->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        $this->info('done');
        return self::SUCCESS;
    }
}
