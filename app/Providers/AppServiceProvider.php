<?php

namespace App\Providers;

use App\Services\AuthService;
use App\Services\BankBranchService;
use App\Services\BankService;
use App\Services\ClassAttendanceService;
use App\Services\ClassCategoryHasStudentService;
use App\Services\ClassCategoryService;
use App\Services\ClassHallsService;
use App\Services\ClassRoomService;
use App\Services\ExamService;
use App\Services\GradeService;
use App\Services\ImageUploadService;
use App\Services\PaymentReasonService;
use App\Services\QuickPhotoService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Services\StudentAttendanceService;
use App\Services\StudentPaymentService;
use App\Services\ReadQRCodeService;
use App\Services\StudentAdmissionPaymentService;
use App\Services\StudentClassSeparateService;
use App\Services\StudentService;
use App\Services\StudentStudentStudentClassService;
use App\Services\SubjectService;
use App\Services\SystemUserService;
use App\Services\TeacherPaymentsService;
use App\Services\TeacherService;
use App\Services\UserTypesService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AuthService::class, function ($app) {
            return new AuthService();
        });
        $this->app->bind(StudentAdmissionPaymentService::class, function ($app) {
            return new StudentAdmissionPaymentService();
        });
        $this->app->bind(StudentAttendanceService::class, function ($app) {
            return new StudentAttendanceService();
        });
        $this->app->bind(BankBranchService::class, function ($app) {
            return new BankBranchService();
        });
        $this->app->bind(BankService::class, function ($app) {
            return new BankService();
        });
        $this->app->bind(ClassAttendanceService::class, function ($app) {
            return new ClassAttendanceService();
        });
        $this->app->bind(ClassCategoryService::class, function ($app) {
            return new ClassCategoryService();
        });
        $this->app->bind(ClassCategoryHasStudentService::class, function ($app) {
            return new ClassCategoryHasStudentService();
        });
        $this->app->bind(ClassHallsService::class, function ($app) {
            return new ClassHallsService();
        });
        $this->app->bind(ClassRoomService::class, function ($app) {
            return new ClassRoomService();
        });
        $this->app->bind(ExamService::class, function ($app) {
            return new ExamService();
        });
        $this->app->bind(GradeService::class, function ($app) {
            return new GradeService();
        });
        $this->app->bind(ImageUploadService::class, function ($app) {
            return new ImageUploadService();
        });
        $this->app->bind(PaymentReasonService::class, function ($app) {
            return new PaymentReasonService();
        });
        $this->app->bind(QuickPhotoService::class, function ($app) {
            return new QuickPhotoService();
        });
        $this->app->bind(StudentAttendanceService::class, function ($app) {
            return new StudentAttendanceService();
        });
        $this->app->bind(StudentPaymentService::class, function ($app) {
            return new StudentPaymentService();
        });
        $this->app->bind(ReadQRCodeService::class, function ($app) {
            return new ReadQRCodeService();
        });
        $this->app->bind(StudentService::class, function ($app) {
            return new StudentService();
        });
        $this->app->bind(StudentStudentStudentClassService::class, function ($app) {
            return new StudentStudentStudentClassService();
        });
        $this->app->bind(SubjectService::class, function ($app) {
            return new SubjectService();
        });
        $this->app->bind(StudentClassSeparateService::class, function ($app) {
            return new StudentClassSeparateService();
        });
        $this->app->bind(SystemUserService::class, function ($app) {
            return new SystemUserService();
        });
        $this->app->bind(TeacherPaymentsService::class, function ($app) {
            return new TeacherPaymentsService();
        });
        $this->app->bind(TeacherService::class, function ($app) {
            return new TeacherService();
        });
        $this->app->bind(UserTypesService::class, function ($app) {
            return new UserTypesService();
        });
    }


    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // මෙම line එක add කරන්න
        Schema::defaultStringLength(191);
    }
}
