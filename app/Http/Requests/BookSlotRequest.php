<?php

namespace App\Http\Requests;

use App\Models\Booking;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BookSlotRequest extends FormRequest
{
    use ApiResponse;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'pitch_id' => 'required|exists:pitches,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ];
    }
    public function messages() : array
    {
        return [
            'pitch_id.required' => 'Pitch ID is required.',
            'pitch_id.exists' => 'The selected pitch does not exist.',
            'date.required' => 'Booking date is required.',
            'date.date' => 'Invalid date format.',
            'date.after_or_equal' => 'Booking date must be today or later.',
            'start_time.required' => 'Start time is required.',
            'start_time.date_format' => 'Start time must be in H:i format.',
            'end_time.required' => 'End time is required.',
            'end_time.date_format' => 'End time must be in H:i format.',
            'end_time.after' => 'End time must be after start time.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {

            //check slot 60 minutes or 90 minutes only
            $start = Carbon::createFromFormat('H:i', $this->start_time);
            $end = Carbon::createFromFormat('H:i', $this->end_time);
            $duration = $start->diffInMinutes($end);

            if (!in_array($duration, [60, 90])) {
                $validator->errors()->add('duration', 'Slot duration must be 60 or 90 minutes.');
            }

            // check if date is today the time not in past
            if ($this->date === Carbon::today()->toDateString() && $end->lt(Carbon::now())) {
                $validator->errors()->add('time', 'Cannot book a slot in the past.');
            }

            //check if there conflict booking
            $conflict = Booking::where('pitch_id', $this->pitch_id)
                ->where('date', $this->date)
                ->where('start_time', '<', $this->end_time)
                ->where('end_time', '>', $this->start_time)
                ->exists();

            if ($conflict) {
                $validator->errors()->add('slot', 'Slot already booked');
            }
        });
    }


    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->error('Validation failed',422, $validator->errors())
        );
    }

}
