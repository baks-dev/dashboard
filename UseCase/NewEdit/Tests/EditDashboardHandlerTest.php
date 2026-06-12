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

namespace BaksDev\Dashboard\UseCase\NewEdit\Tests;


use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Dashboard\Entity\Dashboard;
use BaksDev\Dashboard\Repository\DashboardCurrentEvent\DashboardCurrentEventInterface;
use BaksDev\Dashboard\Type\Id\DashboardUid;
use BaksDev\Dashboard\UseCase\NewEdit\NewEditDashboardDTO;
use BaksDev\Dashboard\UseCase\NewEdit\NewEditDashboardHandler;
use BaksDev\Payment\Type\Id\PaymentUid;
use BaksDev\Reference\Money\Type\Money;
use BaksDev\Users\User\Type\Id\UserUid;
use DateInterval;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DependsOnClass;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

#[Group('dashboard')]
#[When(env: 'test')]
class EditDashboardHandlerTest extends KernelTestCase
{
    #[DependsOnClass(NewDashboardHandlerTest::class)]
    public function testUseCase(): void
    {
        /** @var DashboardCurrentEventInterface $DashboardCurrentEventRepository */
        $DashboardCurrentEventRepository = self::getContainer()->get(DashboardCurrentEventInterface::class);
        $DashboardEvent = $DashboardCurrentEventRepository
            ->forDashboardMain(new DashboardUid(DashboardUid::TEST))
            ->find();

        self::assertNotNull($DashboardEvent);

        /** @see DashboardDTO */
        $NewEditDashboardDTO = new NewEditDashboardDTO();

        //dd($DashboardEvent); /* TODO: удалить !!! */

        $DashboardEvent->getDto($NewEditDashboardDTO);


        self::assertTrue($NewEditDashboardDTO->getTotal()->equals(-123.36));


        $DashboardInvariableDTO = $NewEditDashboardDTO->getInvariable();
        self::assertEquals('Название', $DashboardInvariableDTO->getName());

        $tetsPeriod = new DateTimeImmutable('now')->sub(DateInterval::createFromDateString('1 day'));
        self::assertEquals($tetsPeriod->format('d.m.Y'), $DashboardInvariableDTO->getStart()->format('d.m.Y'));
        self::assertEquals($tetsPeriod->format('d.m.Y'), $DashboardInvariableDTO->getFinish()->format('d.m.Y'));


        $DashboardPaymentDTO = $NewEditDashboardDTO->getPayment();
        self::assertTrue($DashboardPaymentDTO->getValue()->equals(PaymentUid::TEST));

        $DashboardUserDTO = $NewEditDashboardDTO->getUser();
        self::assertTrue($DashboardUserDTO->getValue()->equals(UserUid::TEST));

        $NewEditDashboardTypeDTO = $NewEditDashboardDTO->getType();
        self::assertEquals('day', $NewEditDashboardTypeDTO->getValue());

        /** Обновляем */

        $NewEditDashboardDTO->setTotal(new Money(-123.36));

        $DashboardInvariableDTO = $NewEditDashboardDTO->getInvariable();
        $DashboardInvariableDTO
            ->setName('Новое название')
            ->setPeriod(
                new DateTimeImmutable('now'),
                new DateTimeImmutable('now'),
            );

        $NewEditDashboardTypeDTO->setValue('month');
        $DashboardPaymentDTO->setValue(clone new PaymentUid(PaymentUid::TEST));
        $DashboardUserDTO->setValue(clone new UserUid(UserUid::TEST));


        /** @var NewEditDashboardHandler $NewEditDashboardHandler */
        $NewEditDashboardHandler = self::getContainer()->get(NewEditDashboardHandler::class);
        $handle = $NewEditDashboardHandler->handle($NewEditDashboardDTO);

        self::assertTrue(($handle instanceof Dashboard), $handle.': Ошибка Dashboard');

    }
}