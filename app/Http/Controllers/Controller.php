<?php

namespace App\Http\Controllers;

use App\Traits\APIToolTrait;
use App\Traits\ContainerTrait;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use ContainerTrait;
    use APIToolTrait;
    protected $apiModel;
    protected $apiFormatter;
    protected $container;

    public function __construct()
    {
        $this->container = $this->getContainer();
        $this->apiModel = $this->getAPIModel();
        $this->apiFormatter = $this->getAPIFormatter();
    }

    protected function formatValidationErrors(Validator $validator)
    {
        return $validator->errors()->all();
    }

}
