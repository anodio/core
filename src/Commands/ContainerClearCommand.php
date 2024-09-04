<?php

namespace Anodio\Core\Commands;



use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[\Anodio\Core\Attributes\Command('container:clear', description: 'Clear compiled containers')]
class ContainerClearCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        shell_exec('rm -rf ' . BASE_PATH.'/system/cnt_*');
        shell_exec('rm -rf ' . BASE_PATH.'/system/hashes/*');
        return 0;
    }
}
