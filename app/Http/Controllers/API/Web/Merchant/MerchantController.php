<?php
namespace App\Http\Controllers\API\Web\Merchant;

use App\Formatter\Merchant\CurMerchantFormatter;
use App\Http\Controllers\Controller;
use App\Services\MerchantService;
use Tymon\JWTAuth\Facades\JWTAuth;
use Auth;
use Illuminate\Http\Request;

class MerchantController extends Controller
{
    protected $merchantService;
    function __construct(MerchantService $merchantService)
    {
        $this->merchantService = $merchantService;
        parent::__construct();
    }

    public function get_current(CurMerchantFormatter $curMerchantFormatter){

        $cur_merchant = $this->merchantService->first(Auth::user()->id);
        $data = $curMerchantFormatter->dataFormat($cur_merchant);
        $this->apiModel->setData($data);

        return $this->apiFormatter->success($this->apiModel);
    }
}
?>