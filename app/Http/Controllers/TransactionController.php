<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Support\TransactionType;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        return inertia('transactions/List', [
            'transactions' => function () use ($request) {
                return Transaction::query()
                    ->when($request->input('filtered_date_preset'), function ($query, $datePreset) {
                        $query->whereBetween('date', match ($datePreset) {
                            'Today' => [now()->startOfDay(), now()],
                            'This week' => [now()->startOfWeek(), now()],
                            'Last week' => [now()->subWeek()->startOfWeek(), now()],
                            'This month' => [now()->startOfMonth(), now()],
                            'Last month' => [now()->subMonth()->startOfMonth(), now()],
                            'This quarter' => [now()->startOfQuarter(), now()],
                            'Last quarter' => [now()->subQuarter()->startOfQuarter(), now()],
                            'This year' => [now()->startOfYear(), now()],
                            'Last year' => [now()->subYear()->startOfDay(), now()],
                            default => []
                        });
                    })
                    ->when($request->input('filtered_accounts'), function ($query, $accounts) {
                        $query->whereIn('account_id', explode(',', $accounts));
                    })
                    ->when($request->input('filtered_categories'), function ($query, $categories) {
                        $query->whereIn('category_id', explode(',', $categories));
                    })
                    ->when($request->input('filtered_type'), function ($query, $type) {
                        $query->where('amount', $type === 'Income' ? '>' : '<', 0);
                    })
                    ->with([
                        'account.currency',
                        'category',
                    ])
                    ->latest('date')
                    ->orderByDesc('id')
                    ->paginate($request->input('per_page'))
                    ->withQueryString();
            },
            'availableDatePresets' => function () {
                return [
                    'Today',
                    'This week',
                    'Last week',
                    'This month',
                    'Last month',
                    'This quarter',
                    'Last quarter',
                    'This year',
                    'Last year',
                ];
            },
            'availableAccounts' => function () {
                return Account::query()
                    ->whereHas('transactions')
                    ->orderBy('name')
                    ->get();
            },
            'availableCategories' => function () {
                return Category::query()
                    ->orderBy('name')
                    ->get();
            },
            'availableTypes' => function () {
                return ['Income', 'Expense'];
            },
            'filteredDatePreset' => array_filter(explode(',', $request->input('filtered_date_preset', ''))),
            'filteredAccounts' => array_filter(array_map('intval', explode(',', $request->input('filtered_accounts', '')))),
            'filteredCategories' => array_filter(array_map('intval', explode(',', $request->input('filtered_categories', '')))),
            'filteredType' => $request->input('filtered_type'),
        ]);
    }

    public function create()
    {
        inertia()->share('breadcrumbs', [
            [
                'text' => 'Transactions',
                'url' => route('transactions.index'),
            ],
            [
                'text' => 'Add transaction',
            ],
        ]);

        return inertia('transactions/Edit', [
            'availableTypes' => function () {
                return TransactionType::cases();
            },
            'availableAccounts' => function () {
                return Account::query()->with('currency')->orderBy('name')->get();
            },
            'availableCategories' => function () {
                return Category::query()->orderBy('name')->get();
            },
        ]);
    }

    public function store(StoreTransactionRequest $request)
    {
        $transaction = new Transaction($request->validated());
        $transaction->save();

        $transaction->account->updateAmount()->save();

        return redirect()->route('transactions.index');
    }

    public function show(Transaction $transaction)
    {
        return inertia('transactions/View', [
            'transactions' => $transaction,
        ]);
    }

    public function edit(Transaction $transaction)
    {
        inertia()->share('breadcrumbs', [
            [
                'text' => 'Transactions',
                'url' => route('transactions.index'),
            ],
            [
                'text' => 'Edit transaction',
            ],
        ]);

        return inertia('transactions/Edit', [
            'transaction' => $transaction->load(['account.currency', 'category']),
            'availableTypes' => function () {
                return TransactionType::cases();
            },
            'availableAccounts' => function () {
                return Account::query()->with('currency')->orderBy('name')->get();
            },
            'availableCategories' => function () {
                return Category::query()->orderBy('name')->get();
            },
        ]);
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        $account = $transaction->account;

        $transaction->fill($request->validated());
        $transaction->save();

        if ($transaction->wasChanged('account_id')) {
            $account->updateAmount()->save();
        }

        $transaction->refresh()->account->updateAmount()->save();

        return redirect()->route('transactions.index');
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        $transaction->account->updateAmount()->save();

        return redirect()->route('transactions.index');
    }
}
