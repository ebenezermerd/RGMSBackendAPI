<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|string|unique:transactions',
            'transaction_date' => 'required|date',
            'transaction_amount' => 'required|numeric',
            'transaction_type' => 'required|string',
            'transaction_description' => 'nullable|string',
            'fund_request_id' => 'required|exists:fund_requests,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $transaction = Transaction::create($request->all());
        return response()->json($transaction, 201);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $transaction = Transaction::findOrFail($id);
        return response()->json($transaction);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'transaction_id' => 'required|string|unique:transactions,transaction_id,' . $id,
            'transaction_date' => 'required|date',
            'transaction_amount' => 'required|numeric',
            'transaction_type' => 'required|string',
            'transaction_description' => 'nullable|string',
            'fund_request_id' => 'required|exists:fund_requests,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $transaction = Transaction::findOrFail($id);
        $transaction->update($request->all());
        return response()->json($transaction);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->delete();
        return response()->json(null, 204);
    }

    /**
     * Display a listing of the resource for a specific user.
     */
    public function userTransactions(string $user_id)
    {
        $transactions = Transaction::where('user_id', $user_id)->get();
        return response()->json($transactions);
    }
}
