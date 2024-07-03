<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use stdClass;

/**
 * Account controller
 */
class AccountController extends Controller
{
    const FILE_NAME = 'accounts.txt';

    /**
     * Reset data state
     * 
     * @return void
     */
    public function reset()
    {
        $status = 200;
        
        try {
            if (Storage::exists(self::FILE_NAME)) {
                Storage::delete(self::FILE_NAME);
            }

            Storage::put('accounts.txt', '{}');
        } catch (Exception $e) {
            $status = 500;
        }

        return response('OK', $status);
    }

    /**
     * Get account balance
     *
     * @param Request $request
     * @return void
     */
    public function balance(Request $request)
    {
        $account_id = $request->query('account_id');
        $status = 200;
        $response = 0;

        if ($account_id) {
            $accounts = json_decode(Storage::get(self::FILE_NAME));

            if ($accounts && isset($accounts->$account_id)) {
                $response = $accounts->$account_id->balance;
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

    /**
     * Store event
     *
     * @param EventRequest $request
     * @return void
     */
    public function event(EventRequest $request)
    {
        $status = 201;
        $response = null;

        $accounts = json_decode(Storage::get(self::FILE_NAME));

        switch ($request->input('type')) {
            case 'deposit':
                $destination = $request->input('destination');

                if (isset($accounts->$destination)) {
                    $account = $accounts->$destination;
                } else {
                    $account = new stdClass();
                    $account->id = $destination;
                    $account->balance = 0;
                }

                $account->balance += $request->input('amount');
                $accounts->$destination = $account;

                $response = [
                    'destination' => $account
                ];
                break;
            case 'withdraw':
                $origin = $request->input('origin');

                if (isset($accounts->$origin)) {
                    $accounts->$origin->balance -= $request->input('amount');

                    $response = [
                        'destination' => $accounts->$origin
                    ];
                } else {
                    $response = 0;
                    $status = 404;
                }
                break;
            default:
                $status = 400;
                $response = 'Event type not found';
                break;
        }

        Storage::put(self::FILE_NAME, json_encode($accounts));

        return response($response, $status);
    }
}
