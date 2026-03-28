<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountCategory;
use App\Models\Branch;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = Account::with('user', 'category', 'branch')->paginate(15);
        return view('accounts.index', compact('accounts'));
    }

    public function create()
    {
        $categories = AccountCategory::active()->get();
        $branches = Branch::active()->get();
        return view('accounts.create', compact('categories', 'branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_number' => 'required|unique:accounts',
            'account_name' => 'required|string|max:255',
            'category_id' => 'required|exists:account_categories,id',
            'branch_id' => 'required|exists:branches,id',
        ]);

        $validated['user_id'] = auth()->id();
        Account::create($validated);

        return redirect()->route('accounts.index')->with('success', 'Account created successfully.');
    }

    public function show(Account $account)
    {
        return view('accounts.show', compact('account'));
    }

    public function edit(Account $account)
    {
        $categories = AccountCategory::active()->get();
        $branches = Branch::active()->get();
        return view('accounts.edit', compact('account', 'categories', 'branches'));
    }

    public function update(Request $request, Account $account)
    {
        $validated = $request->validate([
            'account_name' => 'required|string|max:255',
            'category_id' => 'required|exists:account_categories,id',
            'branch_id' => 'required|exists:branches,id',
        ]);

        $account->update($validated);

        return redirect()->route('accounts.show', $account)->with('success', 'Account updated successfully.');
    }

    public function destroy(Account $account)
    {
        $account->delete();

        return redirect()->route('accounts.index')->with('success', 'Account deleted successfully.');
    }
}
