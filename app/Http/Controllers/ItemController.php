<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ProductType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ItemController extends Controller
{
    public function index($productTypeId)
    {
        $user = auth()->user();
        return $this->searchBySerialNumber($user->id, $productTypeId);
    }

    public function store($productTypeId)
    {
        try {
            $user = auth()->user();
            $request = request()->validate([
                'serial_number' => 'required',
                'sold' => ''
            ]);
            $item = Item::create([
                'serial_number' => $request['serial_number'],
                'sold' => $request['sold'],
                'product_type_id' => $productTypeId
            ]);
            Log::info('item created', ['itemId' => $item->id]);
            
            return $this->searchBySerialNumber($user->id, $productTypeId);
        } catch (Exception $exception) {
            Log::error('item not created', $exception);
            
            return response(['success' => false, 'message' => 'item not created'], 500);
        }
    }

    public function update($productTypeId)
    {
        try {
            $user = auth()->user();
            $request = request()->validate([
                'id' => 'required',
                'serial_number' => 'required',
                'sold' => ''
            ]);
            $item = Item::where([
                'id' => $request['id'],
                'product_type_id' => $productTypeId
            ])->update(['serial_number' => $request['serial_number'], 'sold' => $request['sold']]);
            // Log::info('item updated', ['itemId' => $item->id]);
            
            return $this->searchBySerialNumber($user->id, $productTypeId);
        } catch (Exception $exception) {
            Log::error('item not updated', ['e' => $exception]);
            
            return response(['success' => false, 'message' => 'item not updated'], 500);
        }
    }

    public function delete($productId, $itemId)
    {
        try {
            $user = auth()->user();
            $item = Item::whereId($itemId)->first();
            $item->delete();
            Log::info('item deleted', ['itemId' => $item->id]);
            return $this->searchBySerialNumber($user->id, $productId);
            
        } catch (Exception $exception) {
            Log::error('item not deleted', $exception);
            
            return response(['success' => false, 'message' => 'item not deleted'], 500);
        }
    }

    private function searchBySerialNumber($userId, $productTypeId)
    {
        $search = request('search');
        $items = Item::with('product_type')->whereHas('product_type', function ($q) use ($userId) {
            return $q->where('user_id', $userId);
        })->where('product_type_id', $productTypeId)->where('serial_number', 'LIKE', "%$search%")
            ->get();
        return response(['success' => true, 'data' => $items]);
    }
}
