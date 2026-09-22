<?php

namespace App\Http\Controllers;
use App\TransactionDetail;
use App\Item;
use App\Client;
use App\Dealer;
use Illuminate\Http\Request;

use RealRashid\SweetAlert\Facades\Alert;
class TransactionController extends Controller
{
    //

    public function index(Request $request)
    {
        $customers = Client::where('status', 'Active')->whereHas('serial')->get();
        $items = Item::get();
        $dealers = Dealer::get();
        $query = $this->transactionQuery();
        $transactionStats = [
            'total_sales' => (clone $query)->selectRaw('COALESCE(SUM(qty * price), 0) as total')->first()->total,
            'total_transactions' => (clone $query)->count(),
            'total_quantity' => (clone $query)->sum('qty'),
            'total_points' => (clone $query)->selectRaw('COALESCE(SUM(points_dealer + points_client), 0) as total')->first()->total,
        ];
        return view('transactions',
            array(
                'transactionStats' => $transactionStats,
                'items' => $items,
                'customers' => $customers,
                'dealers' => $dealers,
            )
        );
    }

    public function data(Request $request)
    {
        $query = $this->transactionQuery();
        $recordsTotal = (clone $query)->count();
        $search = trim((string) $request->input('search.value', ''));

        $this->applySearch($query, $search);

        $recordsFiltered = (clone $query)->count();
        $columns = [
            'id' => 'transaction_details.id', 'date' => 'transaction_details.date',
            'quantity' => 'transaction_details.qty', 'amount' => 'transaction_details.price',
            'dealer_points' => 'transaction_details.points_dealer',
            'customer_points' => 'transaction_details.points_client', 'item' => 'transaction_details.item',
        ];
        $requestedColumn = $request->input('columns.' . $request->input('order.0.column') . '.data', 'date');
        $orderBy = isset($columns[$requestedColumn]) ? $columns[$requestedColumn] : 'transaction_details.date';
        $direction = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $start = max(0, (int) $request->input('start', 0));
        $length = min(100, max(10, (int) $request->input('length', 10)));
        $canDelete = auth()->user()->role === 'Admin' && auth()->user()->can_delete === 'on';

        $transactions = $query->with(['dealer', 'customer'])->orderBy($orderBy, $direction)
            ->orderBy('transaction_details.id', 'desc')->skip($start)->take($length)->get();

        return response()->json([
            'draw' => (int) $request->input('draw', 0), 'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $transactions->map(function ($transaction) use ($canDelete) {
                $row = [
                    'id' => $transaction->id, 'date' => $transaction->date,
                    'date_display' => date('M d, Y', strtotime($transaction->date)),
                    'quantity' => number_format($transaction->qty, 2),
                    'amount' => number_format($transaction->qty * $transaction->price, 2),
                    'dealer' => optional($transaction->dealer)->name,
                    'customer' => optional($transaction->customer)->name,
                    'customer_address' => optional($transaction->customer)->address,
                    'dealer_points' => '<span class="text-success">' . e($transaction->points_dealer) . '</span>',
                    'customer_points' => '<span class="text-success">' . e($transaction->points_client) . '</span>',
                    'item' => e($transaction->item),
                ];
                if ($canDelete) {
                    $row['select'] = '<input type="checkbox" class="checkbox-item" data-id="' . $transaction->id . '">';
                    $row['actions'] = '<button type="button" class="btn btn-danger btn-sm delete-single" data-id="' . $transaction->id . '" title="Delete"><i class="bi bi-trash"></i></button>';
                }
                return $row;
            })->values(),
        ]);
    }

    public function export(Request $request)
    {
        $query = $this->transactionQuery();
        $this->applySearch($query, trim((string) $request->input('search', '')));
        $filename = 'transactions-' . date('Y-m-d') . '.xls';

        return response()->stream(function () use ($query) {
            echo '<?xml version="1.0" encoding="UTF-8"?>';
            echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">';
            echo '<Styles><Style ss:ID="Date"><NumberFormat ss:Format="yyyy-mm-dd"/></Style><Style ss:ID="Number"><NumberFormat ss:Format="0.00"/></Style></Styles>';
            echo '<Worksheet ss:Name="Transactions"><Table>';
            echo '<Row><Cell><Data ss:Type="String">ID</Data></Cell><Cell><Data ss:Type="String">Date</Data></Cell><Cell><Data ss:Type="String">Quantity</Data></Cell><Cell><Data ss:Type="String">Amount</Data></Cell><Cell><Data ss:Type="String">Dealer</Data></Cell><Cell><Data ss:Type="String">Customer</Data></Cell><Cell><Data ss:Type="String">Customer Address</Data></Cell><Cell><Data ss:Type="String">Dealer Points</Data></Cell><Cell><Data ss:Type="String">Customer Points</Data></Cell><Cell><Data ss:Type="String">Item</Data></Cell></Row>';

            $query->with(['dealer', 'customer'])->orderBy('date', 'desc')->orderBy('id', 'desc')->chunk(500, function ($transactions) {
                foreach ($transactions as $transaction) {
                    $values = [
                        ['value' => $transaction->id, 'type' => 'Number'],
                        ['value' => date('Y-m-d\\T00:00:00.000', strtotime($transaction->date)), 'type' => 'DateTime', 'style' => 'Date'],
                        ['value' => $transaction->qty, 'type' => 'Number', 'style' => 'Number'],
                        ['value' => $transaction->qty * $transaction->price, 'type' => 'Number', 'style' => 'Number'],
                        ['value' => optional($transaction->dealer)->name, 'type' => 'String'],
                        ['value' => optional($transaction->customer)->name, 'type' => 'String'],
                        ['value' => optional($transaction->customer)->address, 'type' => 'String'],
                        ['value' => $transaction->points_dealer, 'type' => 'Number', 'style' => 'Number'],
                        ['value' => $transaction->points_client, 'type' => 'Number', 'style' => 'Number'],
                        ['value' => $transaction->item, 'type' => 'String'],
                    ];
                    echo '<Row>';
                    foreach ($values as $field) {
                        $style = isset($field['style']) ? ' ss:StyleID="' . $field['style'] . '"' : '';
                        echo '<Cell' . $style . '><Data ss:Type="' . $field['type'] . '">' . htmlspecialchars((string) $field['value'], ENT_XML1 | ENT_COMPAT, 'UTF-8') . '</Data></Cell>';
                    }
                    echo '</Row>';
                }
            });

            echo '</Table></Worksheet></Workbook>';
        }, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function transactionQuery()
    {
        $query = TransactionDetail::query();
        if (auth()->user()->role === 'Dealer') {
            $query->where('dealer_id', auth()->id());
        } elseif (auth()->user()->role !== 'Admin') {
            $query->whereRaw('1 = 0');
        }
        return $query;
    }

    private function applySearch($query, $search)
    {
        if ($search === '') {
            return;
        }

        $query->where(function ($q) use ($search) {
            $q->where('transaction_details.id', 'like', '%' . $search . '%')
                ->orWhere('transaction_details.date', 'like', '%' . $search . '%')
                ->orWhere('transaction_details.item', 'like', '%' . $search . '%')
                ->orWhereHas('dealer', function ($dealerQuery) use ($search) {
                    $dealerQuery->where('name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('customer', function ($customerQuery) use ($search) {
                    $customerQuery->where('name', 'like', '%' . $search . '%')
                        ->orWhere('address', 'like', '%' . $search . '%');
                });
        });
    }


    public function store(Request $request)
    {
        // dd($request->all());
        $item = Item::findOrfail($request->item_id);


        $transaction = new TransactionDetail;
        $transaction->item = $item->item;
        $transaction->points_dealer = $item->dealer_points * $request->qty;
        $transaction->points_client = $item->customer_points * $request->qty;
        $transaction->item_description = $item->item_description;
        $transaction->qty = $request->qty;
        $transaction->price = $item->price;
        $transaction->client_id = $request->customer_id;
        $transaction->date = date('Y-m-d');
        $transaction->dealer_id = auth()->user()->id;
        $transaction->created_by = auth()->user()->id;
        $transaction->save();


         Alert::success('Successfully Save')->persistent('Dismiss');
        return back();
    }
    
    public function storeAdmin(Request $request)
    {
        // dd($request->all());
        $item = Item::findOrfail($request->item_id);


        $transaction = new TransactionDetail;
        $transaction->item = $item->item;
        $transaction->points_dealer = $item->dealer_points * $request->qty;
        $transaction->points_client = $item->customer_points * $request->qty;
        $transaction->item_description = $item->item_description;
        $transaction->qty = $request->qty;
        $transaction->price = $item->price;
        $transaction->client_id = $request->customer_id;
        $transaction->dealer_id = $request->dealer;
        $transaction->date = $request->date;
        $transaction->created_by = auth()->user()->id;
        $transaction->save();


         Alert::success('Successfully Save')->persistent('Dismiss');
        return back();
    }

  public function destroy($id)
    {
        try {
            if (!is_numeric($id) || $id <= 0) {
                return response()->json(['error' => 'Invalid transaction ID'], 400);
            }

            $transaction = TransactionDetail::findOrFail($id);

            if (auth()->user()->role === "Dealer" && $transaction->dealer_id != auth()->user()->id) {
                return response()->json(['error' => 'Unauthorized to delete this transaction'], 403);
            }

            $transaction->delete();

            return response()->json([
                'success' => 'Transaction deleted successfully',
                'transaction_id' => $id
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Transaction not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete transaction'], 500);
        }
    }


   public function bulkDelete(Request $request)
    {
        try {
            $ids = $request->input('ids');

            if (!$ids || !is_array($ids) || empty($ids)) {
                return response()->json(['error' => 'No transactions selected'], 400);
            }

            $validIds = array_filter($ids, function ($id) {
                return is_numeric($id) && intval($id) > 0;
            });

            if (empty($validIds)) {
                return response()->json(['error' => 'Invalid transaction IDs provided'], 400);
            }

            $validIds = array_map('intval', $validIds);

            $query = TransactionDetail::whereIn('id', $validIds);

            if (auth()->user()->role === "Dealer") {
                $query->where('dealer_id', auth()->user()->id);
            }

            $transactions = $query->get();

            if ($transactions->isEmpty()) {
                return response()->json(['error' => 'No valid transactions found or unauthorized'], 403);
            }

            $deletedIds = $transactions->pluck('id')->toArray();
            $deletedCount = TransactionDetail::whereIn('id', $deletedIds)->delete();

            return response()->json([
                'success' => "Successfully deleted {$deletedCount} transaction(s)",
                'deleted_count' => $deletedCount,
                'deleted_ids' => $deletedIds
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete transactions'], 500);
        }
    }


       
}
