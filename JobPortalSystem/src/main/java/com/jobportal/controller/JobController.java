package com.jobportal.controller;

import java.io.IOException;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.util.List;
import java.util.Optional;

import org.springframework.security.core.Authentication;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.multipart.MultipartFile;
import org.springframework.web.servlet.mvc.support.RedirectAttributes;

import com.jobportal.model.Job;
import com.jobportal.model.JobApplication;
import com.jobportal.model.User;
import com.jobportal.repo.JobApplicationRepository;
import com.jobportal.repo.JobRepository;
import com.jobportal.repo.UserRepository;

@Controller
public class JobController {

    private final JobRepository jobRepository;
    private final UserRepository userRepository;
    private final JobApplicationRepository applicationRepository;
    private final Path uploadLocation = Paths.get("src/main/resources/static/uploads");

    public JobController(JobRepository jobRepository, UserRepository userRepository,
                         JobApplicationRepository applicationRepository) {
        this.jobRepository = jobRepository;
        this.userRepository = userRepository;
        this.applicationRepository = applicationRepository;
        try {
            Files.createDirectories(uploadLocation);
        } catch (IOException e) {
            throw new RuntimeException("Unable to create upload directory", e);
        }
    }

    @GetMapping("/jobs")
    public String browseJobs(@RequestParam Optional<String> q, Model model) {
        String query = q.orElse("");
        List<Job> jobs;
        if (query.isBlank()) {
            jobs = jobRepository.findAll();
        } else {
            jobs = jobRepository.findByTitleContainingIgnoreCaseOrLocationContainingIgnoreCaseOrSkillsContainingIgnoreCase(query, query, query);
        }
        model.addAttribute("jobs", jobs);
        model.addAttribute("query", query);
        return "job-list";
    }

    @GetMapping("/jobs/{id}")
    public String jobDetails(@PathVariable Long id, Model model) {
        Job job = jobRepository.findById(id).orElseThrow();
        model.addAttribute("job", job);
        return "job-details";
    }

    @GetMapping("/jobs/{id}/apply")
    public String applyPage(@PathVariable Long id, Model model) {
        Job job = jobRepository.findById(id).orElseThrow();
        model.addAttribute("job", job);
        return "job-apply";
    }

    @PostMapping("/jobs/{id}/apply")
    public String submitApplication(@PathVariable Long id,
                                    @RequestParam("resume") MultipartFile resume,
                                    Authentication authentication,
                                    RedirectAttributes redirectAttributes) throws IOException {
        String email = authentication.getName();
        User user = userRepository.findByEmail(email).orElseThrow();
        Job job = jobRepository.findById(id).orElseThrow();

        if (resume.isEmpty()) {
            redirectAttributes.addFlashAttribute("error", "Please upload a resume to apply.");
            return "redirect:/jobs/" + id + "/apply";
        }

        String filename = System.currentTimeMillis() + "-" + resume.getOriginalFilename();
        Path destination = uploadLocation.resolve(filename);
        resume.transferTo(destination);

        JobApplication application = new JobApplication();
        application.setApplicant(user);
        application.setJob(job);
        application.setResumeFilename(filename);
        application.setStatus("Submitted");
        applicationRepository.save(application);

        redirectAttributes.addFlashAttribute("success", "Application submitted successfully.");
        return "redirect:/dashboard";
    }

    @GetMapping("/employer/post-job")
    public String postJobForm() {
        return "job-post";
    }

    @PostMapping("/employer/post-job")
    public String saveJob(@RequestParam String title,
                          @RequestParam String location,
                          @RequestParam String skills,
                          @RequestParam String salary,
                          @RequestParam String description,
                          Authentication authentication,
                          RedirectAttributes redirectAttributes) {
        String email = authentication.getName();
        Job job = new Job();
        job.setTitle(title);
        job.setLocation(location);
        job.setSkills(skills);
        job.setSalary(salary);
        job.setDescription(description);
        job.setPostedBy(email);
        jobRepository.save(job);
        redirectAttributes.addFlashAttribute("success", "Job posted successfully.");
        return "redirect:/dashboard";
    }

    @GetMapping("/employer/job/{id}/applicants")
    public String viewApplicants(@PathVariable Long id, Model model, Authentication authentication) {
        Job job = jobRepository.findById(id).orElseThrow();
        if (!job.getPostedBy().equals(authentication.getName())) {
            return "redirect:/dashboard";
        }
        model.addAttribute("job", job);
        model.addAttribute("applications", applicationRepository.findByJob(job));
        return "job-applicants";
    }
}
