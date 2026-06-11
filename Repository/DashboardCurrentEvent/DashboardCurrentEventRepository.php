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

namespace BaksDev\Dashboard\Repository\DashboardCurrentEvent;

use BaksDev\Core\Doctrine\ORMQueryBuilder;
use BaksDev\Dashboard\Entity\Dashboard;
use BaksDev\Dashboard\Entity\Event\DashboardEvent;
use BaksDev\Dashboard\Type\Id\DashboardUid;
use InvalidArgumentException;


final class DashboardCurrentEventRepository implements DashboardCurrentEventInterface
{

    private DashboardUid|false $dashboard = false;

    public function __construct(private readonly ORMQueryBuilder $ORMQueryBuilder) {}

    public function forDashboardMain(DashboardUid $dashboard): self
    {
        $this->dashboard = $dashboard;

        return $this;
    }

    /** Метод возвращает текущее событие */
    public function find(): ?DashboardEvent
    {
        if(false === ($this->dashboard instanceof DashboardUid))
        {
            throw new InvalidArgumentException('Invalid Argument DashboardUid');
        }

        $orm = $this->ORMQueryBuilder->createQueryBuilder(self::class);

        $orm
            ->from(Dashboard::class, 'dashboard')
            ->where('dashboard.id = :dashboard')
            ->setParameter(
                key: 'dashboard',
                value: $this->dashboard,
                type: DashboardUid::TYPE,
            );

        $orm
            ->select('dashboard_event')
            ->join(
                DashboardEvent::class,
                'dashboard_event',
                'WITH',
                'dashboard_event.id = dashboard.event',
            );

        return $orm->getQuery()->getOneOrNullResult();
    }
}