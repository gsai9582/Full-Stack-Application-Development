package com.project.main;

import com.project.controller.EmployeeController;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.context.ApplicationContext;
import org.springframework.context.support.ClassPathXmlApplicationContext;

/**
 * Main Application Entry Point
 * Demonstrates Spring IoC container usage.
 * Uses ClassPathXmlApplicationContext (which implements BeanFactory interface)
 * to load Spring configuration and manage bean lifecycle.
 */
public class MainApp {

    private static final Logger logger = LoggerFactory.getLogger(MainApp.class);

    public static void main(String[] args) {
        logger.info("Initializing Spring Application Context...");

        // Load Spring context from XML configuration file
        // ClassPathXmlApplicationContext implements BeanFactory and ApplicationContext
        ApplicationContext context = new ClassPathXmlApplicationContext("applicationContext.xml");

        logger.info("Spring Context loaded successfully. Retrieving EmployeeController bean...");

        // Retrieve the EmployeeController bean from Spring container (IoC)
        EmployeeController controller = context.getBean(EmployeeController.class);

        // Start the console-based application
        controller.start();

        logger.info("Application finished.");
    }
}

