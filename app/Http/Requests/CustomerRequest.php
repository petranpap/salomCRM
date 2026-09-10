<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            // Not required in general — some customers don't want to share a phone
            // number — but required if SMS reminders are being turned on for them,
            // since there's nowhere to send the reminder otherwise.
            'phone' => 'nullable|string|max:15|required_if:consent_sms,1',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
            'consent_email' => 'boolean',
            'consent_sms' => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The customer name is required.',
            'phone.required_if' => 'Add a mobile phone number to enable SMS reminders for this customer.',
            'email.email' => 'The email must be a valid email address.',
        ];
    }
}