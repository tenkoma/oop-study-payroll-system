<?php
declare(strict_types=1);

namespace PayrollSystem\Application\UseCases;

use PayrollSystem\Domain\Entities\Employee;
use PayrollSystem\Domain\Exceptions\InvalidArgumentException;
use PayrollSystem\Domain\Repositories\EmployeeRepositoryInterface;
use PayrollSystem\Domain\ValueObjects\Address;
use PayrollSystem\Domain\ValueObjects\PayClassification\CommissionedClassification;
use PayrollSystem\Domain\ValueObjects\Identifier\EmployeeId;
use PayrollSystem\Domain\ValueObjects\PayClassification\HourlyClassification;
use PayrollSystem\Domain\ValueObjects\Name;
use PayrollSystem\Domain\ValueObjects\PayClassification\SalariedClassification;

class AddEmployee
{
    private $employeeRepository;

    public function __construct(EmployeeRepositoryInterface $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    /**
     * @param string $employeeId
     * @param string $name
     * @param string $address
     * @param int $hourlyRate
     * @return bool
     */
    public function addHourlyEmployee(string $employeeId, string $name, string $address, int $hourlyRate): bool
    {
        try {
            $employeeId = new EmployeeId($employeeId);
            $employeeName = new Name($name);
            $employeeAddress = new Address($address);
            $employee = new Employee($employeeId, $employeeName, $employeeAddress, new HourlyClassification($hourlyRate));
        } catch (\PayrollSystem\Domain\Exceptions\InvalidArgumentException $e) {
            throw new \PayrollSystem\Application\Exceptions\InvalidArgumentException('入力されたパラメータに誤りがあります');
        }

        $this->employeeRepository->add($employee);

        return true;
    }

    /**
     * @param string $employeeId
     * @param string $name
     * @param string $address
     * @param int $monthlySalary
     * @return bool
     */
    public function addSalaryEmployee(string $employeeId, string $name, string $address, int $monthlySalary): bool
    {
        try {
            $employeeId = new EmployeeId($employeeId);
            $employeeName = new Name($name);
            $employeeAddress = new Address($address);
            $employee = new Employee($employeeId, $employeeName, $employeeAddress, new SalariedClassification($monthlySalary));
        } catch (\PayrollSystem\Domain\Exceptions\InvalidArgumentException $e) {
            throw new \PayrollSystem\Application\Exceptions\InvalidArgumentException('入力されたパラメータに誤りがあります');
        }

        $this->employeeRepository->add($employee);

        return true;
    }

    public function addCommissionedEmployee(string $employeeId, string $name, string $address, int $salaryRate, int $commissionedRate)
    {
        try {
            $employeeId = new EmployeeId($employeeId);
            $employeeName = new Name($name);
            $employeeAddress = new Address($address);
            $employee = new Employee($employeeId, $employeeName, $employeeAddress, new CommissionedClassification($salaryRate, $commissionedRate));
        } catch (\PayrollSystem\Domain\Exceptions\InvalidArgumentException $e) {
            throw new \PayrollSystem\Application\Exceptions\InvalidArgumentException('入力されたパラメータに誤りがあります');
        }

        $this->employeeRepository->add($employee);

        return true;
    }
}
