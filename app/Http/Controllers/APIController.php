<?php

namespace Looden\Framework\Controllers;
use App\Http\Controllers\Controller;
use http\Env\Response;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Looden\Framework\App\LoodenModel;
use Looden\Framework\HandleException;
use Looden\Framework\HandleFieldToSelect;
use Looden\Framework\HandleFilter;
use Looden\Framework\HandleOrderBy;
use Looden\Framework\HandleWith;
use Looden\Framework\Helpers\LoodenHelper;
use Looden\Framework\Traits\ApiDescribe;
use Looden\Framework\Traits\ShowApi;
use Looden\Framework\Traits\IndexApi;
use Looden\Framework\Traits\DestroyApi;
use Looden\Framework\Traits\DestroyCollectionApi;
use Looden\Framework\Traits\StoreApi;
use Looden\Framework\Traits\StoreColletionApi;
use Looden\Framework\Traits\UpsertApi;
use Looden\Framework\Traits\UpsertColletionApi;
use Looden\Framework\Traits\ValidationAPI;

class APIController extends Controller
{
    use ApiDescribe;
    use ShowApi;
    use IndexApi;
    use DestroyApi;
    use DestroyCollectionApi;
    use UpsertApi;
    use UpsertColletionApi;
    use ValidationAPI;
    use ApiDescribe;

    use HandleFieldToSelect;
    use HandleOrderBy;
    use HandleFilter;
    use HandleWith;
    use HandleException;

    protected $query, $model;

    public function query(){
        return $this->query;
    }
}
