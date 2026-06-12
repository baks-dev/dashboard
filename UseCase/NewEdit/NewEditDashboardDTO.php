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

namespace BaksDev\Dashboard\UseCase\NewEdit;

use BaksDev\Dashboard\Entity\Event\DashboardEventInterface;
use BaksDev\Dashboard\Type\Event\DashboardEventUid;
use BaksDev\Dashboard\UseCase\NewEdit\Invariable\NewEditDashboardInvariableDTO;
use BaksDev\Dashboard\UseCase\NewEdit\Payment\NewEditDashboardPaymentDTO;
use BaksDev\Dashboard\UseCase\NewEdit\User\NewEditDashboardUserDTO;
use BaksDev\Reference\Money\Type\Money;
use Symfony\Component\Validator\Constraints as Assert;

/** @see DashboardEvent */
final class NewEditDashboardDTO implements DashboardEventInterface
{
    /**
     * Идентификатор события
     */
    #[Assert\Uuid]
    private ?DashboardEventUid $id = null;

    /** DashboardInvariable */
    #[Assert\Valid]
    private NewEditDashboardInvariableDTO $invariable;


    /** DashboardPayment */
    private ?NewEditDashboardPaymentDTO $payment;


    /** DashboardUser */
    #[Assert\Valid]
    private NewEditDashboardUserDTO $user;

    /** Стоимость */
    #[Assert\NotBlank]
    private Money $total;

    public function __construct()
    {
        $this->invariable = new NewEditDashboardInvariableDTO();
        $this->payment = new NewEditDashboardPaymentDTO();
        $this->user = new NewEditDashboardUserDTO();
    }

    /**
     * Идентификатор события
     */
    public function getEvent(): ?DashboardEventUid
    {
        return $this->id;
    }

    public function getInvariable(): ?NewEditDashboardInvariableDTO
    {
        return $this->invariable;
    }

    public function getTotal(): Money
    {
        return $this->total;
    }

    public function setTotal(Money $total): self
    {
        $this->total = $total;
        return $this;
    }

    public function getPayment(): NewEditDashboardPaymentDTO
    {
        return $this->payment;
    }

    public function getUser(): NewEditDashboardUserDTO
    {
        return $this->user;
    }
}