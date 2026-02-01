#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\Gift\Attribution\Domain\Port\Out\ResidentRepositoryInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    $kernel = new App\Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
    $kernel->boot();
    $container = $kernel->getContainer();

    $io = new SymfonyStyle(new ArgvInput(), new ConsoleOutput());

    /** @var ResidentRepositoryInterface $repository */
    $repository = $container->get(ResidentRepositoryInterface::class);

    $residents = $repository->findAll();

    $io->title('Test ValueObject Hydration');
    $io->section(sprintf('Found %d residents', count($residents)));

    foreach ($residents as $resident) {
        $io->writeln(sprintf(
            '- <info>%s %s</info> (age: %d, email: %s, id: %s)',
            $resident->getFirstName(),
            $resident->getLastName(),
            $resident->getAge()->value,  // Test Age ValueObject
            $resident->getEmail()->value,  // Test Email ValueObject
            $resident->getId()->value  // Test ResidentId ValueObject
        ));

        // Verify types
        $io->writeln(sprintf(
            '  Types: Age=%s, Email=%s, Id=%s',
            get_class($resident->getAge()),
            get_class($resident->getEmail()),
            get_class($resident->getId())
        ));
        $io->newLine();
    }

    $io->success('ValueObjects are correctly hydrated!');

    return 0;
};
