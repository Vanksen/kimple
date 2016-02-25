<?php

namespace Vanksen\Kimple;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

/**
 * Class KimpleCommand
 * @package Vanksen\Kimple
 */
class KimpleCommand extends Command
{
    /**
     * Declares custom command for the Kimple framework.
     */
    protected function configure()
    {
        $this
            ->setName('create:app')
            ->setDescription('Creates a new app.')
            ->addArgument(
                'url',
                InputArgument::REQUIRED,
                'URL of the app without the www. Eg. domain-name.com'
            );
    }

    /**
     * Definition of the custom command
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Retrieves the project's url
        $url = $input->getArgument('url');

        // Initialises the output
        $output->writeln('<info>Crafting the app...</info>');

        // Tries to mirror the app's folder.
        try {
            $fs = new Filesystem();
            $fs->mirror('vendor/vanksen/kimple/default_app/', 'apps/' . $url);
        } catch (IOExceptionInterface $e) {
            $output->writeln('<error>Error copying files.</error>');
        }

        // Everything went smoothly, so reassures the user
        $output->writeln('<info>The app is ready!</info>');
        $output->writeln('<comment>Configure the app in "apps/' . $url . '/settings.yml" file');
        $output->writeln('Don\'t forget the translations of the app! Edit the "apps/' . $url . '/settings.yml" file in order to set them.</comment>');


    }
}