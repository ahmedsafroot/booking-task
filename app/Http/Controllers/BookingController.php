<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookSlotRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Booking;
use App\Models\Pitch;

class BookingController extends Controller
{


    public function availableSlots(Request $request, int $pitchId): JsonResponse
    {
        $pitch = Pitch::find($pitchId);
        if (!$pitch) {
            return $this->notFound('Pitch not found');
        }

        $dateInput = $request->query('date');
        $date = $dateInput ? Carbon::parse($dateInput) : Carbon::today();
        $today = Carbon::today();

        // reject past dates
        if ($date->lt($today)) {
            return $this->error('Cannot view slots for past dates');
        }

        $slotDuration = (int) $request->query('duration', 90); // default 90 min

        // Stadium working hours (could be dynamic) from stadium table(has different open and close) or env file(same open and close)
        $startTime = Carbon::createFromTime(8, 0);  // open 8 AM
        $endTime   = Carbon::createFromTime(23, 0); // close 11 PM

        $now = Carbon::now();
        $slots = [];

        while ($startTime->copy()->addMinutes($slotDuration) <= $endTime) {
            $slotStart = $startTime->copy();
            $slotEnd   = $slotStart->copy()->addMinutes($slotDuration);

            // Skip past slots if today
            if ($date->isToday() && $slotStart->lt($now)) {
                $startTime->addMinutes($slotDuration);
                continue;
            }

            // Check overlap instead of exact match
            $isBooked = Booking::where('pitch_id', $pitch->id)
                ->whereDate('date', $date->toDateString())
                ->where('start_time', '<', $slotEnd->format('H:i:s'))
                ->where('end_time', '>', $slotStart->format('H:i:s'))
                ->exists();

            if (!$isBooked) {
                $slots[] = [
                    'start_time' => $slotStart->format('H:i'),
                    'end_time'   => $slotEnd->format('H:i'),
                ];
            }

            $startTime->addMinutes($slotDuration);
        }
        return $this->success(['slots'=>$slots]);
    }
    public function book(BookSlotRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $booking = Booking::create($validated);
        return $this->success(['slot'=>$booking],'Booking a slot successfully',201);
    }

}
