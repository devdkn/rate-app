<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\RateUpdateService;
use App\ValueObject\CurrencyValue;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RateUpdateCommand extends Command
{
    private const ARG_CURRENCY = 'currency';

    /**
     * {@inheritdoc}
     */
    protected static $defaultName = 'rate-app:update-rates';

    /**
     * @var RateUpdateService
     */
    private $rateUpdateService;

    /**
     * @param RateUpdateService $rateUpdateService
     * @param null              $name
     */
    public function __construct(RateUpdateService $rateUpdateService, $name = null)
    {
        parent::__construct($name);
        $this->rateUpdateService = $rateUpdateService;
    }

    /**
     * @return void
     */
    protected function configure()
    {
        parent::configure();
        $this->addArgument(
            self::ARG_CURRENCY,
            InputArgument::REQUIRED,
            'The currency to update rates for'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $currency = $this->getCurrency($input);
        $this->rateUpdateService->updateRates($currency, $this->getDateTime());
        $output->writeln($currency->getValue() . ' rate update complete');
        return 0;
    }

    /**
     * @param InputInterface $input
     *
     * @return CurrencyValue
     */
    private function getCurrency(InputInterface $input): CurrencyValue
    {
        $val = $input->getArgument(self::ARG_CURRENCY);
        if (!\is_string($val)) {
            throw new \LogicException(
                'Unexpected value from the ' . InputInterface::class . '::getArgument(): expected string, got '
                . gettype($val)
            );
        }
        return new CurrencyValue($val);
    }

    /**
     * @return \DateTimeImmutable
     */
    private function getDateTime(): \DateTimeImmutable
    {
        return new \DateTimeImmutable();
    }
}
