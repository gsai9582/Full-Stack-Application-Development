package com.project.controller;

import com.project.model.Employee;
import com.project.service.EmployeeService;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Component;

import java.util.InputMismatchException;
import java.util.List;
import java.util.Scanner;

/**
 * EmployeeController (Presentation Layer)
 * Annotated with @Component so Spring detects it during component scanning.
 * Handles console-based user interaction and delegates business logic to EmployeeService.
 */
@Component
public class EmployeeController {

    private static final Logger logger = LoggerFactory.getLogger(EmployeeController.class);

    private final EmployeeService employeeService;
    private final Scanner scanner;

    @Autowired
    public EmployeeController(EmployeeService employeeService) {
        this.employeeService = employeeService;
        this.scanner = new Scanner(System.in);
        logger.info("EmployeeController initialized.");
    }

    /**
     * Starts the console menu loop.
     */
    public void start() {
        logger.info("Starting Employee Management Console Application...");
        boolean running = true;

        while (running) {
            printMenu();
            int choice = readIntInput("Enter your choice: ");

            switch (choice) {
                case 1:
                    addEmployee();
                    break;
                case 2:
                    viewAllEmployees();
                    break;
                case 3:
                    searchEmployeeById();
                    break;
                case 4:
                    updateEmployee();
                    break;
                case 5:
                    deleteEmployee();
                    break;
                case 6:
                    searchByDepartment();
                    break;
                case 7:
                    sortEmployees();
                    break;
                case 8:
                    countEmployees();
                    break;
                case 9:
                    running = false;
                    System.out.println("\nThank you for using Employee Management System. Goodbye!");
                    logger.info("Application exited by user.");
                    break;
                default:
                    System.out.println("Invalid choice! Please select a valid option.");
            }
        }
        scanner.close();
    }

    private void printMenu() {
        System.out.println("\n========== Employee Management System ==========");
        System.out.println("1. Add Employee");
        System.out.println("2. View All Employees");
        System.out.println("3. Search Employee by ID");
        System.out.println("4. Update Employee");
        System.out.println("5. Delete Employee");
        System.out.println("6. Search by Department");
        System.out.println("7. Sort Employees");
        System.out.println("8. Count Total Employees");
        System.out.println("9. Exit");
        System.out.println("================================================");
    }

    private void addEmployee() {
        System.out.println("\n--- Add New Employee ---");
        try {
            int id = readIntInput("Enter Employee ID: ");
            if (id <= 0) {
                System.out.println("Error: ID must be a positive number.");
                return;
            }

            System.out.print("Enter Employee Name: ");
            String name = scanner.nextLine().trim();

            System.out.print("Enter Department: ");
            String dept = scanner.nextLine().trim();

            double salary = readDoubleInput("Enter Salary: ");

            System.out.print("Enter Email: ");
            String email = scanner.nextLine().trim();

            System.out.print("Enter Phone Number: ");
            String phone = scanner.nextLine().trim();

            Employee employee = new Employee(id, name, dept, salary, email, phone);
            employeeService.addEmployee(employee);
            System.out.println("Success: Employee added successfully!");
            logger.info("User added employee with ID: {}", id);

        } catch (IllegalArgumentException e) {
            System.out.println("Error: " + e.getMessage());
            logger.warn("Failed to add employee: {}", e.getMessage());
        }
    }

    private void viewAllEmployees() {
        System.out.println("\n--- All Employees ---");
        List<Employee> employees = employeeService.getAllEmployees();
        if (employees.isEmpty()) {
            System.out.println("No employees found.");
        } else {
            printEmployeeTable(employees);
        }
    }

    private void searchEmployeeById() {
        System.out.println("\n--- Search Employee by ID ---");
        int id = readIntInput("Enter Employee ID: ");
        Employee employee = employeeService.getEmployeeById(id);
        if (employee != null) {
            printEmployeeTableHeader();
            System.out.println(employee);
            printTableFooter();
        } else {
            System.out.println("Employee with ID " + id + " not found.");
        }
    }

    private void updateEmployee() {
        System.out.println("\n--- Update Employee ---");
        int id = readIntInput("Enter Employee ID to update: ");
        Employee existing = employeeService.getEmployeeById(id);

        if (existing == null) {
            System.out.println("Employee with ID " + id + " not found.");
            return;
        }

        System.out.println("Leave field empty to keep existing value.");

        System.out.print("Enter new Name [" + existing.getEmployeeName() + "]: ");
        String name = scanner.nextLine().trim();
        if (!name.isEmpty()) existing.setEmployeeName(name);

        System.out.print("Enter new Department [" + existing.getDepartment() + "]: ");
        String dept = scanner.nextLine().trim();
        if (!dept.isEmpty()) existing.setDepartment(dept);

        System.out.print("Enter new Salary [" + existing.getSalary() + "]: ");
        String salaryStr = scanner.nextLine().trim();
        if (!salaryStr.isEmpty()) {
            try {
                existing.setSalary(Double.parseDouble(salaryStr));
            } catch (NumberFormatException e) {
                System.out.println("Invalid salary input. Keeping old value.");
            }
        }

        System.out.print("Enter new Email [" + existing.getEmail() + "]: ");
        String email = scanner.nextLine().trim();
        if (!email.isEmpty()) existing.setEmail(email);

        System.out.print("Enter new Phone [" + existing.getPhoneNumber() + "]: ");
        String phone = scanner.nextLine().trim();
        if (!phone.isEmpty()) existing.setPhoneNumber(phone);

        employeeService.updateEmployee(existing);
        System.out.println("Success: Employee updated successfully!");
        logger.info("User updated employee with ID: {}", id);
    }

    private void deleteEmployee() {
        System.out.println("\n--- Delete Employee ---");
        int id = readIntInput("Enter Employee ID to delete: ");
        if (employeeService.deleteEmployee(id)) {
            System.out.println("Success: Employee deleted successfully!");
            logger.info("User deleted employee with ID: {}", id);
        } else {
            System.out.println("Employee with ID " + id + " not found.");
        }
    }

    private void searchByDepartment() {
        System.out.println("\n--- Search by Department ---");
        System.out.print("Enter Department Name: ");
        String dept = scanner.nextLine().trim();
        List<Employee> employees = employeeService.searchByDepartment(dept);
        if (employees.isEmpty()) {
            System.out.println("No employees found in department: " + dept);
        } else {
            System.out.println("Employees in " + dept + " department:");
            printEmployeeTable(employees);
        }
    }

    private void sortEmployees() {
        System.out.println("\n--- Sort Employees ---");
        System.out.println("1. Sort by Salary");
        System.out.println("2. Sort by Name");
        int sortChoice = readIntInput("Enter choice: ");
        List<Employee> employees;

        if (sortChoice == 1) {
            employees = employeeService.sortBySalary();
            System.out.println("Employees sorted by Salary (Ascending):");
        } else if (sortChoice == 2) {
            employees = employeeService.sortByName();
            System.out.println("Employees sorted by Name (Alphabetical):");
        } else {
            System.out.println("Invalid choice.");
            return;
        }

        if (employees.isEmpty()) {
            System.out.println("No employees to sort.");
        } else {
            printEmployeeTable(employees);
        }
    }

    private void countEmployees() {
        int count = employeeService.countEmployees();
        System.out.println("\nTotal Employees: " + count);
    }

    // ================== Helper Methods ==================

    private int readIntInput(String prompt) {
        while (true) {
            try {
                System.out.print(prompt);
                int value = scanner.nextInt();
                scanner.nextLine(); // consume newline
                return value;
            } catch (InputMismatchException e) {
                System.out.println("Invalid input. Please enter a valid number.");
                scanner.nextLine(); // clear invalid input
            }
        }
    }

    private double readDoubleInput(String prompt) {
        while (true) {
            try {
                System.out.print(prompt);
                double value = scanner.nextDouble();
                scanner.nextLine(); // consume newline
                return value;
            } catch (InputMismatchException e) {
                System.out.println("Invalid input. Please enter a valid number.");
                scanner.nextLine(); // clear invalid input
            }
        }
    }

    private void printEmployeeTable(List<Employee> employees) {
        printEmployeeTableHeader();
        for (Employee e : employees) {
            System.out.println(e);
        }
        printTableFooter();
    }

    private void printEmployeeTableHeader() {
        System.out.println("+------+-----------------+--------------+------------+------------------------+--------------+");
        System.out.println("| ID   | Name            | Department   | Salary     | Email                  | Phone        |");
        System.out.println("+------+-----------------+--------------+------------+------------------------+--------------+");
    }

    private void printTableFooter() {
        System.out.println("+------+-----------------+--------------+------------+------------------------+--------------+");
    }
}

