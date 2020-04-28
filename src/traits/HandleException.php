<?php
namespace Looden\Framework;
use Illuminate\Validation\ValidationException;

trait HandleException
{
    public function handleException($e){
        if ($e instanceof ValidationException){
            return response()->json(['validations' => $e->errors()]);
        }
        if (config('looden.get_trace_error_on_api_calls')){
//            dd(config('looden.get_trace_error_on_api_calls'));
        }
        return response()->json([
            'error_message' => $e ->getMessage(),
            'trace' => config('looden.get_trace_error_on_api_calls') || request()->has('get-error-trace')
                ?   $e->getTraceAsString()
                :   'disabled'
        ], 400);
    }
}