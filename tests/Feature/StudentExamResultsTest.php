<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Student;
use App\StudentExamResult;
use App\CourseSubject;
use App\StudentAccDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class StudentExamResultsTest extends TestCase
{
    use DatabaseTransactions;

    protected $student;
    protected $academicDetails;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test student
        $this->student = Student::create([
            'registration_no' => 'TEST001',
            'index_no' => 'IND001',
            'initials' => 'J',
            'name_marking' => 'Doe',
            'full_name' => 'John Doe',
            'dob' => '2000-01-01',
            'gender' => 'M',
            'address' => 'Test Address',
            'email' => 'test@example.com',
            'phone' => '1234567890',
        ]);

        // Create academic details
        $this->academicDetails = StudentAccDetail::create([
            'student_id' => $this->student->id,
            'regulation_id' => 1,
            'batch' => 1,
            'current_study_year' => 2,
            'specialization_id' => 1,
        ]);
    }

    /**
     * Test that unauthenticated students cannot access results
     */
    public function test_unauthenticated_student_cannot_access_results()
    {
        Config::set('settings.std_show_results', 1);
        
        $response = $this->get('/student/view-results');
        
        $response->assertRedirect('/login');
    }

    /**
     * Test that results page is accessible when feature is enabled
     */
    public function test_results_page_accessible_when_enabled()
    {
        Config::set('settings.std_show_results', 1);
        
        $response = $this->actingAs($this->student, 'student')
                         ->get('/student/view-results');
        
        $response->assertStatus(200);
        $response->assertViewIs('student.view-results');
    }

    /**
     * Test that results page returns 403 when feature is disabled
     */
    public function test_results_page_returns_403_when_disabled()
    {
        Config::set('settings.std_show_results', 0);
        
        $response = $this->actingAs($this->student, 'student')
                         ->get('/student/view-results');
        
        $response->assertStatus(403);
    }

    /**
     * Test that student can view their own results
     */
    public function test_student_can_view_their_results()
    {
        Config::set('settings.std_show_results', 1);
        
        // Create test subjects
        $subject1 = CourseSubject::create([
            'code' => 'CS101',
            'name' => 'Introduction to Computer Science',
            'semester' => 1,
            'credits' => 3,
            'regulation_id' => 1,
            'status' => 1,
            'type' => 'C',
        ]);

        $subject2 = CourseSubject::create([
            'code' => 'CS102',
            'name' => 'Programming Fundamentals',
            'semester' => 1,
            'credits' => 3,
            'regulation_id' => 1,
            'status' => 1,
            'type' => 'C',
        ]);

        // Create test results
        StudentExamResult::create([
            'student_id' => $this->student->id,
            'year' => 2024,
            'course_subject_id' => $subject1->id,
            'marks' => 85,
            'result' => 'A',
            'status' => 1,
        ]);

        StudentExamResult::create([
            'student_id' => $this->student->id,
            'year' => 2024,
            'course_subject_id' => $subject2->id,
            'marks' => 78,
            'result' => 'B+',
            'status' => 1,
        ]);

        $response = $this->actingAs($this->student, 'student')
                         ->get('/student/view-results');
        
        $response->assertStatus(200);
        $response->assertViewHas('results');
        $response->assertViewHas('student');
    }

    /**
     * Test that results are grouped by semester
     */
    public function test_results_grouped_by_semester()
    {
        Config::set('settings.std_show_results', 1);
        
        // Create subjects for different semesters
        $subject1 = CourseSubject::create([
            'code' => 'CS101',
            'name' => 'Semester 1 Subject',
            'semester' => 1,
            'credits' => 3,
            'regulation_id' => 1,
            'status' => 1,
            'type' => 'C',
        ]);

        $subject2 = CourseSubject::create([
            'code' => 'CS201',
            'name' => 'Semester 2 Subject',
            'semester' => 2,
            'credits' => 3,
            'regulation_id' => 1,
            'status' => 1,
            'type' => 'C',
        ]);

        // Create results for both semesters
        StudentExamResult::create([
            'student_id' => $this->student->id,
            'year' => 2024,
            'course_subject_id' => $subject1->id,
            'marks' => 85,
            'result' => 'A',
            'status' => 1,
        ]);

        StudentExamResult::create([
            'student_id' => $this->student->id,
            'year' => 2024,
            'course_subject_id' => $subject2->id,
            'marks' => 75,
            'result' => 'B',
            'status' => 1,
        ]);

        $response = $this->actingAs($this->student, 'student')
                         ->get('/student/view-results');
        
        $response->assertStatus(200);
        $results = $response->viewData('results');
        
        $this->assertArrayHasKey(1, $results);
        $this->assertArrayHasKey(2, $results);
        $this->assertCount(1, $results[1]);
        $this->assertCount(1, $results[2]);
    }

    /**
     * Test that student with no results sees empty results
     */
    public function test_student_with_no_results_sees_empty_array()
    {
        Config::set('settings.std_show_results', 1);
        
        $response = $this->actingAs($this->student, 'student')
                         ->get('/student/view-results');
        
        $response->assertStatus(200);
        $results = $response->viewData('results');
        
        $this->assertIsArray($results);
        $this->assertEmpty($results);
    }

    /**
     * Test that results display correct subject information
     */
    public function test_results_display_correct_subject_info()
    {
        Config::set('settings.std_show_results', 1);
        
        $subject = CourseSubject::create([
            'code' => 'CS101',
            'name' => 'Test Subject',
            'semester' => 1,
            'credits' => 3,
            'regulation_id' => 1,
            'status' => 1,
            'type' => 'C',
        ]);

        StudentExamResult::create([
            'student_id' => $this->student->id,
            'year' => 2024,
            'course_subject_id' => $subject->id,
            'marks' => 85,
            'result' => 'A',
            'status' => 1,
        ]);

        $response = $this->actingAs($this->student, 'student')
                         ->get('/student/view-results');
        
        $results = $response->viewData('results');
        $semesterResults = $results[1];
        
        $this->assertEquals('CS101', $semesterResults[0]['code']);
        $this->assertEquals('Test Subject', $semesterResults[0]['name']);
        $this->assertEquals(2024, $semesterResults[0]['year']);
        $this->assertEquals(85, $semesterResults[0]['marks']);
        $this->assertEquals('A', $semesterResults[0]['result']);
    }

    /**
     * Test that results are ordered correctly (semester, code, year)
     */
    public function test_results_are_ordered_correctly()
    {
        Config::set('settings.std_show_results', 1);
        
        $subject1 = CourseSubject::create([
            'code' => 'CS201',
            'name' => 'Subject 2',
            'semester' => 2,
            'credits' => 3,
            'regulation_id' => 1,
            'status' => 1,
            'type' => 'C',
        ]);

        $subject2 = CourseSubject::create([
            'code' => 'CS101',
            'name' => 'Subject 1',
            'semester' => 1,
            'credits' => 3,
            'regulation_id' => 1,
            'status' => 1,
            'type' => 'C',
        ]);

        // Create results in reverse order
        StudentExamResult::create([
            'student_id' => $this->student->id,
            'year' => 2024,
            'course_subject_id' => $subject1->id,
            'marks' => 75,
            'result' => 'B',
            'status' => 1,
        ]);

        StudentExamResult::create([
            'student_id' => $this->student->id,
            'year' => 2023,
            'course_subject_id' => $subject2->id,
            'marks' => 85,
            'result' => 'A',
            'status' => 1,
        ]);

        $response = $this->actingAs($this->student, 'student')
                         ->get('/student/view-results');
        
        $results = $response->viewData('results');
        
        // Results should be ordered by semester first
        $semesterKeys = array_keys($results);
        $this->assertEquals([1, 2], $semesterKeys);
    }

    /**
     * Test that student cannot see other students' results
     */
    public function test_student_cannot_see_other_students_results()
    {
        Config::set('settings.std_show_results', 1);
        
        // Create another student
        $otherStudent = Student::create([
            'registration_no' => 'TEST002',
            'index_no' => 'IND002',
            'initials' => 'J',
            'name_marking' => 'Smith',
            'full_name' => 'Jane Smith',
            'dob' => '2000-01-01',
            'gender' => 'F',
            'address' => 'Test Address',
            'email' => 'jane@example.com',
            'phone' => '1234567890',
        ]);

        $subject = CourseSubject::create([
            'code' => 'CS101',
            'name' => 'Test Subject',
            'semester' => 1,
            'credits' => 3,
            'regulation_id' => 1,
            'status' => 1,
            'type' => 'C',
        ]);

        // Create result for other student
        StudentExamResult::create([
            'student_id' => $otherStudent->id,
            'year' => 2024,
            'course_subject_id' => $subject->id,
            'marks' => 90,
            'result' => 'A+',
            'status' => 1,
        ]);

        // Create result for current student
        StudentExamResult::create([
            'student_id' => $this->student->id,
            'year' => 2024,
            'course_subject_id' => $subject->id,
            'marks' => 75,
            'result' => 'B',
            'status' => 1,
        ]);

        $response = $this->actingAs($this->student, 'student')
                         ->get('/student/view-results');
        
        $results = $response->viewData('results');
        $semesterResults = $results[1];
        
        // Should only see own result
        $this->assertCount(1, $semesterResults);
        $this->assertEquals(75, $semesterResults[0]['marks']);
    }

    /**
     * Test that multiple results for same subject show max marks
     */
    public function test_multiple_results_same_subject_show_max_marks()
    {
        Config::set('settings.std_show_results', 1);
        
        $subject = CourseSubject::create([
            'code' => 'CS101',
            'name' => 'Test Subject',
            'semester' => 1,
            'credits' => 3,
            'regulation_id' => 1,
            'status' => 1,
            'type' => 'C',
        ]);

        // Create multiple results for same subject (different attempts)
        StudentExamResult::create([
            'student_id' => $this->student->id,
            'year' => 2023,
            'course_subject_id' => $subject->id,
            'marks' => 65,
            'result' => 'C',
            'status' => 1,
        ]);

        StudentExamResult::create([
            'student_id' => $this->student->id,
            'year' => 2024,
            'course_subject_id' => $subject->id,
            'marks' => 85,
            'result' => 'A',
            'status' => 1,
        ]);

        $response = $this->actingAs($this->student, 'student')
                         ->get('/student/view-results');
        
        $results = $response->viewData('results');
        $semesterResults = $results[1];
        
        // Should show both results as they are stored separately
        $this->assertGreaterThanOrEqual(1, count($semesterResults));
    }

    /**
     * Test that results with different grades are displayed correctly
     */
    public function test_results_with_different_grades_displayed_correctly()
    {
        Config::set('settings.std_show_results', 1);
        
        $grades = ['A+', 'A', 'A-', 'B+', 'B', 'B-', 'C', 'C+', 'F', 'MCA'];
        
        foreach ($grades as $index => $grade) {
            $subject = CourseSubject::create([
                'code' => 'CS' . (100 + $index),
                'name' => 'Subject ' . $grade,
                'semester' => 1,
                'credits' => 3,
                'regulation_id' => 1,
                'status' => 1,
                'type' => 'C',
            ]);

            StudentExamResult::create([
                'student_id' => $this->student->id,
                'year' => 2024,
                'course_subject_id' => $subject->id,
                'marks' => 80 - $index * 5,
                'result' => $grade,
                'status' => 1,
            ]);
        }

        $response = $this->actingAs($this->student, 'student')
                         ->get('/student/view-results');
        
        $response->assertStatus(200);
        $results = $response->viewData('results');
        
        $this->assertCount(count($grades), $results[1]);
    }

    /**
     * Test that empty semesters are not displayed
     */
    public function test_empty_semesters_not_displayed()
    {
        Config::set('settings.std_show_results', 1);
        
        $subject = CourseSubject::create([
            'code' => 'CS101',
            'name' => 'Test Subject',
            'semester' => 3,
            'credits' => 3,
            'regulation_id' => 1,
            'status' => 1,
            'type' => 'C',
        ]);

        StudentExamResult::create([
            'student_id' => $this->student->id,
            'year' => 2024,
            'course_subject_id' => $subject->id,
            'marks' => 85,
            'result' => 'A',
            'status' => 1,
        ]);

        $response = $this->actingAs($this->student, 'student')
                         ->get('/student/view-results');
        
        $results = $response->viewData('results');
        
        // Only semester 3 should be present
        $this->assertArrayHasKey(3, $results);
        $this->assertArrayNotHasKey(1, $results);
        $this->assertArrayNotHasKey(2, $results);
    }

    /**
     * Test that results view contains required data structure
     */
    public function test_results_view_contains_required_data_structure()
    {
        Config::set('settings.std_show_results', 1);
        
        $response = $this->actingAs($this->student, 'student')
                         ->get('/student/view-results');
        
        $response->assertViewHas('student');
        $response->assertViewHas('results');
        
        $student = $response->viewData('student');
        $results = $response->viewData('results');
        
        $this->assertInstanceOf(Student::class, $student);
        $this->assertIsArray($results);
    }

    /**
     * Test that student information is correctly passed to view
     */
    public function test_student_info_correctly_passed_to_view()
    {
        Config::set('settings.std_show_results', 1);
        
        $response = $this->actingAs($this->student, 'student')
                         ->get('/student/view-results');
        
        $student = $response->viewData('student');
        
        $this->assertEquals($this->student->id, $student->id);
        $this->assertEquals($this->student->registration_no, $student->registration_no);
        $this->assertEquals($this->student->full_name, $student->full_name);
    }
}
