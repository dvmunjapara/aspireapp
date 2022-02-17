<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;
    protected $fillable = ['principal_amount', 'terms', 'type', 'status', 'interest_rate', 'approved_at', 'interest_amount'];
    protected $appends = ['total', 'remaining', 'remaining_tenure', 'paid_amount'];

    public function user() {

        return $this->belongsTo(User::class);
    }

    public function repayments() {

        return $this->hasMany(Repayment::class,'loan_id');
    }

    public function total() : Attribute {

        return Attribute::get(fn() => $this->principal_amount + $this->interest_amount);
    }

    public function paidAmount() : Attribute {


        return Attribute::get(fn() => round($this->repayments()->sum('amount'), 2));
    }

    public function remaining() : Attribute {

        return Attribute::get(fn() => $this->total - $this->paid_amount);
    }

    /*
     * Assumed payment is being made regularly on given date as in tenure list
     *
     * Calculate remaining payment tenures
     * */
    public function remainingTenure() : Attribute {

        return Attribute::get(function () {
            $repayments = $this->repayments;
            $repayments_counts = $repayments->count();

            $remaining_tenures = $this->terms - $repayments_counts;

            $last_payment = $repayments_counts ? $repayments->sortByDesc('payment_date')->first()->payment_date : $this->approved_at;

            $dateInterval = \DateInterval::createFromDateString("1 week");

            $endDate = Carbon::parse($this->approved_at)->addWeeks($this->terms + 1);

            $periods = new \DatePeriod(Carbon::parse($last_payment)->addWeek(), $dateInterval, $endDate);

            $data = [];
            $remaining_amount = $this->remaining;
            $term_amount = $remaining_amount / $remaining_tenures;

            foreach ($periods as $period) {

                $payable_amount  = $remaining_amount < $term_amount ? $remaining_amount : $term_amount;


                $data[] = [
                    'remaining_amount' => round($remaining_amount,2),
                    'payable_amount' => round($payable_amount, 2),
                    'next_payment_date' => $period->format('m/d/Y'),
                ];

                $remaining_amount -= $term_amount;

            }

            return $data;
        });
    }
}
