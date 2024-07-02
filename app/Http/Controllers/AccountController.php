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
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Reset data state
     */
    public function reset()
    {
        $file_name = 'accounts.txt';
        $status = 200;
        
        try {
            if (Storage::exists($file_name)) {
                Storage::delete($file_name);
            }

            Storage::put('accounts.txt', '');
        } catch (Exception $e) {
            $status = 500;
        }

        return response('', $status);
    }
}
