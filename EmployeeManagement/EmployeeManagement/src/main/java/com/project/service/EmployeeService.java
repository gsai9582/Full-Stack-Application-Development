package com.project.service;

import com.project.dao.EmployeeDAO;
import com.project.model.Employee;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import java.util.List;

/**
 * EmployeeService (Business Logic Layer)
 * Annotated with @Service to indicate it holds business logic.
 * Uses @Autowired to inject EmployeeDAO (Dependency Injection).
 */
@Service
public class EmployeeService {

    private static final Logger logger = LoggerFactory.getLogger(EmployeeService.class);

    // Dependency Injection via @Autowired on constructor
    private final EmployeeDAO employeeDAO;

    @Autowired
    public EmployeeService(EmployeeDAO employeeDAO) {
        this.employeeDAO = employeeDAO;
        logger.info("EmployeeService initialized with EmployeeDAO injected.");
    }

    /**
     * Adds an employee after validating duplicate ID.
     */
    public void addEmployee(Employee employee) throws IllegalArgumentException {
        if (employeeDAO.existsById(employee.getEmployeeId())) {
            throw new IllegalArgumentException("Employee with ID " + employee.getEmployeeId() + " already exists!");
        }
        employeeDAO.addEmployee(employee);
    }

    /**
     * Returns all employees.
     */
    public List<Employee> getAllEmployees() {
        return employeeDAO.getAllEmployees();
    }

    /**
     * Finds an employee by ID.
     */
    public Employee getEmployeeById(int employeeId) {
        return employeeDAO.getEmployeeById(employeeId);
    }

    /**
     * Updates employee details.
     */
    public boolean updateEmployee(Employee employee) {
        return employeeDAO.updateEmployee(employee);
    }

    /**
     * Deletes an employee by ID.
     */
    public boolean deleteEmployee(int employeeId) {
        return employeeDAO.deleteEmployee(employeeId);
    }

    /**
     * Searches employees by department.
     */
    public List<Employee> searchByDepartment(String department) {
        return employeeDAO.searchByDepartment(department);
    }

    /**
     * Returns employees sorted by salary.
     */
    public List<Employee> sortBySalary() {
        return employeeDAO.sortBySalary();
    }

    /**
     * Returns employees sorted by name.
     */
    public List<Employee> sortByName() {
        return employeeDAO.sortByName();
    }

    /**
     * Returns total employee count.
     */
    public int countEmployees() {
        return employeeDAO.countEmployees();
    }

    /**
     * Checks if employee exists.
     */
    public boolean existsById(int employeeId) {
        return employeeDAO.existsById(employeeId);
    }
}

