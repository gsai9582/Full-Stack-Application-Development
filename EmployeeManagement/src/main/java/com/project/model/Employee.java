package com.project.model;

/**
 * Employee Model class (POJO)
 * Represents an employee with basic details.
 */
public class Employee {

    private int employeeId;
    private String employeeName;
    private String department;
    private double salary;
    private String email;
    private String phoneNumber;

    // Default constructor (required for Spring bean instantiation)
    public Employee() {
    }

    // Parameterized constructor
    public Employee(int employeeId, String employeeName, String department,
                    double salary, String email, String phoneNumber) {
        this.employeeId = employeeId;
        this.employeeName = employeeName;
        this.department = department;
        this.salary = salary;
        this.email = email;
        this.phoneNumber = phoneNumber;
    }

    // Getters and Setters
    public int getEmployeeId() {
        return employeeId;
    }

    public void setEmployeeId(int employeeId) {
        this.employeeId = employeeId;
    }

    public String getEmployeeName() {
        return employeeName;
    }

    public void setEmployeeName(String employeeName) {
        this.employeeName = employeeName;
    }

    public String getDepartment() {
        return department;
    }

    public void setDepartment(String department) {
        this.department = department;
    }

    public double getSalary() {
        return salary;
    }

    public void setSalary(double salary) {
        this.salary = salary;
    }

    public String getEmail() {
        return email;
    }

    public void setEmail(String email) {
        this.email = email;
    }

    public String getPhoneNumber() {
        return phoneNumber;
    }

    public void setPhoneNumber(String phoneNumber) {
        this.phoneNumber = phoneNumber;
    }

    @Override
    public String toString() {
        return String.format(
                "| %-4d | %-15s | %-12s | %10.2f | %-22s | %-12s |",
                employeeId, employeeName, department, salary, email, phoneNumber
        );
    }
}

