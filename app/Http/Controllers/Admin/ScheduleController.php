<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\FulfillmentSchedule;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function edit(): View
    {
        $config = FulfillmentSchedule::config();

        return view('admin.schedule.edit', [
            'title' => 'Delivery times | Dezato Admin',
            'heading' => 'Delivery & pickup times',
            'active' => 'schedule',
            'nav' => config('dezato_admin.nav'),
            'slotsText' => implode("\n", $config['slots']),
            'minHours' => $config['min_hours'],
            'note' => $config['note'],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'slots' => ['required', 'string', 'max:2000'],
            'min_hours' => ['required', 'integer', 'min:0', 'max:168'],
            'note' => ['nullable', 'string', 'max:300'],
        ], [
            'slots.required' => 'Add at least one time window (one per line).',
            'min_hours.required' => 'Enter how many hours of notice you need (0 = same day OK).',
        ]);

        $slots = preg_split('/\r\n|\r|\n/', $data['slots']) ?: [];

        FulfillmentSchedule::save([
            'slots' => $slots,
            'min_hours' => (int) $data['min_hours'],
            'note' => $data['note'] ?? '',
        ]);

        return back()->with(
            'status',
            'Delivery times saved. Customers will see these slots at checkout.'
        );
    }
}
