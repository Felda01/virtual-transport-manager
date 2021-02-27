<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateMarkets;
use App\Jobs\ManageDriverStatus;
use App\Jobs\PayBankLoans;
use App\Jobs\PayFees;
use App\Jobs\UpdatePersonalAgency;
use App\Utilities\QueueJobUtility;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Class AdminController
 * @package App\Http\Controllers
 */
class AdminController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function payFees(Request $request)
    {
        if ($request->input('single')) {
            QueueJobUtility::dispatch(new PayFees(true), Carbon::now('Europe/Bratislava'));
        } else {
            QueueJobUtility::dispatch(new PayFees(), Carbon::now('Europe/Bratislava'));
        }

        return back()->with([
            'message' => 'Job dispatched.'
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function generateMarkets(Request $request)
    {
        if ($request->input('single')) {
            QueueJobUtility::dispatch(new GenerateMarkets(true), Carbon::now('Europe/Bratislava'));
        } else {
            QueueJobUtility::dispatch(new GenerateMarkets(), Carbon::now('Europe/Bratislava'));
        }

        return back()->with([
            'message' => 'Job dispatched.'
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePersonalAgency(Request $request)
    {
        if ($request->input('single')) {
            QueueJobUtility::dispatch(new UpdatePersonalAgency(true), Carbon::now('Europe/Bratislava'));
        } else {
            QueueJobUtility::dispatch(new UpdatePersonalAgency(), Carbon::now('Europe/Bratislava'));
        }

        return back()->with([
            'message' => 'Job dispatched.'
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function manageDriverStatus(Request $request)
    {
        if ($request->input('single')) {
            QueueJobUtility::dispatch(new ManageDriverStatus($request->input('goSleep'), true), Carbon::now('Europe/Bratislava'));
        } else {
            QueueJobUtility::dispatch(new ManageDriverStatus($request->input('goSleep')), Carbon::now('Europe/Bratislava'));
        }

        return back()->with([
            'message' => 'Job dispatched.'
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetRoutes(Request $request)
    {
        \Illuminate\Support\Facades\Cache::forget('routes');

        return back()->with([
            'message' => 'Job dispatched.'
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bankLoan(Request $request)
    {
        if ($request->input('single')) {
            QueueJobUtility::dispatch(new PayBankLoans(true), Carbon::now('Europe/Bratislava'));
        } else {
            QueueJobUtility::dispatch(new PayBankLoans(), Carbon::now('Europe/Bratislava'));
        }

        return back()->with([
            'message' => 'Job dispatched.'
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function testNodeJs(Request $request)
    {
        try {
            $response = Http::withBasicAuth(config('services.nodejs.user'), config('services.nodejs.password'))->post(config('services.nodejs.test_url'));
        } catch (\Exception $e) {
            return back()->with([
                'message' => $e->getMessage()
            ]);
        }

        return back()->with([
            'message' => 'Job dispatched.'
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function fetchRoutes(Request $request)
    {
        try {
            $response = Http::withBasicAuth(config('services.nodejs.user'), config('services.nodejs.password'))->post(config('services.nodejs.fetch_routes_url'));
        } catch (\Exception $e) {
            return back()->with([
                'message' => $e->getMessage()
            ]);
        }

        return back()->with([
            'message' => 'Job dispatched.'
        ]);
    }
}
