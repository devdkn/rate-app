<?php

declare(strict_types=1);

namespace App\Dto\Input;

use App\ValueObject\CurrencyValue;
use Symfony\Component\Validator\Constraints as Assert;

class RateHistoryDto
{
    /**
     * @Assert\NotIdenticalTo("")
     *
     * @var string
     */
    private $from;

    /**
     * @Assert\NotIdenticalTo("")
     *
     * @var string
     */
    private $to;

    /**
     * @Assert\Type("string")
     * @Assert\NotIdenticalTo("")
     * @Assert\DateTime(format="Y-m-d\TH:i:s.u\Z")
     *
     * @var mixed
     */
    private $since;

    /**
     * @Assert\Type("string")
     * @Assert\NotIdenticalTo("")
     * @Assert\DateTime(format="Y-m-d\TH:i:s.u\Z")
     *
     * @var mixed
     */
    private $until;

    /**
     * @param string $from
     * @param string $to
     * @param mixed  $since
     * @param mixed  $until
     */
    public function __construct(string $from, string $to, $since = null, $until = null)
    {
        $this->from = $from;
        $this->to = $to;
        $this->since = $since;
        $this->until = $until;
    }

    /**
     * @return CurrencyValue
     */
    public function getCurrencyFrom(): CurrencyValue
    {
        return new CurrencyValue($this->from);
    }

    /**
     * @return CurrencyValue
     */
    public function getCurrencyTo(): CurrencyValue
    {
        return new CurrencyValue($this->to);
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getSince(): ?\DateTimeImmutable
    {
        if ($this->since === null) {
            return null;
        }
        return new \DateTimeImmutable($this->since);
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getUntil(): ?\DateTimeImmutable
    {
        if ($this->until === null) {
            return null;
        }
        return new \DateTimeImmutable($this->until);
    }
}
