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
use BaksDev\Core\Entity\EntityState;
use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Core\Type\Modify\ModifyAction;
use BaksDev\Dashboard\Entity\Dashboard;
use BaksDev\Dashboard\Entity\Event\Modify\DashboardModify;
use BaksDev\Dashboard\Type\Event\DashboardEventUid;
use BaksDev\Dashboard\Type\Id\DashboardUid;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
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

    /**
     * Идентификатор Dashboard
     */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Column(type: DashboardUid::TYPE, nullable: false)]
    private ?DashboardUid $main = null;

    /** One To One */
    //#[ORM\OneToOne(targetEntity: DashboardLogo::class, mappedBy: 'event', cascade: ['all'])]
    //private ?DashboardOne $one = null;

    /**
     * Модификатор
     */
    #[ORM\OneToOne(targetEntity: DashboardModify::class, mappedBy: 'event', cascade: ['all'])]
    private DashboardModify $modify;

    /**
     * Переводы
     */
    //#[ORM\OneToMany(targetEntity: DashboardTrans::class, mappedBy: 'event', cascade: ['all'])]
    //private Collection $translate;


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