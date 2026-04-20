package com.jobportal.data;

import org.springframework.boot.CommandLineRunner;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.stereotype.Component;

import com.jobportal.model.Job;
import com.jobportal.model.User;
import com.jobportal.repo.JobRepository;
import com.jobportal.repo.UserRepository;

@Component
public class DataInitializer implements CommandLineRunner {

    private final UserRepository userRepository;
    private final JobRepository jobRepository;
    private final PasswordEncoder passwordEncoder;

    public DataInitializer(UserRepository userRepository, JobRepository jobRepository,
                           PasswordEncoder passwordEncoder) {
        this.userRepository = userRepository;
        this.jobRepository = jobRepository;
        this.passwordEncoder = passwordEncoder;
    }

    @Override
    public void run(String... args) {
        if (userRepository.count() == 0) {
            User admin = new User();
            admin.setName("Portal Admin");
            admin.setEmail("admin@jobportal.com");
            admin.setPassword(passwordEncoder.encode("admin123"));
            admin.setRole("ADMIN");
            userRepository.save(admin);

            User employer = new User();
            employer.setName("Acme Recruiter");
            employer.setEmail("employer@jobportal.com");
            employer.setPassword(passwordEncoder.encode("employer123"));
            employer.setRole("EMPLOYER");
            userRepository.save(employer);

            User student = new User();
            student.setName("Jane Student");
            student.setEmail("student@jobportal.com");
            student.setPassword(passwordEncoder.encode("student123"));
            student.setRole("JOB_SEEKER");
            userRepository.save(student);
        }

        if (jobRepository.count() == 0) {
            Job job1 = new Job();
            job1.setTitle("Senior Java Developer");
            job1.setLocation("New York, NY");
            job1.setSkills("Spring Boot, JPA, REST APIs");
            job1.setSalary("$90k - $120k");
            job1.setDescription("Join a premium technology team building enterprise hiring software.");
            job1.setPostedBy("employer@jobportal.com");
            jobRepository.save(job1);

            Job job2 = new Job();
            job2.setTitle("Campus Recruiter");
            job2.setLocation("Remote");
            job2.setSkills("Recruiting, ATS, Communication");
            job2.setSalary("$60k - $80k");
            job2.setDescription("Support student hiring with structured candidate pipelines and admin reviews.");
            job2.setPostedBy("employer@jobportal.com");
            jobRepository.save(job2);
        }
    }
}
