<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AbandonedCheckout;
use Illuminate\Http\Request;

class AbandonedCheckoutController extends Controller
{
    public function index(Request $request)
    {
        $query = AbandonedCheckout::query()->latest('last_activity_at');
        if ($search = trim((string) $request->query('q'))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('phone', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('landing_slug', 'like', "%{$search}%");
            });
        }
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $checkouts = $query->paginate(25)->withQueryString();
        return view('backend.abandoned_checkouts.index', compact('checkouts'));
    }

    public function updateStatus(Request $request, AbandonedCheckout $abandonedCheckout)
    {
        $data = $request->validate(['status' => 'required|in:contacted,closed']);
        if ($abandonedCheckout->status !== 'converted') {
            $abandonedCheckout->update(['status' => $data['status']]);
        }
        return back()->with('success', 'Checkout follow-up status updated.');
    }
}
