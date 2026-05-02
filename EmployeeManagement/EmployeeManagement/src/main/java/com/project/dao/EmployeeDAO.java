package com.project.dao;

import com.project.model.Employee;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.stereotype.Repository;

import java.util.ArrayList;
import java.util.Comparator;
import java.util.List;
import java.util.stream.Collectors;

/**
 * EmployeeDAO (Data Access Object)
 * Annotated with @Repository to indicate it's a DAO layer component.
 * Manages in-memory employee data using an ArrayList.
 */
@Repository
public class EmployeeDAO {

    private static final Logger logger = LoggerFactory.getLogger(EmployeeDAO.class);

    // In-memory storage for employee records
    private final List<Employee> employees = new ArrayList<>();

    /**
     * Adds a new employee to the list.
     */
    public void addEmployee(Employee employee) {
        employees.add(employee);
        logger.info("Employee added: ID = {}", employee.getEmployeeId());
    }

    /**
     * Retrieves all employees.
     */
    public List<Employee> getAllEmployees() {
        logger.debug("Fetching all employees. Total count: {}", employees.size());
        return new ArrayList<>(employees);
    }

    /**
     * Finds an employee by their ID.
     */
    public Employee getEmployeeById(int employeeId) {
        return employees.stream()
                .filter(e -> e.getEmployeeId() == employeeId)
                .findFirst()
                .orElse(null);
    }

    /**
     * Updates an existing employee.
     */
    public boolean updateEmployee(Employee updatedEmployee) {
        for (int i = 0; i < employees.size(); i++) {
            if (employees.get(i).getEmployeeId() == updatedEmployee.getEmployeeId()) {
                employees.set(i, updatedEmployee);
                logger.info("Employee updated: ID = {}", updatedEmployee.getEmployeeId());
                return true;
            }
        }
        return false;
    }

    /**
     * Deletes an employee by ID.
     */
    public boolean deleteEmployee(int employeeId) {
        boolean removed = employees.removeIf(e -> e.getEmployeeId() == employeeId);
        if (removed) {
            logger.info("Employee deleted: ID = {}", employeeId);
        }
        return removed;
    }

    /**
     * Searches employees by department name.
     */
    public List<Employee> searchByDepartment(String department) {
        return employees.stream()
                .filter(e -> e.getDepartment().equalsIgnoreCase(department))
                .collect(Collectors.toList());
    }

    /**
     * Sorts employees by salary in ascending order.
     */
    public List<Employee> sortBySalary() {
        return employees.stream()
                .sorted(Comparator.comparingDouble(Employee::getSalary))
                .collect(Collectors.toList());
    }

    /**
     * Sorts employees by name in alphabetical order.
     */
    public List<Employee> sortByName() {
        return employees.stream()
                .sorted(Comparator.comparing(Employee::getEmployeeName, String.CASE_INSENSITIVE_ORDER))
                .collect(Collectors.toList());
    }

    /**
     * Returns the total number of employees.
     */
    public int countEmployees() {
        return employees.size();
    }

    /**
     * Checks if an employee with the given ID already exists.
     */
    public boolean existsById(int employeeId) {
        return employees.stream().anyMatch(e -> e.getEmployeeId() == employeeId);
    }
}

