<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminExamController;
use App\Http\Controllers\AdminRegistrationController;
use App\Http\Controllers\AdminResultController;
use App\Http\Controllers\AdminStudentController;
use App\Http\Controllers\AdminTranscriptController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentExamController;
use App\Http\Controllers\StudentRegistrationController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/', [StudentController::class, 'index'])->name('home');
Route::get('/home', [StudentController::class, 'index']);

Route::get('/logout', [LoginController::class, 'logout'])->name('student.logout');
Route::get('/student/update-profile', [StudentController::class, 'update_profile'])->name('update.profile');
Route::post('/student/update-profile', [StudentController::class, 'save_profile_updates']);
Route::get('/student/profile-picture/{id}', [StudentController::class, 'profile_images']);

Route::get('/student/update-password', [StudentController::class, 'update_password'])->name('update.password');
Route::post('/student/update-password', [StudentController::class, 'save_updated_password']);

Route::get('/student/annual-registration', [StudentRegistrationController::class, 'annual_registration']);
Route::get('/student/semester-registration', [StudentRegistrationController::class, 'semester_registration']);
Route::post('/student/semester-registration', [StudentRegistrationController::class, 'save_semester_registration']);

Route::get('/student/specialization-selection', [StudentRegistrationController::class, 'specialization_selection']);
Route::post('/student/request-specialization', [StudentRegistrationController::class, 'save_specialization_selection']);

Route::get('/student/exam-registration', [StudentExamController::class, 'exam_registration']);
Route::get('/student/exam-registration-view', [StudentExamController::class, 'exam_registration_view']);
Route::post('/student/exam-registration-view', [StudentExamController::class, 'save_register_exam']);
Route::get('/student/view-approved-exam-subjects', [StudentExamController::class, 'view_approved_exam_subjects']);

Route::get('/student/view-results', [StudentExamController::class, 'view_results']);

Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::get('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');
Route::post('/admin/login', [AdminLoginController::class, 'login'])->name('admin.login.submit');
Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');

Route::get('/admin/student', [AdminStudentController::class, 'index']);
Route::get('/admin/student/list', [AdminStudentController::class, 'listing'])->name('admin.student.list');
Route::get('/admin/student/view/{id}', [AdminStudentController::class, 'view'])->name('admin.student.view');
Route::get('/admin/student/profile-pic', [AdminStudentController::class, 'view_profile_images']);
Route::post('/admin/student/update-personal-details', [AdminStudentController::class, 'update_personal_details'])->name('admin.student.update-personal-details');
Route::post('/admin/student/update-al-details', [AdminStudentController::class, 'update_al_details']);

Route::post('/admin/student/update-scholarship-details', [AdminStudentController::class, 'update_student_scholarship']);
Route::post('/admin/student/add-batch-mis', [AdminStudentController::class, 'add_batch_mis']);
Route::post('/admin/student/add-student-achievemnt', [AdminStudentController::class, 'add_student_achievemnt']);

Route::get('/admin/student/upload', [AdminStudentController::class, 'import']);
Route::post('/admin/student/upload', [AdminStudentController::class, 'upload'])->name('admin.student.upload');
Route::post('/admin/student/get-uploaded-list', [AdminStudentController::class, 'uploaded_list']);
Route::post('/admin/student/process-uploaded', [AdminStudentController::class, 'process_import']);

Route::get('/admin/student/transfer', [AdminStudentController::class, 'transfer']);
Route::post('/admin/student/transfer', [AdminStudentController::class, 'upload_transfer'])->name('admin.student.transfer');
Route::get('/admin/student/get-transferred-list', [AdminStudentController::class, 'transfer_list']);
Route::post('/admin/student/process-transfer', [AdminStudentController::class, 'process_transfer']);

Route::get('/admin/student/graduate', [AdminStudentController::class, 'graduate']);
Route::post('/admin/student/graduate', [AdminStudentController::class, 'upload_graduate'])->name('admin.student.graduate');
Route::get('/admin/student/get-graduated-list', [AdminStudentController::class, 'graduate_list']);
Route::post('/admin/student/process-graduate', [AdminStudentController::class, 'process_graduate']);

Route::get('/admin/student/upload-profile-pic', [AdminStudentController::class, 'view_upload_profile_pictures']);
Route::post('/admin/student/upload-profile-pic', [AdminStudentController::class, 'upload_profile_pictures']);

Route::get('/admin/student/upload-documents', [AdminStudentController::class, 'view_upload_documents']);
Route::post('/admin/student/upload-documents', [AdminStudentController::class, 'upload_documents']);
Route::get('/admin/student/get-document/{id}', [AdminStudentController::class, 'download_document']);

Route::get('/admin/student/upload-scholarship', [AdminStudentController::class, 'view_upload_scholarships']);
Route::post('/admin/student/upload-scholarship', [AdminStudentController::class, 'upload_scholarships']);

Route::get('/admin/settings', [SettingsController::class, 'index']);
Route::get('/admin/settings/system-settings', [SettingsController::class, 'list_settings']);
Route::post('/admin/settings/update-system-settings', [SettingsController::class, 'update_setting']);

Route::get('/admin/settings/fees', [SettingsController::class, 'list_fees']);
Route::post('/admin/settings/update-fees', [SettingsController::class, 'update_fees']);

Route::get('/admin/settings/regulations', [SettingsController::class, 'list_regulations']);
Route::post('/admin/settings/update-regulations', [SettingsController::class, 'update_regulations']);
Route::post('/admin/settings/add-regulations', [SettingsController::class, 'add_regulations']);

Route::get('/admin/settings/batch', [SettingsController::class, 'list_batch']);
Route::post('/admin/settings/update-batch', [SettingsController::class, 'update_batch']);
Route::post('/admin/settings/add-batch', [SettingsController::class, 'add_batch']);

Route::get('/admin/settings/regulation', [SettingsController::class, 'list_regulations']);
Route::post('/admin/settings/update-regulation', [SettingsController::class, 'update_regulation']);
Route::post('/admin/settings/add-regulation', [SettingsController::class, 'add_regulation']);

Route::get('/admin/settings/courses', [SettingsController::class, 'list_courses']);
Route::get('/admin/settings/courses-specialization', [SettingsController::class, 'get_course_specialization']);
Route::post('/admin/settings/add-course', [SettingsController::class, 'add_course']);
Route::post('/admin/settings/update-course', [SettingsController::class, 'update_course']);
Route::post('/admin/settings/update-course-specialization', [SettingsController::class, 'update_course_specialization']);
Route::get('/admin/settings/courses-lectuerer', [SettingsController::class, 'get_course_lecturer_list']);
Route::post('/admin/settings/assign-courses-lectuerer', [SettingsController::class, 'update_course_lecturer']);

Route::post('/admin/settings/delete-course', [SettingsController::class, 'delete_course']);

Route::get('/admin/settings/schedules', [SettingsController::class, 'list_schedules']);
Route::post('/admin/settings/update-schedule', [SettingsController::class, 'update_schedule']);

Route::get('/admin/settings/roles', [SettingsController::class, 'list_roles']);
Route::get('/admin/settings/add-roles', [SettingsController::class, 'create_role_view']);
Route::post('/admin/settings/add-roles', [SettingsController::class, 'create_role']);
Route::get('/admin/settings/update-roles/{id}', [SettingsController::class, 'update_role_view']);
Route::post('/admin/settings/update-roles', [SettingsController::class, 'update_role']);

Route::get('/admin/settings/users', [SettingsController::class, 'list_users']);
Route::get('/admin/settings/add-user', [SettingsController::class, 'create_user_view']);
Route::post('/admin/settings/add-user', [SettingsController::class, 'create_user']);
Route::get('/admin/settings/update-user/{id}', [SettingsController::class, 'update_user_view']);
Route::post('/admin/settings/update-user', [SettingsController::class, 'update_user']);

Route::get('/admin/settings/system-logs', [SettingsController::class, 'list_system_logs']);
Route::get('/admin/settings/get-file', [SettingsController::class, 'get_uploaded_files']);

Route::get('/admin/account', [AdminController::class, 'view_account']);
Route::post('/admin/account', [AdminController::class, 'update_account']);

Route::get('/admin/registration/view-year-registration', [AdminRegistrationController::class, 'view_year_registration']);

Route::get('/admin/registration/upload-year-registration', [AdminRegistrationController::class, 'view_upload_year_registration']);
Route::post('/admin/registration/upload-year-registration', [AdminRegistrationController::class, 'upload_year_registration']);
Route::get('/admin/registration/process-specialization', [AdminRegistrationController::class, 'view_process_specialization']);
Route::get('/admin/registration/download-specialization', [AdminRegistrationController::class, 'download_specialization']);
Route::post('/admin/registration/upload-specialization', [AdminRegistrationController::class, 'upload_specialization_selection']);
Route::get('/admin/registration/export-to-lms', [AdminRegistrationController::class, 'view_lms_export']);
Route::get('/admin/registration/download-vle-export', [AdminRegistrationController::class, 'download_vle_export_file']);

Route::get('/admin/exam', [AdminExamController::class, 'index'])->name('admin.exam');
Route::get('/admin/exam/list', [AdminExamController::class, 'listing']);
Route::get('/admin/exam/application/{id}', [AdminExamController::class, 'view_application']);
Route::get('/admin/exam/get-subjects', [AdminExamController::class, 'get_subjects']);
Route::get('/admin/exam/approve-by-subject', [AdminExamController::class, 'approve_by_subject']);
Route::post('/admin/exam/approve-app-subject', [AdminExamController::class, 'approve_application_subject']);
Route::get('/admin/exam/download-applications', [AdminExamController::class, 'download_applications']);
Route::get('/admin/exam/print-applications', [AdminExamController::class, 'print_applications']);
Route::get('/admin/exam/export-to-excel', [AdminExamController::class, 'excel_export_applications']);

Route::get('/admin/results/view-uploaded-results', [AdminResultController::class, 'view_uploaded_resutls']);
Route::get('/admin/results/upload-results', [AdminResultController::class, 'view_results_upload']);
Route::post('/admin/results/upload-results', [AdminResultController::class, 'upload_results']);
Route::get('/admin/results/get-uploaded-results', [AdminResultController::class, 'get_uploaded_results']);
Route::post('/admin/results/process-upload-results', [AdminResultController::class, 'process_uploaded_results']);
Route::get('/admin/results/upload-results-bulk', [AdminResultController::class, 'view_bulk_results_upload']);
Route::post('/admin/results/upload-results-bulk', [AdminResultController::class, 'upload_bulk_results']);
Route::post('/admin/results/process-upload-bulk-results', [AdminResultController::class, 'process_bulk_uploaded_results']);
Route::get('/admin/results/process-gpa', [AdminResultController::class, 'gpa_process_view']);
Route::get('/admin/results/download-raw-gpa', [AdminResultController::class, 'download_raw_gpa_file']);
Route::get('/admin/results/upload-gpa', [AdminResultController::class, 'view_upload_gpa']);
Route::post('/admin/results/upload-gpa', [AdminResultController::class, 'upload_gpa']);

Route::get('/admin/transcripts/semester-transcripts', [AdminTranscriptController::class, 'print_semester_transcripts']);
Route::get('/admin/transcripts/semester-transcripts-download', [AdminTranscriptController::class, 'print_semester_transcripts_download']);
Route::get('/admin/transcripts/final-transcripts', [AdminTranscriptController::class, 'print_final_transcripts_view']);
Route::get('/admin/transcripts/final-transcripts-download', [AdminTranscriptController::class, 'print_final_transcripts_download']);
Route::get('/admin/transcripts/final-detail-certificate', [AdminTranscriptController::class, 'print_final_detail_certificate_view']);
Route::get('/admin/transcripts/final-detail-certificate-download', [AdminTranscriptController::class, 'print_final_detail_certificate_download']);
