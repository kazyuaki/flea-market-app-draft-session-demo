<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionDraftController extends Controller
{
    private function key(Transaction $tx): string
    {
        return 'draft.transaction.' . $tx->id . '.user.' . Auth::id() . '.body';
    }

    public function store(Request $request, Transaction $transaction)
    {
        $this->authorize('view', $transaction);
        $request->session()->put($this->key($transaction), (string) $request->input('body', ''));
        return response()->noContent();
    }

    public function destroy(Request $request, Transaction $transaction)
    {
        $this->authorize('view', $transaction);
        $request->session()->forget($this->key($transaction));
        return response()->noContent();
    }
}