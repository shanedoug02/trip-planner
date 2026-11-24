<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTripRequest extends FormRequest
{
    /**
     * The controller checks that the user is on the trip.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Same as creating, except a trip that has already departed can still be edited.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'destination' => ['nullable', 'string', 'max:255'],
            'departs_on' => ['required', 'date'],
        ];
    }
}
