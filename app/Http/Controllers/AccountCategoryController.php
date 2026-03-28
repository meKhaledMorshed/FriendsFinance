<?php

namespace App\Http\Controllers;

use App\Models\AccountCategory;
use Illuminate\Http\Request;

class AccountCategoryController extends Controller
{
    public function index()
    {
        $categories = AccountCategory::paginate(15);
        return view('account-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('account-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:account_categories',
            'code' => 'required|string|max:50|unique:account_categories',
            'description' => 'nullable|string|max:500',
        ]);

        AccountCategory::create($validated);

        return redirect()->route('account-categories.index')->with('success', 'Account category created successfully.');
    }

    public function show(AccountCategory $accountCategory)
    {
        return view('account-categories.show', compact('accountCategory'));
    }

    public function edit(AccountCategory $accountCategory)
    {
        return view('account-categories.edit', compact('accountCategory'));
    }

    public function update(Request $request, AccountCategory $accountCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:account_categories,name,' . $accountCategory->id,
            'code' => 'required|string|max:50|unique:account_categories,code,' . $accountCategory->id,
            'description' => 'nullable|string|max:500',
        ]);

        $accountCategory->update($validated);

        return redirect()->route('account-categories.show', $accountCategory)
            ->with('success', 'Account category updated successfully.');
    }

    public function destroy(AccountCategory $accountCategory)
    {
        $accountCategory->delete();

        return redirect()->route('account-categories.index')
            ->with('success', 'Account category deleted successfully.');
    }
}
