<?php

namespace Anodio\Core\Commands;

use Anodio\Core\Configuration\Env;
use olvlvl\ComposerAttributeCollector\Attributes;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[\Anodio\Core\Attributes\Command('env:print-all', description: 'Print all possible envs')]
class PrintAllEnvsCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $printed = [];
        $targets = Attributes::findTargetProperties(Env::class);
        foreach ($targets as $target) {
            if (!$target->attribute instanceof Env) {
                continue;
            }
            if (in_array($target->attribute->name, $printed)) {
                continue;
            }
            $this->printEnv($output, $target);
            $printed[] = $target->attribute->name;
        }

        return 0;
    }

    private function printEnv(OutputInterface $output, \olvlvl\ComposerAttributeCollector\TargetProperty $target)
    {
        if ($target->attribute->comment) {
            $output->writeln('# '.$target->attribute->comment);
        }
        if ($target->attribute->default) {
            if (str_contains($target->attribute->default, ' ')) {
                $output->writeln($target->attribute->name.'="'.$target->attribute->default.'"');
            } else {
                $output->writeln($target->attribute->name.'='.$target->attribute->default);
            }
        } else {
            $output->writeln($target->attribute->name.'=');
        }
    }
}
