<?php
declare(strict_types=1);

namespace Tests\Feature\UseCases;

use Carbon\CarbonImmutable;
use PayrollSystem\Domain\Entities\Employee;
use PayrollSystem\Domain\Repositories\EmployeeRepositoryInterface;
use PayrollSystem\Domain\Repositories\TimeCardRepositoryInterface;
use PayrollSystem\Domain\ValueObjects\Address;
use PayrollSystem\Domain\ValueObjects\Date;
use PayrollSystem\Domain\ValueObjects\EmployeeId;
use PayrollSystem\Domain\ValueObjects\HourlyClassification;
use PayrollSystem\Domain\ValueObjects\Name;
use Tests\BaseTestCase;
use PayrollSystem\Domain\Entities\TimeCard;
use PayrollSystem\Application\UseCases\PunchTimeCard;

class PaySalaryTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        CarbonImmutable::setTestNow(CarbonImmutable::now());
    }

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();

        parent::tearDown();
    }

    public function testPayToHourlyClassification()
    {
        // arrange
        $employeeId = new EmployeeId('5.002.0186');
        $employeeName = new Name('name');
        $employeeAddress = new Address('address');
        $hourlyEmployee = new Employee($employeeId, $employeeName, $employeeAddress, new HourlyClassification(1000));
        $employeeRepository = $this->createMock(EmployeeRepositoryInterface::class);
        $employeeRepository->expects($this->once())
            ->method('all')
            ->willReturn([$hourlyEmployee]);

        $timeCards = [];
        $timeCards[] = new TimeCard($employeeId, CarbonImmutable::tody()->sub()->toDateString(), 5);
        $timeCards[] = new TimeCard($employeeId, CarbonImmutable::tody()->sub()->toDateString(), 5);
        $timeCards[] = new TimeCard($employeeId, CarbonImmutable::tody()->sub()->toDateString(), 5);
        $timeCardRepository = $this->createMock(TimeCardRepositoryInterface::class);
        $timeCardRepository->expects($this->once())
            ->method('findByEmployeeId')
            ->willReturn($timeCards);

        $sut = new PaySalary($employeeRepository, $timeCardRepository);

        // act
        $actual = $sut->pay(CarbonImmutable::today()->toDateString());

        // assert
        $this->assertSame(15000, $actual);
    }
}
