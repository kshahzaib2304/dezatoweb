<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promo;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PromoController extends Controller
{
    public function index(): View
    {
        return view('admin.promos.index', [
            'title' => 'Promo codes | Dezato Admin',
            'heading' => 'Promo codes',
            'active' => 'promos',
            'nav' => config('dezato_admin.nav'),
            'promos' => Promo::query()->latest()->paginate(20),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        Promo::query()->create($data);

        return back()->with('status', 'Promo code “'.$data['code'].'” is ready. Customers can enter it at checkout.');
    }

    public function update(Request $request, Promo $promo): RedirectResponse
    {
        $data = $this->validated($request, $promo);
        $promo->update($data);

        return back()->with('status', 'Promo code updated.');
    }

    public function toggle(Promo $promo): RedirectResponse
    {
        $promo->update(['is_active' => ! $promo->is_active]);

        return back()->with(
            'status',
            $promo->is_active
                ? 'Promo code “'.$promo->code.'” is now active.'
                : 'Promo code “'.$promo->code.'” is turned off.'
        );
    }

    public function destroy(Promo $promo): RedirectResponse
    {
        $code = $promo->code;
        $promo->delete();

        return back()->with('status', 'Promo code “'.$code.'” deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Promo $promo = null): array
    {
        $data = $request->validate([
            'code' => [
                'required',
                'string',
                'max:40',
                Rule::unique('promos', 'code')->ignore($promo?->id),
            ],
            'label' => ['nullable', 'string', 'max:120'],
            'type' => ['required', Rule::in([Promo::TYPE_PERCENT, Promo::TYPE_FIXED])],
            'value' => ['required', 'integer', 'min:1', 'max:100000'],
            'min_subtotal' => ['nullable', 'integer', 'min:0', 'max:500000'],
            'max_uses' => ['nullable', 'integer', 'min:1', 'max:1000000'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'value.required' => 'Enter the discount amount (percent or PKR).',
            'type.in' => 'Choose Percent off or Fixed PKR off.',
        ]);

        if ($data['type'] === Promo::TYPE_PERCENT && $data['value'] > 100) {
            $data['value'] = 100;
        }

        $data['code'] = strtoupper(trim($data['code']));
        $data['label'] = filled($data['label'] ?? null) ? $data['label'] : null;

        $minSubtotal = $data['min_subtotal'] ?? null;
        $data['min_subtotal'] = $minSubtotal !== null && $minSubtotal !== ''
            ? (int) $minSubtotal
            : null;

        $maxUses = $data['max_uses'] ?? null;
        $data['max_uses'] = $maxUses !== null && $maxUses !== ''
            ? (int) $maxUses
            : null;
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
