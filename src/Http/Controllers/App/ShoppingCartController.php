<?php

namespace Sudo\Order\Http\Controllers\App;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Cart;

class ShoppingCartController extends Controller
{
    public function __construct() {
        // Sử dụng middleware để check phân quyền và set ngôn ngữ
        $this->middleware(function ($request, $next) {
            // Đặt lại ngôn ngữ nếu trên url có request setLanguage
            setLanguage($request->setLanguage);

            return $next($request);
        });
    }

    /**
     * Thêm vào giỏ hàng
     * @param number        $requests->product_id: ID sản phẩm
     * @param number        $requests->quantity: số lượng
     */
    public function addToCart(Request $requests) {
        try {
            $product_id = $requests->product_id ?? 0;
            $quantity = $requests->quantity ?? 0;
            $product = \DB::table('products')->where('id', $product_id)->first();
            Cart::add([
                'id' => $product->id, 
                'name' => $product->name, 
                'qty' => $quantity,
                'price' => $product->price,
                'weight' => 0,
                'options' => [
                    'image' => getImage($product->image),
                ],
            ]);
            return [
                'status' => 1,
                'message' => __('Cập nhật vào giỏ hàng thành công.'),
                'total_quantity' => Cart::count(),
                'data' => $product
            ];
        } catch (\Exception $e) {
            \Log::error($e);
            return [
                'status' => 2,
                'message' => __('Translate::admin.ajax_fail'),
            ];
        }
    }

    /**
     * Xoá giỏ hàng
     * @param number        $requests->type: (All: Xoá toàn bộ giỏ hàng | One: Xoá 1 sản phẩm với rowId)
     */
    public function destroyCart(Request $requests) {
        try {
            if ($requests->type == 'all') {
                Cart::destroy();
                return [
                    'status' => 1,
                    'message' => __('Xoá giỏ hàng thành công.'),
                    'total_quantity' => Cart::count(),
                ];
            } else if ($requests->type == 'one') {
                $rowId = $requests->rowId;
                Cart::remove($rowId);
                return [
                    'status' => 1,
                    'message' => __('Xoá thành công.'),
                    'total_quantity' => Cart::count(),
                    'total_price' => Cart::priceTotal(0, '', ''),
                    'rowId' => $rowId
                ];
            }
        } catch (\Exception $e) {
            \Log::error($e);
            return [
                'status' => 2,
                'message' => __('Translate::admin.ajax_fail'),
            ];
        }
    }

    /**
     * Cập nhật số lượng của hàng trong giỏ
     * @param number        $requests->type: (minus: bỏ 1 | plus: thêm 1)
     */
    public function updateCart(Request $requests) {
        try {
            $type = $requests->type;
            $rowId = $requests->rowId;
            $data = Cart::get($rowId);
            $qty = $data->qty;
            if ($type == 'minus') {
                $qty = $qty-1;
            } else if ($type == 'plus') {
                $qty = $qty+1;
            }
            if ($qty > 0) { 
                Cart::update($rowId, $qty);
            }

            return [
                'status' => 1,
                'message' => __('Cập nhật vào giỏ hàng thành công.'),
                'total_quantity' => Cart::count(),
                'total_price' => Cart::priceTotal(0, '', ''),
                'data' => Cart::get($rowId)
            ];
        } catch (\Exception $e) {
            \Log::error($e);
            return [
                'status' => 2,
                'message' => __('Translate::admin.ajax_fail'),
            ];
        }
    }

}
