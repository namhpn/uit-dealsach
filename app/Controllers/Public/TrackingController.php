<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;

class TrackingController extends BaseController
{
    public function requestOtp()
    {
        return redirect()->back()->with('error', 'Luồng OTP sẽ được hoàn thiện ở P7. Form theo dõi giá đã sẵn sàng để nối logic.');
    }

    public function verifyOtp()
    {
        return redirect()->back()->with('error', 'Xác thực OTP sẽ được hoàn thiện ở P7.');
    }

    public function createRule()
    {
        return redirect()->back()->with('error', 'Tạo theo dõi giá sẽ được hoàn thiện ở P7.');
    }

    public function disableRule()
    {
        return redirect()->back()->with('error', 'Tắt theo dõi giá sẽ được hoàn thiện ở P7.');
    }
}
