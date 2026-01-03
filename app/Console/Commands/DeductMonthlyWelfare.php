<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WelfarePayment;
use App\Models\Teacher;
use App\Models\WelfareSetting;
use Carbon\Carbon;

class DeductMonthlyWelfare extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'welfare:deduct';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically deduct welfare from all teachers on 25th of each month';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // $today = Carbon::now();

        // if ($today->day != 31) {
        //     $this->info('Today is not 25th. No deductions.');
        //     return 0;
        // }

        // // Fetch current amount from DB
        // $setting = WelfareSetting::where('status', 1)->latest()->first();
        // $amount = $setting ? $setting->amount : 2000.00;

        // $teachers = Teacher::all()->where('is_active', 1);

        // foreach ($teachers as $teacher) {
        //     WelfarePayment::create([
        //         'teacher_id' => $teacher->id,
        //         'user_id' => 1,
        //         'amount' => $amount,
        //         'payment_date' => $today,
        //         'payment_method' => 'salary_deduction',
        //         'status' => 1,
        //         'description' => 'Automatic welfare deduction for ' . $today->format('F'),
        //     ]);
        // }

        // $this->info('Monthly welfare deductions completed successfully.');
        // return 0;
    }
}
