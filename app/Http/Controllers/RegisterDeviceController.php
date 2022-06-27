<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\RegisterDevice;
use App\Models\DeviceLeasing;
use Illuminate\Support\Str;

class RegisterDeviceController extends Controller
{
    public function getDevice(Request $request)
    {
        $device_id = $request->id;
        $device = RegisterDevice::where('device_id', $device_id)->first();
        $device_leasing = DeviceLeasing::where('device_id', $device_id)->orderby('id', 'desc')->get();

        if ($device) {
            if ($device->device_type != 'leasing') {
                RegisterDevice::where('device_id', $device_id)->update(['device_api_key' => Str::random(32)]);

                return response()->json([
                    "deviceId" => $device_id,
                    "deviceType" => $device->device_type,
                    "leasingPeriods" => [],
                    "timestamp" => $device->created_at,
                ]);
            } else {
                RegisterDevice::where('device_id', $device_id)->update(['device_api_key' => Str::random(32)]);

                $leasing_periods = [];
                foreach ($device_leasing as $d_leasing) {
                    $leasing_periods[] = [
                        'leasingConstructionId' => $d_leasing->lease_construction_id,
                        'leasingConstructionMaximumTraining' => $d_leasing->lease_max_training,
                        'leasingConstructionMaximumDate' => $d_leasing->lease_max_date,
                    ];
                }

                return response()->json([
                    "deviceId" => $device_id,
                    "deviceType" => $device->device_type,
                    "deviceOwner" => "WebOrigo Magyarország Zrt.",
                    "deviceOwnerDetails" => [
                        "billing_name" => "WebOrigo Magyarország Zrt.",
                        "address_country" => "348",
                        "address_zip" => "1027",
                        "address_city" => "Budapest",
                        "address_street" => "Bem József utca 9. fszt.",
                        "vat_number" => "28767116-2-41"
                    ],
                    "dateofRegistration" => "2021-11-04",
                    "leasingPeriodsComputed" => [
                        "leasingConstructionId" => $device_leasing[0]->lease_construction_id,
                        "leasingConstructionMaximumTraining" =>  $device_leasing[0]->lease_max_training,
                        "leasingConstructionMaximumDate" =>  $device_leasing[0]->lease_max_date,
                        "leasingActualPeriodStartDate" =>  $device_leasing[0]->lease_actual_start_date,
                        "leasingNextCheck" => $device_leasing[0]->lease_next_check,
                    ],
                    "leasingPeriods" => $leasing_periods,
                    "timestamp" => "2021-07-01 00:00:00",
                ]);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Device not found'
            ]);
        }
    }
    public function postDevice(Request $request)
    {
        $device_id = $request->deviceId;
        $activation_code = $request->activationCode;

        if ($device_id && $activation_code) {
            $register_device = new RegisterDevice();
            $register_device->device_id = $device_id;
            $register_device->activation_code = $activation_code;
            $register_device->device_type = 'leasing';
            $register_device->device_api_key = Str::random(32);

            $already_registered = RegisterDevice::where('device_id', $device_id)->first();
            if ($already_registered) {
                if ($already_registered->device_api_key == "") {
                    $register_device->activation_code = $already_registered->activation_code;
                    RegisterDevice::where('device_id', $device_id)->update(['activation_code' => $activation_code, 'device_api_key' => $register_device->device_api_key, 'device_type' => $register_device->device_type]);

                    return response()->json([
                        'deviceId' => $device_id,
                        'deviceAPIKey' => $register_device->device_api_key,
                        'deviceType' => $register_device->device_type,
                        'timestamp' => $already_registered->created_at
                    ]);
                } elseif ($already_registered->activation_code == $activation_code) {
                    return response()->json([
                        'title' => 'Error',
                        'message' => 'Device already registered',
                    ]);
                }
            } else {
                try {
                    $register_device->save();
                    return response()->json([
                        'deviceId' => $device_id,
                        'deviceAPIKey' => $register_device->device_api_key,
                        'deviceType' => $register_device->device_type,
                        'timestamp' => $register_device->created_at
                    ]);
                } catch (\Exception $e) {
                    return response()->json([
                        'title' => 'Error',
                        'message' => 'Same device id and activation code is not allowed',
                    ]);
                }
            }
        } elseif ($device_id && !$activation_code) {
            $register_device = new RegisterDevice();
            $register_device->device_id = $device_id;
            $register_device->device_type = 'free';

            $already_registered = RegisterDevice::where('device_id', $device_id)->first();
            if ($already_registered) {
                return response()->json([
                    'title' => 'Error',
                    'message' => 'Device already registered',
                ]);
            } else {
                try {
                    $register_device->save();
                    return response()->json([
                        'deviceId' => $device_id,
                        'deviceAPIKey' => $register_device->device_api_key,
                        'deviceType' => $register_device->device_type,
                        'timestamp' => $register_device->created_at
                    ]);
                } catch (\Exception $e) {
                    return response()->json([
                        'title' => 'Error',
                        'message' => 'Same device id and activation code is not allowed',
                    ]);
                }
            }
        }
    }
}
