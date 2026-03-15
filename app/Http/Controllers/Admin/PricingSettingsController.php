<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PricingSetting;
use App\Models\PricingSettingAudit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class PricingSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (! auth()->user()->isSuperAdmin() && ! auth()->user()->isOwner()) {
                abort(403, 'Unauthorized access');
            }

            return $next($request);
        });
    }

    public function edit(): View
    {
        $groups = $this->buildGroups();

        return view('admin.pricing-settings', [
            'groups' => $groups,
        ]);
    }

    public function audits(Request $request): View
    {
        $audits = PricingSettingAudit::query()
            ->with('user')
            ->latest()
            ->paginate(50);

        return view('admin.pricing-settings-audits', [
            'audits' => $audits,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $keys = array_keys($this->defaults());

        $existing = PricingSetting::query()
            ->whereIn('key', $keys)
            ->get()
            ->keyBy('key');

        $validator = Validator::make($request->all(), [
            'prices' => ['required', 'array'],
            'prices.*' => ['required', 'numeric', 'min:0'],
        ]);

        $validator->after(function ($validator) use ($keys, $request) {
            $prices = $request->input('prices', []);
            foreach ($keys as $key) {
                if (! array_key_exists($key, $prices)) {
                    $validator->errors()->add('prices.'.$key, 'This field is required.');
                }
            }
        });

        $validated = $validator->validate();

        foreach ($validated['prices'] as $key => $value) {
            $current = $existing[$key]->value ?? null;

            $setting = PricingSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );

            $currentNumeric = is_numeric($current) ? (float) $current : null;
            $newNumeric = (float) $value;

            if ($currentNumeric === null || abs($currentNumeric - $newNumeric) > 0.0001) {
                PricingSettingAudit::create([
                    'pricing_setting_id' => $setting->id,
                    'key' => $key,
                    'old_value' => $currentNumeric,
                    'new_value' => $newNumeric,
                    'user_id' => auth()->id(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }
        }

        PricingSetting::flushCache();

        return redirect()
            ->route('admin.pricing-settings.edit')
            ->with('success', 'Pricing settings updated successfully.');
    }

    private function buildGroups(): array
    {
        $defaults = $this->defaults();
        $values = PricingSetting::query()->whereIn('key', array_keys($defaults))
            ->pluck('value', 'key')
            ->toArray();

        $groups = [];
        foreach ($this->fieldGroups() as $groupLabel => $rows) {
            $groups[$groupLabel] = array_map(function (array $row) use ($values, $defaults) {
                $key = $row['key'];
                $row['value'] = $values[$key] ?? $defaults[$key] ?? 0;

                return $row;
            }, $rows);
        }

        return $groups;
    }

    private function fieldGroups(): array
    {
        return [
            'Individual - Basic' => [
                ['key' => 'basic.individual.quarter', 'label' => 'Quarter (3 months)'],
                ['key' => 'basic.individual.half', 'label' => 'Half (6 months)'],
                ['key' => 'basic.individual.year', 'label' => 'Year (12 months)'],
                ['key' => 'basic.individual.one_off', 'label' => 'One-off'],
            ],
            'Individual - Senior' => [
                ['key' => 'senior.individual.quarter', 'label' => 'Quarter (3 months)'],
                ['key' => 'senior.individual.half', 'label' => 'Half (6 months)'],
                ['key' => 'senior.individual.year', 'label' => 'Year (12 months)'],
                ['key' => 'senior.individual.one_off', 'label' => 'One-off'],
            ],
            'Individual - University' => [
                ['key' => 'university.individual.quarter', 'label' => 'Quarter (3 months)'],
                ['key' => 'university.individual.half', 'label' => 'Half (6 months)'],
                ['key' => 'university.individual.year', 'label' => 'Year (12 months)'],
                ['key' => 'university.individual.one_off', 'label' => 'One-off'],
            ],
            'Institution - Basic (per student)' => [
                ['key' => 'basic.institution.quarter', 'label' => 'Quarter (3 months)'],
                ['key' => 'basic.institution.half', 'label' => 'Half (6 months)'],
                ['key' => 'basic.institution.year', 'label' => 'Year (12 months)'],
                ['key' => 'basic.institution.mid_term', 'label' => 'Mid-term (one-off)'],
                ['key' => 'basic.institution.mock_exams', 'label' => 'Mock exams (one-off)'],
            ],
            'Institution - Senior (per student)' => [
                ['key' => 'senior.institution.quarter', 'label' => 'Quarter (3 months)'],
                ['key' => 'senior.institution.half', 'label' => 'Half (6 months)'],
                ['key' => 'senior.institution.year', 'label' => 'Year (12 months)'],
                ['key' => 'senior.institution.mid_term', 'label' => 'Mid-term (one-off)'],
                ['key' => 'senior.institution.mock_exams', 'label' => 'Mock exams (one-off)'],
            ],
            'Institution - University (per student)' => [
                ['key' => 'university.institution.quarter', 'label' => 'Quarter (3 months)'],
                ['key' => 'university.institution.half', 'label' => 'Half (6 months)'],
                ['key' => 'university.institution.year', 'label' => 'Year (12 months)'],
                ['key' => 'university.institution.mid_term', 'label' => 'Mid-term (one-off)'],
                ['key' => 'university.institution.mock_exams', 'label' => 'Mock exams (one-off)'],
            ],
        ];
    }

    private function defaults(): array
    {
        return [
            'basic.individual.quarter' => 20,
            'basic.individual.half' => 30,
            'basic.individual.year' => 45,
            'basic.individual.one_off' => 10,

            'senior.individual.quarter' => 35,
            'senior.individual.half' => 50,
            'senior.individual.year' => 75,
            'senior.individual.one_off' => 15,

            'university.individual.quarter' => 35,
            'university.individual.half' => 35,
            'university.individual.year' => 35,
            'university.individual.one_off' => 20,

            'basic.institution.quarter' => 45,
            'basic.institution.half' => 75,
            'basic.institution.year' => 45,
            'basic.institution.mid_term' => 5,
            'basic.institution.mock_exams' => 10,

            'senior.institution.quarter' => 75,
            'senior.institution.half' => 90,
            'senior.institution.year' => 75,
            'senior.institution.mid_term' => 10,
            'senior.institution.mock_exams' => 20,

            'university.institution.quarter' => 35,
            'university.institution.half' => 35,
            'university.institution.year' => 35,
            'university.institution.mid_term' => 0,
            'university.institution.mock_exams' => 0,
        ];
    }
}
