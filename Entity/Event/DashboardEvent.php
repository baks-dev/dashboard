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

namespace BaksDev\Dashboard\Entity\Event;

use BaksDev\Core\Entity\EntityEvent;
use BaksDev\Dashboard\Entity\Dashboard;
use BaksDev\Dashboard\Entity\Event\Invariable\DashboardInvariable;
use BaksDev\Dashboard\Entity\Event\Modify\DashboardModify;
use BaksDev\Dashboard\Entity\Event\Payment\DashboardPayment;
use BaksDev\Dashboard\Entity\Event\User\DashboardUser;
use BaksDev\Dashboard\Type\Event\DashboardEventUid;
use BaksDev\Dashboard\Type\Id\DashboardUid;
use BaksDev\Reference\Money\Type\Money;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;


/* DashboardEvent */

#[ORM\Entity]
#[ORM\Table(name: 'dashboard_event')]
class DashboardEvent extends EntityEvent
{
    /**
     * Идентификатор События
     */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Id]
    #[ORM\Column(type: DashboardEventUid::TYPE)]
    private DashboardEventUid $id;

    /** Идентификатор Dashboard */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Column(type: DashboardUid::TYPE, nullable: false)]
    private ?DashboardUid $main = null;

    /** DashboardInvariable */
    #[ORM\OneToOne(targetEntity: DashboardInvariable::class, mappedBy: 'event', cascade: ['all'])]
    private ?DashboardInvariable $invariable = null;

    /** DashboardPayment */
    #[ORM\OneToOne(targetEntity: DashboardPayment::class, mappedBy: 'event', cascade: ['all'])]
    private ?DashboardPayment $payment = null;

    /** DashboardUser */
    #[ORM\OneToOne(targetEntity: DashboardUser::class, mappedBy: 'event', cascade: ['all'])]
    private ?DashboardUser $user = null;

    /** Стоимость */
    #[Assert\NotBlank]
    #[ORM\Column(type: Money::TYPE)]
    private Money $total;

    /**
     * Модификатор
     */
    #[ORM\OneToOne(targetEntity: DashboardModify::class, mappedBy: 'event', cascade: ['all'])]
    private DashboardModify $modify;

    public function __construct()
    {
        $this->id = new DashboardEventUid();
        $this->modify = new DashboardModify($this);
    }

    /**
     * Идентификатор События
     */

    public function __clone()
    {
        $this->id = clone new DashboardEventUid();
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }

    public function getMain(): ?DashboardUid
    {
        return $this->main;
    }

    /**
     * Идентификатор Dashboard
     */
    public function setMain(DashboardUid|Dashboard $main): void
    {
        $this->main = $main instanceof Dashboard ? $main->getId() : $main;
    }

    public function getId(): DashboardEventUid
    {
        return $this->id;
    }

    public function getDto($dto): mixed
    {
        $dto = is_string($dto) && class_exists($dto) ? new $dto() : $dto;

        if($dto instanceof DashboardEventInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function setEntity($dto): mixed
    {
        if($dto instanceof DashboardEventInterface)
        {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

}