<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeviceLeasing;
use App\Models\RegisterDevice;
use Illuminate\Support\Str;

class DeviceLeasingController extends Controller
{
    public function getDeviceLeasing(Request $request)
    {
        $lease_construction_id = $request->id;

        $device_leasing = DeviceLeasing::where('lease_construction_id', $lease_construction_id)->first();
        $getApiKey = RegisterDevice::where('device_id', $device_leasing->device_id)->first();

        RegisterDevice::where('device_id', $device_leasing->device_id)->update(['device_api_key' => Str::random(32)]);

        return response()->json([
            "deviceId" => $device_leasing->device_id,
            "deviceTraining" => $device_leasing->lease_max_training,
        ])->withHeaders([
            'X-API-KEY' => $getApiKey->device_api_key,
            'appVersion' => "v1.0",
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ]);;
    }
    public function postDeviceLeasing(Request $request)
    {
        $device_id = $request->deviceId;
        $lease_construction_id = $request->leaseConstructionId;
        $lease_max_training = $request->leaseMaxTraining;
        $lease_max_date = $request->leaseMaxDate;
        $lease_actual_start_date = $request->leaseActualStartDate;
        $lease_next_check = $request->leaseNextCheck;

        $device_leasing = new DeviceLeasing();
        $device_leasing->device_id = $device_id;
        $device_leasing->lease_construction_id = $lease_construction_id;
        $device_leasing->lease_max_training = $lease_max_training;
        $device_leasing->lease_max_date = $lease_max_date;
        $device_leasing->lease_actual_start_date = $lease_actual_start_date;
        $device_leasing->lease_next_check = $lease_next_check;

        $register_device = RegisterDevice::where('device_id', $device_id)->first();
        $already_construction_id = DeviceLeasing::where('device_id', $device_id)->orderby('id', 'desc')->get();

        if ($already_construction_id->count() == 0) {
            try {
                $device_leasing->save();
                RegisterDevice::where('device_id', $device_leasing->device_id)->update(['device_api_key' => Str::random(32)]);

                $leasing_periods = [];

                $already_construction_id_2 = DeviceLeasing::where('device_id', $device_id)->orderby('id', 'desc')->get();

                foreach ($already_construction_id_2 as $a_id) {
                    $leasing_periods[] = [
                        'id' => $a_id->id,
                        'deviceId' => $a_id->device_id,
                        'leasingConstructionId' => $a_id->lease_construction_id,
                        'leasingConstructionMaximumTraining' => $a_id->lease_max_training,
                        'leasingConstructionMaximumDate' => $a_id->lease_max_date,
                        'leasingActualPeriodStartDate' => $a_id->lease_actual_start_date,
                        'leasingNextCheck' => $a_id->lease_next_check,
                    ];
                }

                return response()->json([
                    'deviceId' => $device_id,
                    'leasingPeriods' => $leasing_periods,
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'title' => 'Error',
                    'message' => 'Same device id and activation code is not allowed',
                ]);
            }
        } elseif ($lease_construction_id != $already_construction_id[0]->lease_construction_id) {
            try {
                $device_leasing->save();
                RegisterDevice::where('device_id', $device_leasing->device_id)->update(['device_api_key' => Str::random(32)]);

                $leasing_periods = [];


                $already_construction_id_2 = DeviceLeasing::where('device_id', $device_id)->orderby('id', 'desc')->get();

                foreach ($already_construction_id_2 as $a_id) {
                    $leasing_periods[] = [
                        'id' => $a_id->id,
                        'deviceId' => $a_id->device_id,
                        'leasingConstructionId' => $a_id->lease_construction_id,
                        'leasingConstructionMaximumTraining' => $a_id->lease_max_training,
                        'leasingConstructionMaximumDate' => $a_id->lease_max_date,
                        'leasingActualPeriodStartDate' => $a_id->lease_actual_start_date,
                        'leasingNextCheck' => $a_id->lease_next_check,
                    ];
                }

                return response()->json([
                    'deviceId' => $device_id,
                    'leasingPeriods' => $leasing_periods,
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'title' => 'Error',
                    'message' => 'Same device id and activation code is not allowed',
                ]);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'Device Leasing Failed']);
        }
    }
}
