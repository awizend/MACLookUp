<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MacAddressController extends Controller
{
    public function lookupMac($macAddress)
    {
        $macAddress = strtoupper(str_replace([':', '-', '.', ':'], '', $macAddress));

        $result = DB::table('oui_data')->where('assignment', '=', substr($macAddress, 0, 6))->first();

        if ($result) {
            return response()->json(['mac_address' => $macAddress, 'vendor' => $result->organization_name]);
        } else {
            return response()->json(['mac_address' => $macAddress, 'vendor' => 'Unknown']);
        }
    }

    public function lookupMacs(Request $request)
    {
        $macAddresses = $request->input('mac_addresses');
        $results = [];

        foreach ($macAddresses as $mac) {
            $mac = strtoupper(str_replace([':', '-', '.', ':'], '', $mac));

            $result = DB::table('oui_data')->where('assignment', '=', substr($mac, 0, 6))->first();

            if ($result) {
                $results[] = ['mac_address' => $mac, 'vendor' => $result->organization_name];
            } else {
                $results[] = ['mac_address' => $mac, 'vendor' => 'Unknown'];
            }
        }

        return response()->json($results);
    }
}
