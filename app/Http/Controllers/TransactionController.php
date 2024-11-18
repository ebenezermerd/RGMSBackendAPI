<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransactionController extends Controller
{

    /**
     * Display a listing of the resource with pagination, filtering, and sorting.
     */
    public function index(Request $request)
    {
        try {
            $query = Transaction::query();

            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 20);
            $search = $request->input('search');
            $filter = $request->input('filter');
            $sort = $request->input('sort', 'created_at');
            $sortDirection = $request->input('sort_direction', 'desc');

            if ($search) {
                $query->where('transaction_description', 'like', "%{$search}%");
            }

            if ($filter && $filter !== 'all') {
                $query->where('transaction_type', $filter);
            }

            switch ($sort) {
                case 'date_desc':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'date_asc':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'amount_desc':
                    $query->orderBy('transaction_amount', 'desc');
                    break;
                case 'amount_asc':
                    $query->orderBy('transaction_amount', 'asc');
                    break;
                default:
                    $query->orderBy('created_at', $sortDirection);
                    break;
            }
            $transactions = $query->paginate($perPage, ['*'], 'page', $page);

            if ($transactions->isEmpty()) {
                return response()->json(['message' => 'No transactions found.'], 404);
            }
            
            $transactions->each(function ($transaction) {
                $transaction->status = 'approved';
                $transaction->proposal_name = $transaction->fundRequest->proposal->proposal_title ?? null;
                $transaction->proposal_budget = $transaction->fundRequest->proposal->proposal_budget ?? null;
                $transaction->remaining_budget = $transaction->fundRequest->proposal->remaining_budget ?? null;
                $transaction->activity_name = $transaction->fundRequest->activity->activity_name ?? null;
                $transaction->phase_name = $transaction->fundRequest->phase->phase_name ?? null;
                $transaction->username = $transaction->user->username;
                $transaction->full_name = $transaction->user->first_name . ' ' . $transaction->user->last_name;
                unset($transaction->fundRequest); // Remove the fundRequest relationship data
                unset($transaction->user); // Remove the fundRequest relationship data
            });

            return response()->json($transactions, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching transactions.'], 500);
        }
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
            'transaction_date' => 'required|date',
            'transaction_amount' => 'required|numeric',
            'transaction_type' => 'required|string',
            'transaction_description' => 'nullable|string',
            'fund_request_id' => 'required|exists:fund_requests,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $transactionId = 'TXT-' . strtoupper(uniqid()) . '-' . rand(10000000, 99999999);

        $transaction = Transaction::create(array_merge($request->all(), ['transaction_id' => $transactionId]));
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

    public function verifyTransaction(Request $request, $userId, $transactionId)
    {
        $request->validate([
            'verified' => 'required|boolean',
            'verification_details' => 'nullable|string',
            'type' => 'required|string',
        ]);

        $transaction = Transaction::findOrFail($transactionId);
        $transaction->update([
            'transaction_type' => $request->type,
            'verified' => $request->verified,
            'verification_details' => $request->verification_details,
            'verified_at' => $request->verified ? Carbon::now() : null,
        ]);

        return response()->json(['message' => 'Transaction verified successfully', 'transaction' => $transaction], 200);
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
        try {
            $transactions = Transaction::where('user_id', $user_id)->get();

            if ($transactions->isEmpty()) {
                return response()->json(['message' => 'No transactions found for this user.'], 404);
            }

            $transactions->each(function ($transaction) {
                $transaction->status = 'approved';
                $transaction->proposal_name = $transaction->fundRequest->proposal->proposal_title ?? null;
                $transaction->activity_name = $transaction->fundRequest->activity->activity_name ?? null;
                $transaction->phase_name = $transaction->fundRequest->phase->phase_name ?? null;
                $transaction->username = $transaction->user->username;
            });

            return response()->json($transactions, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching transactions.'], 500);
        }
    }
}
