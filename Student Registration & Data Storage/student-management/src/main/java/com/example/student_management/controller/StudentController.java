package com.example.student_management.controller;

import java.util.List;

import org.springframework.web.bind.annotation.*;

import com.example.student_management.model.Student;
import com.example.student_management.service.StudentService;

@RestController
@RequestMapping("/students")
public class StudentController {

    private final StudentService studentService;

    public StudentController(StudentService studentService) {
        this.studentService = studentService;
    }

    @PostMapping("/add")
    public Student addStudent(@RequestBody Student student) {
        return studentService.saveStudent(student);
    }

    @GetMapping("/all")
    public List<Student> getAllStudents() {
        return studentService.getAllStudents();
    }
}
