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

namespace BaksDev\Dashboard\Repository\DashboardCurrentEventByPeriod;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Doctrine\ORMQueryBuilder;
use BaksDev\Dashboard\Entity\Dashboard;
use BaksDev\Dashboard\Entity\Event\DashboardEvent;
use BaksDev\Dashboard\Entity\Event\Invariable\DashboardInvariable;
use BaksDev\Dashboard\Entity\Event\Payment\DashboardPayment;
use BaksDev\Dashboard\Entity\Event\Type\DashboardType;
use BaksDev\Dashboard\Entity\Event\User\DashboardUser;
use BaksDev\Payment\Type\Id\PaymentUid;
use BaksDev\Users\User\Type\Id\UserUid;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;


final class DashboardCurrentEventByPeriodRepository implements DashboardCurrentEventByPeriodInterface
{
    private PaymentUid $payment;
    private UserUid $user;
    private DateTimeImmutable $start;
    private DateTimeImmutable $finish;
    private string $type;

    public function __construct(private readonly ORMQueryBuilder $ORMQueryBuilder) {}

    public function payment(PaymentUid $payment): self
    {
        $this->payment = $payment;

        return $this;
    }

    public function user(UserUid $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function period(DateTimeImmutable $from, DateTimeImmutable $to): self
    {
        $this->start = $from->setTime(0, 0, 0);
        $this->finish = $to->setTime(0, 0, 0);

        return $this;
    }

    public function type(string $type): self
    {
        $this->type = $type;
        return $this;
    }


    public function find(): DashboardEvent|null
    {
        $orm = $this->ORMQueryBuilder->createQueryBuilder(self::class);

        $orm->from(Dashboard::class, 'dashboard');

        $orm->join(
            DashboardPayment::class,
            'dashboard_payment',
            'WITH',
            '
                dashboard_payment.main = dashboard.id 
                AND dashboard_payment.value = :payment
             ')
            ->setParameter(
                key: 'payment',
                value: $this->payment,
                type: PaymentUid::TYPE,
            );

        $orm->join(
            DashboardUser::class,
            'dashboard_user',
            'WITH',
            '
                dashboard_user.main = dashboard.id 
                AND dashboard_payment.value = :usr
             ')
            ->setParameter(
                key: 'usr',
                value: $this->user,
                type: UserUid::TYPE,
            );

        $orm->join(
            DashboardType::class,
            'dashboard_type',
            'WITH',
            '
                dashboard_type.main = dashboard.id 
                AND dashboard_type.value = :type
             ')
            ->setParameter(
                key: 'type',
                value: $this->type,
                type: Types::STRING,
            );

        $orm->join(
            DashboardInvariable::class,
            'dashboard_invariable',
            'WITH',
            '
                dashboard_invariable.main = dashboard.id
                AND dashboard_invariable.start = :start
                AND dashboard_invariable.finish = :finish
            ')
            ->setParameter(
                key: 'start',
                value: $this->start,
                type: Types::DATETIME_IMMUTABLE,
            )
            ->setParameter(
                key: 'finish',
                value: $this->finish,
                type: Types::DATETIME_IMMUTABLE,
            );


        $orm
            ->select('dashboard_event')
            ->join(
                DashboardEvent::class,
                'dashboard_event',
                'WITH',
                'dashboard_event.id = dashboard.event',
            );

        return $orm->getOneOrNullResult();
    }
}