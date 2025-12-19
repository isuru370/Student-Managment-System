<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $paymentData;
    public $pdfContent;
    public $fileName;
    public $teacherId;
    public $month;
    public $teacherName;
    public $totalAmount;
    public $teacherAmount;

    public function __construct($paymentData, $pdfContent, $fileName, $teacherId, $month)
    {
        $this->paymentData = $paymentData;
        $this->pdfContent = $pdfContent;
        $this->fileName = $fileName;
        $this->teacherId = $teacherId;
        $this->month = $month;
        $this->teacherName = $paymentData['teacher_name'] ?? 'Unknown';
        $this->totalAmount = $paymentData['total_class_amount'] ?? 0;
        $this->teacherAmount = $paymentData['total_teacher_amount'] ?? 0;
    }

    public function build(): self
    {
        return $this->subject('Student Payment Report - ' . $this->month)
            ->view('emails.payment_notification')
            ->with([
                'teacherName' => $this->teacherName,
                'teacherId' => $this->teacherId,
                'month' => $this->month,
                'paymentData' => $this->paymentData,
                'totalAmount' => $this->totalAmount,
                'teacherAmount' => $this->teacherAmount,
                'totalClasses' => $this->paymentData['total_classes'] ?? 0,
                'totalStudents' => $this->paymentData['total_students'] ?? 0,
            ])
            ->attachData($this->pdfContent, $this->fileName, [
                'mime' => 'application/pdf',
            ]);
    }
}
