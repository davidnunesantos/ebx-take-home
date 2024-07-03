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
                $destination_id = $request->input('destination');
                $destination_account = $this->deposit($destination_id, $request->input('amount'));
                $accounts->$destination_id = $destination_account;

                $response = [
                    'destination' => $destination_account
                ];
                break;
            case 'withdraw':
                $origin_id = $request->input('origin');
                $origin_account = $this->withdraw($origin_id, $request->input('amount'));

                if ($origin_account) {
                    $accounts->$origin_id = $origin_account;

                    $response = [
                        'origin' => $origin_account
                    ];
                } else {
                    $response = 0;
                    $status = 404;
                }
                break;
            case 'transfer':
                $origin_id = $request->input('origin');
                $destination_id = $request->input('destination');

                $origin_account = $this->withdraw($origin_id, $request->input('amount'));

                if ($origin_account) {
                    $accounts->$origin_id = $origin_account;

                    $destination_account = $this->deposit($destination_id, $request->input('amount'));
                    $accounts->$destination_id = $destination_account;

                    $response = [
                        'origin' => $origin_account,
                        'destination' => $destination_account
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

    /**
     * Depoist method
     *
     * @param int $destination_id
     * @param int $amount
     * @return stdClass
     */
    private function deposit(int $destination_id, int $amount): stdClass
    {
        $accounts = json_decode(Storage::get(self::FILE_NAME));

        if (isset($accounts->$destination_id)) {
            $account = $accounts->$destination_id;
        } else {
            $account = new stdClass();
            $account->id = "$destination_id";
            $account->balance = 0;
        }

        $account->balance += $amount;

        return $account;
    }

    /**
     * Withdraw method
     *
     * @param int $origin_id
     * @param int $amount
     * @return stdClass|boolean
     */
    private function withdraw(int $origin_id, int $amount): stdClass|bool
    {
        $accounts = json_decode(Storage::get(self::FILE_NAME));

        if (isset($accounts->$origin_id)) {
            $accounts->$origin_id->balance -= $amount;

            return $accounts->$origin_id;
        } else {
            return false;
        }
    }
}
