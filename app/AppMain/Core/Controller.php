<?php

namespace App\AppMain\Core;

use App\AppMain\Config\AppConst;
use Closure;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

use function App\AppMain\Core\Helpers\responseJsonFail;
use function App\AppMain\Core\Helpers\responseJsonSuccess;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Display a listing of the resource.
     *
     * @param  \Closure  $closure
     * @param  string|null $messageSuccess
     * @param  string|null $messageError
     * @param  string|null  ...$params
     * @return \Illuminate\Http\Response
     */
    protected function baseAction(Closure $closure, $messageSuccess = 'Success', $messageError = 'Error', ...$params)
    {
        try {
            $result = $closure();
        } catch (Throwable $e) {
            if (config('app.debug')) {
                Log::error($e->getMessage());
                Log::error($e->getTraceAsString());
            }
            $errorMessage = $e->getMessage() ?: __($messageError);
            if ($e->getCode() == CoreConst::NOT_FOUND) {
                return responseJsonFail($e->getMessage() ?? __($messageError), $e->getCode());
            }
            return responseJsonFail(CoreConst::CODE_EXCEPTION_MESSAGE == $e->getCode() ? $e->getMessage() : __($errorMessage));
        }

        return ($result || is_array($result)) ? responseJsonSuccess($result, __($messageSuccess)) : responseJsonFail(__($messageError));
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Closure  $closure
     * @param  string|null $messageSuccess
     * @param  string|null $messageError
     * @param  string|null  ...$params
     * @return \Illuminate\Http\Response
     */
    protected function baseActionTransaction(Closure $closure, $messageSuccess = 'Success', $messageError = 'Error', ...$params)
    {
        DB::beginTransaction();
        try {
            $result = $closure();
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            if (config('app.debug')) {
                Log::warning($e->getMessage());
            }
            $errorMessage = $e->getMessage() ?: __($messageError);
            return responseJsonFail(CoreConst::CODE_EXCEPTION_MESSAGE == $e->getCode() ? $e->getMessage() : __($errorMessage));
        }

        return $result ? responseJsonSuccess($result, __($messageSuccess)) : responseJsonFail(__($messageError));
    }
}
