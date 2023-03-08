<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ProductType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class ProductTypeController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return $this->searchProductTypeByName($user->id);
    }

    public function store()
    {
        try{
            $data = request()->validate([
                'name' => 'required|string',
                'image' => 'file'
            ]);
            $image = null;
            $user = auth()->user();
            if ($data['image']) {
                $image = $data['image']->store('productImages');
            }
            $productType = ProductType::create(['name' => $data['name'], 'user_id' => $user->id, 'image' => $image]);
            Log::info('productType created', ['productType' => $productType->id]);
            
            return $this->searchProductTypeByName($user->id);
        }catch(Exception $exception){
            Log::error('product type not created', $exception);
            
            return response(['success' => false, 'message' => 'product type not created'], 500);
        }
    }


    public function update()
    {
        try{
            $data = request()->validate([
                'name' => 'required|string',
                'id' => 'required',
                'image' => ''
            ]);
            $user = auth()->user();
            $newData = ['name' => $data['name']];
            if ($data['image'] && request()->hasFile('image')) {
                $image = ProductType::whereId($data['id'])->first();
                $image = $image->image;
                $this->deleteImage($image);
                $newData['image'] = $data['image']->store('productImages');
            }
            ProductType::where('id', $data['id'])->update($newData);

            return $this->searchProductTypeByName($user->id);
        }catch(Exception $exception){
            Log::error('product type not updated', $exception);
            
            return response(['success' => false, 'message' => 'product type not updated'], 500);
        }
    }


    public function delete($productTypeId)
    {
        try{
            $user = auth()->user();
            Item::where('product_type_id', $productTypeId)->delete();
            $productType = ProductType::where('id', $productTypeId)->first();
            $this->deleteImage($productType->image);
            $productType->delete();
            Log::info('productType deleted', ['productType' => $productType->id]);

            return $this->searchProductTypeByName($user->id);
        }catch(Exception $exception){
            Log::error('product type not deleted', $exception);
            
            return response(['success' => false, 'message' => 'product type not deleted'], 500);
        }
    }

    private function searchProductTypeByName($userId)
    {
        $search = request('search');
        $productTypes = ProductType::with('items')->where('user_id', $userId)->where('name', 'LIKE', "%$search%")->get();
        return response(['success' => true, 'data' => $productTypes]);
    }

    public function deleteImage($path)
    {
        if (Storage::exists($path)) {
            Storage::delete($path);
            Log::info('delete image success');
        }else{
            Log::warning('image doesn\'t exist');
        }
    }
}
