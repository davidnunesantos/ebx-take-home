<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Account controller
 */
class AccountController extends Controller
{
    const FILE_NAME = 'accounts.txt';

    /**
     * Reset data state
     */
    public function reset()
    {
        $status = 200;
        
        try {
            if (Storage::exists(self::FILE_NAME)) {
                Storage::delete(self::FILE_NAME);
            }

            Storage::put('accounts.txt', json_encode([]));
        } catch (Exception $e) {
            $status = 500;
        }

        return response('OK', $status);
    }

    public function balance(Request $request)
    {
        $account_id = $request->query('account_id');
        $status = 200;
        $response = 0;

        if ($account_id) {
            $accounts = json_decode(Storage::get(self::FILE_NAME));

            if ($accounts && array_key_exists($account_id, $accounts)) {
                $response = $accounts[$account_id]->balance;
            } else {
                $status = 404;
                $response = 0;
            }
        } else {
            $status = 400;
            $response = 'Account ID is required';
        }

        return response($response, $status);
    }
}
