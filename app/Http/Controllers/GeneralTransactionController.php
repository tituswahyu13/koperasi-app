<?php

namespace App\Http\Controllers;

use App\Models\GeneralTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GeneralTransactionController extends Controller
{
    /**
     * Menampilkan daftar semua transaksi operasional.
     */
    public function index()
    {
        $transactions = GeneralTransaction::latest('transaction_date')->paginate(10);
        return view('general_transactions.index', compact('transactions'));
    }

    /**
     * Menampilkan form untuk membuat transaksi baru.
     */
    public function create()
    {
        return view('general_transactions.create');
    }

    /**
     * Menyimpan transaksi baru.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'type' => 'required|in:in,out', // 'in' untuk Pemasukan, 'out' untuk Pengeluaran
            'amount' => 'required|numeric|min:1',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'transaction_date' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            GeneralTransaction::create($validatedData);
            DB::commit();
            return redirect()->route('general_transactions.index')->with('success', 'Transaksi operasional berhasil dicatat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal mencatat transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan form untuk mengedit transaksi.
     */
    public function edit(GeneralTransaction $general_transaction)
    {
        return view('general_transactions.edit', compact('general_transaction'));
    }

    /**
     * Memperbarui transaksi.
     */
    public function update(Request $request, GeneralTransaction $general_transaction)
    {
        $validatedData = $request->validate([
            'type' => 'required|in:in,out',
            'amount' => 'required|numeric|min:1',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'transaction_date' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $general_transaction->update($validatedData);
            DB::commit();
            return redirect()->route('general_transactions.index')->with('success', 'Transaksi operasional berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus transaksi.
     */
    public function destroy(GeneralTransaction $general_transaction)
    {
        DB::beginTransaction();
        try {
            $general_transaction->delete();
            DB::commit();
            return redirect()->route('general_transactions.index')->with('success', 'Transaksi operasional berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }
}