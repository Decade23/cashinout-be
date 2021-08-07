<?php

namespace App\Http\Controllers;

use App\Http\Resources\CashesResources;
use App\Models\Cash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CashController extends Controller
{
    public function index()
    {
        $begin = request('begin');
        $to = request('to');

        if ($begin && $to) {
            $credit = $this->getCreditDebit($begin, $to, '<');
            $debit = $this->getCreditDebit($begin, $to, '>=');
            $balances = $this->getBalances($begin, $to);

            $transactions = $this->getTransactions($begin, $to);
        } else {
            $credit = $this->getCreditDebit(now()->firstOfMonth(), now(), '<');
            $debit = $this->getCreditDebit(now()->firstOfMonth(), now(), '>=');
            $balances = $this->getBalances(now()->firstOfMonth(), now());

            $transactions = $this->getTransactions(now()->firstOfMonth(), now());
        }

        return response()->json([
            'balances' => formatPrice($balances),
            'credit' => formatPrice($credit),
            'debit' => formatPrice($debit),
            'transactions' => CashesResources::collection($transactions),
            'begin'     => $begin ?? now()->firstOfMonth()->format('Y-m-d'),
            'to'        => $to ?? now()->format('Y-m-d')
        ]);
    }
    public function store()
    {
        request()->validate([
            'name' => 'required',
            'amount' => 'required|numeric'
        ]);

        $slug = request('name') . "-" . Str::random(6);
        $when = request('when') ?? now();

        $cash = Auth::user()->cashes()->create([
            'name' => request('name'),
            'slug' => Str::slug($slug),
            'when' => $when,
            'amount' => request('amount'),
            'description' => request('description'),
        ]);

        return response()->json([
            'message' => 'transaction has been saved.',
            'cash' => new CashesResources($cash)
        ]);
    }

    public function show(Cash $cash)
    {
        $this->authorize('show', $cash);
        return new CashesResources($cash);
    }

    protected function getCreditDebit($begin, $to, $opr)
    {
        $select = ['amount'];
        return Auth()->user()->cashes()
            //->whereBetween('when', [$begin, $to])
            ->whereDate('when', '>=', $begin)
            ->whereDate('when', '<=', $to)
            ->where('amount', $opr, 0)
            ->get($select)
            ->sum($select);
    }

    protected function getBalances($begin, $to)
    {
        $select = ['amount'];
        return Auth()->user()->cashes()
            //->whereBetween('when', [$begin, $to])
            ->whereDate('when', '>=', $begin)
            ->whereDate('when', '<=', $to)
            ->get($select)
            ->sum($select);
    }

    protected function getTransactions($begin, $to)
    {
        return Auth()->user()->cashes()
            //->whereBetween('when', [$begin, $to])
            ->whereDate('when', '>=', $begin)
            ->whereDate('when', '<=', $to)
            ->latest()->get();
    }
}
