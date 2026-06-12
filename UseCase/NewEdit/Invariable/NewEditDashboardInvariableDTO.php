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

namespace BaksDev\Dashboard\UseCase\NewEdit\Invariable;

use BaksDev\Dashboard\Entity\Event\Invariable\DashboardInvariableInterface;
use BaksDev\Payment\Type\Id\PaymentUid;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;

/** @see DashboardInvariable */
final class NewEditDashboardInvariableDTO implements DashboardInvariableInterface
{

    /** Название */
    #[Assert\NotBlank]
    private string $name;

    /** Период */
    #[Assert\NotBlank]
    private DateTimeImmutable $start;

    /** Период */
    #[Assert\NotBlank]
    private DateTimeImmutable $finish;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getStart(): DateTimeImmutable
    {
        return $this->start;
    }

    public function getFinish(): DateTimeImmutable
    {
        return $this->finish;
    }

    public function setPeriod(DateTimeImmutable $from, DateTimeImmutable $to): self
    {
        $this->start = $from->setTime(0, 0, 0);
        $this->finish = $to->setTime(0, 0, 0);
        return $this;
    }
}