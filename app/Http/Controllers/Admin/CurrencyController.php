<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CurrencyController extends Controller
{
    public function index()
    {
        $currencies = Currency::orderBy('code')->get();

        return view('admin.currencies.index', compact('currencies'));
    }

    public function create()
    {
        return view('admin.currencies.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|size:3|unique:currencies,code',
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        Currency::create($data);

        return redirect()
            ->route('admin.currencies.index')
            ->with('success', 'Döviz oluşturuldu.');
    }

    public function edit(Currency $currency)
    {
        return view('admin.currencies.edit', compact('currency'));
    }

    public function update(Request $request, Currency $currency)
    {
        $data = $request->validate([
            'code' => 'required|string|size:3|unique:currencies,code,' . $currency->id,
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $currency->update($data);

        return redirect()
            ->route('admin.currencies.index')
            ->with('success', 'Döviz güncellendi.');
    }

    public function destroy(Currency $currency)
    {
        $currency->delete();

        return redirect()
            ->route('admin.currencies.index')
            ->with('success', 'Döviz silindi.');
    }

    public function fetchRates()
    {
        try {
            // TCMB XML
            $response = Http::timeout(10)->get('https://www.tcmb.gov.tr/kurlar/today.xml');

            if ($response->successful()) {
                $xml = simplexml_load_string($response->body());
                $updated = 0;

                foreach ($xml->Currency as $curr) {
                    $code = (string) $curr['CurrencyCode'];
                    $rate = (float) $curr->ForexSelling;

                    if ($rate > 0) {
                        $affected = Currency::where('code', $code)->update([
                            'exchange_rate' => $rate,
                            'updated_at' => now(),
                        ]);
                        $updated += $affected;
                    }
                }

                return back()->with('success', "{$updated} döviz kuru güncellendi (TCMB).");
            }

            return back()->with('error', 'TCMB verisi alınamadı.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Kur güncelleme hatası: ' . $e->getMessage());
        }
    }
}
