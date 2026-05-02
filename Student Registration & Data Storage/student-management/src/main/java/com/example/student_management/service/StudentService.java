package com.example.student_management.service;

import java.util.List;

import org.springframework.stereotype.Service;

import com.example.student_management.model.Student;
import com.example.student_management.repository.StudentRepository;

@Service
public class StudentService {

    private final StudentRepository studentRepository;

    public StudentService(StudentRepository studentRepository) {
        this.studentRepository = studentRepository;
    }

    public Student saveStudent(Student student) {
        return studentRepository.save(student);
    }

    public List<Student> getAllStudents() {
        return studentRepository.findAll();
    }
}
