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
use BaksDev\Dashboard\Entity\Event\DashboardEvent;
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
class NewDashboardHandlerTest extends KernelTestCase
{
    public static function setUpBeforeClass(): void
    {
        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get(EntityManagerInterface::class);

        $main = $em->getRepository(Dashboard::class)
            ->findOneBy(['id' => DashboardUid::TEST]);

        if($main)
        {
            $em->remove($main);
        }


        $event = $em->getRepository(DashboardEvent::class)
            ->findBy(['main' => DashboardUid::TEST]);

        foreach($event as $remove)
        {
            $em->remove($remove);
        }

        $em->flush();
        $em->clear();
    }


    public function testUseCase(): void
    {
        /** @see DashboardDTO */
        $NewEditDashboardDTO = new NewEditDashboardDTO();
        $NewEditDashboardDTO->setTotal(new Money(-123.36));

        $DashboardInvariableDTO = $NewEditDashboardDTO->getInvariable();
        $DashboardInvariableDTO
            ->setName('Название')
            ->setPeriod(
                new DateTimeImmutable('now')->sub(DateInterval::createFromDateString('1 day')),
                new DateTimeImmutable('now')->sub(DateInterval::createFromDateString('1 day')),
            )
            ->setPriority(100);

        $DashboardPaymentDTO = $NewEditDashboardDTO->getPayment();
        $DashboardPaymentDTO->setValue(new PaymentUid(PaymentUid::TEST));

        $DashboardUserDTO = $NewEditDashboardDTO->getUser();
        $DashboardUserDTO->setValue(new UserUid(UserUid::TEST));

        $NewEditDashboardTypeDTO = $NewEditDashboardDTO->getType();
        $NewEditDashboardTypeDTO->setValue('day');

        /** @var NewEditDashboardHandler $NewEditDashboardHandler */
        $NewEditDashboardHandler = self::getContainer()->get(NewEditDashboardHandler::class);
        $handle = $NewEditDashboardHandler->handle($NewEditDashboardDTO);

        self::assertTrue(($handle instanceof Dashboard), $handle.': Ошибка Dashboard');
    }


    public function testComplete(): void
    {
        /** @var DBALQueryBuilder $dbal */
        $dbal = self::getContainer()->get(DBALQueryBuilder::class);

        $dbal->createQueryBuilder(self::class);

        $dbal->from(Dashboard::class)
            ->where('id = :id')
            ->setParameter('id', DashboardUid::TEST);

        self::assertTrue($dbal->fetchExist());
    }
}