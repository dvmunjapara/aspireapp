<?php

namespace App\Http\Requests;

use App\Enums\LoanStatus;
use App\Enums\LoanType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        //Only allow user to apply for loan
        return auth()->user()->isUser();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'principal_amount' => 'required|numeric',
            'terms' => 'required|numeric',
            'type' => Rule::in(collect(LoanType::cases())->pluck('value')),

        ];
    }
}
