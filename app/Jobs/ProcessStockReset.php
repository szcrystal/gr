<?php

namespace App\Jobs;

use App\Item;
use App\ItemStockChange;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use DateTime;

class ProcessStockReset implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $now = new DateTime('now');
        $nowMonth = $now->format('n');
        
        $items = Item::get();
        
        //Item内の何月入荷の指定月が当月であれば、item内のリセットカウントをセットする
        foreach($items as $item) {
            if($nowMonth == $item->stock_reset_month) {
                $item->stock = $item->stock_reset_count;
                $item->save();
                
                //子ポットの時は親のIDをセットする
                $itemScId = $item->is_potset ? $item->pot_parent_id : $item->id;
                                
                //StockChange save
                ItemStockChange::updateOrCreate( //データがなければ各種設定して作成
                	['item_id'=>$itemScId], 
                    ['is_auto'=>1, 'updated_at'=>date('Y-m-d H:i:s', time())]
                ); 
                
            }
        }
    }
    
}

