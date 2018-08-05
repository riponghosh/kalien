<?php
namespace App\Http\Controllers\API\Web\Employee\Product;
use App\Formatter\Employee\TripActivityFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ImageStoreService;
use App\Services\TripActivity\TripActivityService;
use App\Services\TripActivityTicket\TripActivityTicketService;
use Illuminate\Support\Facades\Validator;
use League\Flysystem\Exception;
use Auth;

class ProductController extends Controller
{
    protected $tripActivityService;
    function __construct(TripActivityService $tripActivityService)
    {
        $this->tripActivityService = $tripActivityService;
        parent::__construct();
    }

    function show($id, TripActivityFormatter $tripActivityFormatter){
        $product = $this->tripActivityService->first_by_id($id);
        $product = $tripActivityFormatter->dataFormat($product);
        $this->apiModel->setData($product);
        return $this->apiFormatter->success($this->apiModel);
    }

    function update($id, Request $request){
        if(!$product = $this->tripActivityService->first_by_id($id)){
            return $this->apiFormatter->error();
        };
        $this->tripActivityService->update($product, $request->toArray());
        return $this->apiFormatter->success($this->apiModel);
    }

    function upload_gp_buying_status_img($package_id, $gp_buying_status_id, Request $request, TripActivityTicketService $tripActivityTicketService,ImageStoreService $imageStoreService){

        if(!$request->hasFile('image')){
            $this->apiModel->setMsg('不存在image');
            return $this->apiFormatter->error($this->apiModel);
        }
        if($validator = Validator::make($request->all(),['image' => 'mimes:jpeg,bmp,png,gif|max:20480',])->fails()){
            $this->apiModel->setMsg($validator->messages());
            return $this->apiFormatter->error($this->apiModel);
        };

        //check
        $package = $tripActivityTicketService->get_by_id($package_id);

        if(!collect($package->gp_buying_status)->firstWhere('id', $gp_buying_status_id)){
            $this->apiModel->setMsg('沒有此揪團奬勵。');
            return $this->apiFormatter->error($this->apiModel);
        }

        //save img
        if(!$img = $imageStoreService->store($request->File('image'),'product/package/gp_buying_status', Auth::user()->id)){
            $this->apiModel->setMsg('圖片儲存失敗。');
            return $this->apiFormatter->error($this->apiModel);
        };
        //save db
        if(!$save_to_db = $tripActivityTicketService->save_gp_buying_status_img($gp_buying_status_id, $img['img_path'], $img['img_format'])){
            $this->apiModel->setMsg('圖片儲存失敗。');
            return $this->apiFormatter->error($this->apiModel);
        }

        return $this->apiFormatter->success($this->apiModel);
    }
}
