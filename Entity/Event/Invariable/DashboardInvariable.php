<?php
/*
 *  Copyright 2026.  Baks.dev <admin@baks.dev>
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

declare(strict_types=1);

namespace BaksDev\Dashboard\Entity\Event\Invariable;

use BaksDev\Core\Entity\EntityReadonly;
use BaksDev\Dashboard\Entity\Event\DashboardEvent;
use BaksDev\Dashboard\Type\Id\DashboardUid;
use BaksDev\Payment\Type\Id\PaymentUid;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;


/* DashboardInvariable */

#[ORM\Entity]
#[ORM\Table(name: 'dashboard_invariable')]
#[ORM\Index(columns: ['start', 'finish', 'priority'])]
class DashboardInvariable extends EntityReadonly
{
    /**
     * Идентификатор Main
     */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Id]
    #[ORM\Column(type: DashboardUid::TYPE)]
    private DashboardUid $main;

    /**
     * Идентификатор События
     */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\OneToOne(targetEntity: DashboardEvent::class, inversedBy: 'invariable')]
    #[ORM\JoinColumn(name: 'event', referencedColumnName: 'id')]
    private DashboardEvent $event;

    /** Название */
    #[Assert\NotBlank]
    #[ORM\Column(type: Types::STRING)]
    private string $name;

    /** Приоритет (выше число - выше по списку) */
    #[Assert\NotBlank]
    #[ORM\Column(type: Types::INTEGER, options: ['default: 0'])]
    private int $priority = 0;


    /**
     * Период
     */

    #[Assert\NotBlank]
    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private DateTimeImmutable $start;

    #[Assert\NotBlank]
    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private DateTimeImmutable $finish;


    public function __construct(DashboardEvent $event)
    {
        $this->event = $event;
        $this->main = $event->getMain();
    }

    public function __toString(): string
    {
        return (string) $this->main;
    }


    public function getDto($dto): mixed
    {
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

        if($dto instanceof DashboardInvariableInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }


    public function setEntity($dto): mixed
    {
        if($dto instanceof DashboardInvariableInterface || $dto instanceof self)
        {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
}