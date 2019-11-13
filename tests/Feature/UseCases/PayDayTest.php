<?php
declare(strict_types=1);

namespace Tests\Feature\UseCases;

use Carbon\CarbonImmutable;
use PayrollSystem\Application\UseCases\PayDay;
use PayrollSystem\Domain\Entities\Employee;
use PayrollSystem\Domain\Repositories\EmployeeRepositoryInterface;
use PayrollSystem\Domain\Repositories\TimeCardRepositoryInterface;
use PayrollSystem\Domain\ValueObjects\Address;
use PayrollSystem\Domain\ValueObjects\Date;
use PayrollSystem\Domain\ValueObjects\EmployeeId;
use PayrollSystem\Domain\ValueObjects\HourlyClassification;
use PayrollSystem\Domain\ValueObjects\Name;
use PayrollSystem\Domain\ValueObjects\SalariedClassification;
use Tests\BaseTestCase;
use PayrollSystem\Domain\Entities\TimeCard;

class PayDayTest extends BaseTestCase
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

        $timeCards = [
            new TimeCard($employeeId, new Date(CarbonImmutable::today()->subDays(1)->toDateString()), 5),
            new TimeCard($employeeId, new Date(CarbonImmutable::today()->subDays(2)->toDateString()), 5),
            new TimeCard($employeeId, new Date(CarbonImmutable::today()->subDays(3)->toDateString()), 5)
        ];
        $timeCardRepository = $this->createMock(TimeCardRepositoryInterface::class);
        $timeCardRepository->expects($this->once())
            ->method('findByEmployeeId')
            ->with($employeeId)
            ->willReturn($timeCards);

        $sut = new PayDay($employeeRepository, $timeCardRepository);

        // act
        $actual = $sut->pay(CarbonImmutable::today()->toDateString());

        // assert
        $this->assertSame(15000, $actual);
    }

    public function testPayToSalariedEmployee()
    {
        // arrange
        $employeeId = new EmployeeId('5.002.0186');
        $employeeName = new Name('name');
        $employeeAddress = new Address('address');
        $salariedEmployee = new Employee($employeeId, $employeeName, $employeeAddress, new SalariedClassification(300000));
        $employeeRepository = $this->createMock(EmployeeRepositoryInterface::class);
        $employeeRepository->expects($this->once())
            ->method('all')
            ->willReturn([$salariedEmployee]);

        $timeCardRepository = $this->createMock(TimeCardRepositoryInterface::class);

        $sut = new PayDay($employeeRepository, $timeCardRepository);

        // act
        $actual = $sut->pay(CarbonImmutable::today()->toDateString());

        // assert
        $this->assertSame(30000, $actual);
    }
}