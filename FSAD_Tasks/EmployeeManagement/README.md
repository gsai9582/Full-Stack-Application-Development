# Employee Management Module using Spring Core

A complete console-based Employee Management system built with **Java**, **Spring Core**, **Maven**, and **Annotation/XML Configuration**. This project demonstrates **Inversion of Control (IoC)** and **Dependency Injection (DI)** using Spring's `BeanFactory` container.

---

## Table of Contents
1. [Project Overview](#project-overview)
2. [Technologies Used](#technologies-used)
3. [Project Structure](#project-structure)
4. [Setup Instructions](#setup-instructions)
5. [How to Run](#how-to-run)
6. [Spring Concepts Explained](#spring-concepts-explained)
7. [Sample Output](#sample-output)
8. [Features](#features)

---

## Project Overview

This application allows users to manage employee records stored **in-memory** (no database required). It provides a professional console menu to perform CRUD operations and additional features like sorting and searching.

All object creation and dependency wiring is handled by the **Spring Container** (IoC), not by manual `new` keywords.

---

## Technologies Used

| Technology | Version |
|------------|---------|
| Java | 11 |
| Spring Framework | 5.3.30 |
| Maven | 3.x |
| SLF4J / Logback | 1.7.36 / 1.2.12 |

---

## Project Structure

```
EmployeeManagement/
├── pom.xml
├── README.md
├── src/
│   ├── main/
│   │   ├── java/
│   │   │   └── com/
│   │   │       └── project/
│   │   │           ├── main/
│   │   │           │   └── MainApp.java
│   │   │           ├── controller/
│   │   │           │   └── EmployeeController.java
│   │   │           ├── service/
│   │   │           │   └── EmployeeService.java
│   │   │           ├── dao/
│   │   │           │   └── EmployeeDAO.java
│   │   │           └── model/
│   │   │               └── Employee.java
│   │   └── resources/
│   │       └── applicationContext.xml
```

---

## Setup Instructions

### Option 1: IntelliJ IDEA
1. Open IntelliJ IDEA.
2. Click **File > Open** and select the `EmployeeManagement` folder.
3. IntelliJ will auto-detect the `pom.xml` and import the Maven project.
4. Wait for Maven to download dependencies (check the bottom status bar).
5. Navigate to `src/main/java/com/project/main/MainApp.java`.
6. Right-click and select **Run 'MainApp.main()'**.

### Option 2: Eclipse
1. Open Eclipse.
2. Click **File > Import > Existing Maven Projects**.
3. Browse to the `EmployeeManagement` folder and click **Finish**.
4. Wait for Eclipse to build the project and download dependencies.
5. Right-click on `MainApp.java` and select **Run As > Java Application**.

### Option 3: VS Code
1. Open VS Code.
2. Click **File > Open Folder** and select `EmployeeManagement`.
3. Install the **Extension Pack for Java** and **Maven for Java** extensions if not already installed.
4. Open `pom.xml` and wait for Maven to import the project.
5. Open `MainApp.java` and click the **Run** button above the `main` method.

---

## How to Run

### Using Maven Command Line
```bash
# Navigate to project directory
cd EmployeeManagement

# Compile the project
mvn clean compile

# Run the application
mvn exec:java
```

### Using Java Command Line (after compiling)
```bash
mvn clean package
java -jar target/employee-management-1.0-SNAPSHOT.jar
```

---

## Spring Concepts Explained

### 1. Inversion of Control (IoC)
**Traditional Approach:** You create objects manually using `new`.
```java
EmployeeDAO dao = new EmployeeDAO();
EmployeeService service = new EmployeeService(dao);
```

**IoC Approach:** You tell Spring what objects (beans) you need, and Spring creates and manages them for you.
```java
ApplicationContext context = new ClassPathXmlApplicationContext("applicationContext.xml");
EmployeeController controller = context.getBean(EmployeeController.class);
```

**Simple Words:** Instead of YOU controlling object creation, the Spring Container controls it. You just ask for the object when you need it.

### 2. Dependency Injection (DI)
DI is a design pattern that implements IoC. Instead of a class creating its own dependencies, Spring **injects** them from outside.

In this project:
- `EmployeeController` needs `EmployeeService`
- `EmployeeService` needs `EmployeeDAO`

We never write `new EmployeeService()` inside `EmployeeController`. Spring sees the `@Autowired` constructor and automatically provides the required bean.

### 3. How @Component and @Autowired Work

**@Component:**
- Tells Spring: "Hey, this class is a Spring bean. Please manage its lifecycle."
- Variants: `@Repository` (DAO layer), `@Service` (Business layer).

**@Autowired:**
- Tells Spring: "I need another bean here. Please find it and inject it for me."
- Can be used on constructors, fields, or setter methods.

Example from this project:
```java
@Service
public class EmployeeService {
    private final EmployeeDAO employeeDAO;

    @Autowired
    public EmployeeService(EmployeeDAO employeeDAO) {
        this.employeeDAO = employeeDAO; // Spring injects the DAO automatically
    }
}
```

### 4. How BeanFactory Manages Beans

`BeanFactory` is the core Spring interface for managing beans. `ClassPathXmlApplicationContext` is a concrete implementation that:
1. Reads the XML configuration (`applicationContext.xml`).
2. Scans packages for annotated classes (`@Component`, `@Service`, `@Repository`).
3. Creates bean instances (object creation).
4. Injects dependencies (`@Autowired`).
5. Stores beans in a container (registry).
6. Provides beans when requested via `getBean()`.

---

## Sample Output

```
========== Employee Management System ==========
1. Add Employee
2. View All Employees
3. Search Employee by ID
4. Update Employee
5. Delete Employee
6. Search by Department
7. Sort Employees
8. Count Total Employees
9. Exit
================================================
Enter your choice: 1

--- Add New Employee ---
Enter Employee ID: 101
Enter Employee Name: Alice Johnson
Enter Department: Engineering
Enter Salary: 75000
Enter Email: alice@company.com
Enter Phone Number: 555-0101
Success: Employee added successfully!

========== Employee Management System ==========
...
Enter your choice: 2

--- All Employees ---
+------+-----------------+--------------+------------+------------------------+--------------+
| ID   | Name            | Department   | Salary     | Email                  | Phone        |
+------+-----------------+--------------+------------+------------------------+--------------+
| 101  | Alice Johnson   | Engineering  |   75000.00 | alice@company.com      | 555-0101     |
+------+-----------------+--------------+------------+------------------------+--------------+

========== Employee Management System ==========
...
Enter your choice: 9

Thank you for using Employee Management System. Goodbye!
```

---

## Features

- Add Employee with duplicate ID validation
- View All Employees in a formatted table
- Search Employee by ID
- Update Employee (selective field updates)
- Delete Employee by ID
- Search Employees by Department
- Sort Employees by Salary or Name
- Count Total Employees
- Exception handling for invalid input
- SLF4J/Logback logging
- Clean layered architecture (Model, DAO, Service, Controller)

---

## Author

Built for learning Spring Core fundamentals: IoC, DI, and BeanFactory.

