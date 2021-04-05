<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends \RuntimeException
{
    /**
     * @var ConstraintViolationListInterface
     *
     * @phpstan-var ConstraintViolationListInterface<int, ConstraintViolationInterface>
     */
    private $violations;

    /**
     * @phpstan-param ConstraintViolationListInterface<int, ConstraintViolationInterface> $violations
     */
    public function __construct(ConstraintViolationListInterface $violations, string $message = '')
    {
        parent::__construct($message, 0, null);
        $this->violations = $violations;
    }

    /**
     * @return ConstraintViolationListInterface
     *
     * @phpstan-return ConstraintViolationListInterface<int, ConstraintViolationInterface>
     */
    public function getViolations(): ConstraintViolationListInterface
    {
        return $this->violations;
    }
}
