<?php

namespace App\Imports;

use App\Models\MasterOrder;
use App\Models\Order;
use App\Models\Size;
use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;



class OrderImport implements ToCollection
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public $tailor_id;
    public function __construct($tailor_id)
    {
        $this->tailor_id = $tailor_id;
    }

    public function collection(Collection $rows)
    {

        $track_number = strtoupper(Str::random(3)) . rand(100000000, 999999999) . strtoupper(Str::random(2));

        $master_order=MasterOrder::create([
              'track_number'=>$track_number,
              'user_id'=> $this->tailor_id,
          ]);
        foreach ($rows as $index => $row) {


            $strings = explode(',', $row[1]);

            // Regular expression pattern to capture SKU, Arabic sentence, and Qty
            $pattern = '/\(SKU:\s*(.*?)\)(.*?)\(Qty:\s*(\d+)\)/u'; // 'u' flag for Unicode support


            // Arrays to store extracted data
            $skus = [];
            $arabic_sentences = [];
            $quantities = [];

            foreach ($strings as $string) {
                if (preg_match($pattern, $string, $matches)) {
                    $sku = $matches[1]; // SKU number
                    $arabic_sentence = trim($matches[2]); // Arabic sentence
                    $qty = $matches[3]; // Qty

                    $skus[] = $sku;
                    $arabic_sentences[] = $arabic_sentence;
                    $quantities[] = $qty;
                }
            }


            foreach ($arabic_sentences as $index => $item) {


                $size = Size::where('name', $skus[$index])->first();

                $id = $size->id ?? "";

                if (!$size) {
                    $size = Size::create([
                        'name' => $skus[$index],
                    ]);

                    $id = $size->id;
                }

                $product = Product::firstOrCreate(['name' => $item]);

                // Create the order
                Order::create([
                    'product_id' => $product->id,
                    'size_id' => $id,
                    'quantity_requested' => $quantities[$index],
                    'user_id' => $this->tailor_id,
                    'master_order_id' =>  $master_order->id,
                ]);

            }
        }
    }
}
