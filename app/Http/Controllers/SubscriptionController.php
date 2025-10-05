<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\DriverSubscription;
use App\Models\DriverMembership;
use App\Models\Package;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    public function summary(Request $request)
    {
        $driver = $request->user();

        // 🔹 الاشتراك الشهري (لو موجود)
        $membership = DriverMembership::where('driver_id', $driver->id)
            ->where('is_active', true)
            ->where('expires_at', '>', Carbon::now())
            ->latest('started_at')
            ->first();

        $membershipData = $membership ? [
            'subscription_type'  => 'monthly',
            'title'              => "💳 الاشتراك الشهري",
            'renewal_date'       => $membership->expires_at ? Carbon::parse($membership->expires_at)->format('d M Y') : null,
            'price'              => $membership->price . " دينار أردني",
            'note'               => "✅ هذا الاشتراك يتيح لك استخدام التطبيق طوال مدة الاشتراك.",
        ] : null;

        // 🔹 اشتراك الرحلات (لو موجود)
        $subscription = DriverSubscription::with('package')
            ->where('driver_id', $driver->id)
            ->where('status', 'active')
            ->where('expires_at', '>', Carbon::now())
            ->latest('activated_at')
            ->first();

        $subscriptionData = $subscription ? [
            'subscription_type'  => 'package',
            'title'              => "🎫 باقة رحلات",
            'renewal_date'       => $subscription->expires_at ? Carbon::parse($subscription->expires_at)->format('d M Y') : null,
            'price'              => $subscription->package->price . " دينار أردني",
            'remaining_rides'    => $subscription->remaining_rides . " من أصل " . $subscription->package->rides_count,
            'note'               => "🧾 يتم خصم رحلة واحدة عند قبول كل طلب جديد ⚠️ لن تتمكن من استقبال طلبات جديدة إذا انتهى الرصيد",
        ] : null;

        // 🔹 خيارات الاشتراكات (باقات + شهري)
        $options = [
            'monthly'  => $this->getMembershipOptions(),
            'packages' => $this->getPackages()
        ];

        // 🔹 الاستجابة النهائية
        return ApiResponse::SendRespond(200, 'ملخص الاشتراك', [
            'monthly_subscription' => $membershipData,
            'package_subscription' => $subscriptionData,
            'options'              => $options
        ]);
    }

    private function getPackages()
    {
        $packages = Package::all();
        $options = [];

        foreach ($packages as $package) {
            $options[] = [
                'id'          => $package->id,
                'title'       => $package->title,
                'price'       => $package->price . " دينار أردني",
                'rides_count' => $package->rides_count . " رحلة",
                'description' => $package->description,
            ];
        }

        return $options;
    }

    private function getMembershipOptions()
    {
        // 🔹 تقدر تخزن الخطط في جدول منفصل بدل ما تكون ثابتة
        return [
            [
                'id'          => 1,
                'title'       => 'الاشتراك الشهري',
                'price'       => "10 دينار أردني",
                'description' => "يتيح لك استخدام التطبيق بالكامل لمدة شهر."
            ]
        ];
    }

    public function subscriptionsHistory(Request $request)
    {
        $driver = $request->user();

        $history = [];

        // 🔹 الاشتراكات الشهرية
        $memberships = DriverMembership::where('driver_id', $driver->id)
            ->orderBy('started_at', 'desc')
            ->get();

        foreach ($memberships as $m) {
            $history[] = [
                'type'        => 'monthly',
                'title'       => "💳 اشتراك شهري",
                'started_at'  => $m->started_at ? Carbon::parse($m->started_at)->format('d M Y') : null,
                'expires_at'  => $m->expires_at ? Carbon::parse($m->expires_at)->format('d M Y') : null,
                'price'       => $m->price . " دينار أردني",
                'status'      => $m->is_active ? 'active' : 'expired'
            ];
        }

        // 🔹 اشتراكات الباقات
        $packages = DriverSubscription::with('package')
            ->where('driver_id', $driver->id)
            ->orderBy('activated_at', 'desc')
            ->get();

        foreach ($packages as $p) {
            $history[] = [
                'type'           => 'package',
                'title'          => "🎫 " . ($p->package->title ?? 'باقة'),
                'started_at'     => $p->activated_at ? Carbon::parse($p->activated_at)->format('d M Y') : null,
                'expires_at'     => $p->expires_at ? Carbon::parse($p->expires_at)->format('d M Y') : null,
                'price'          => $p->package->price . " دينار أردني",
                'total_rides'    => $p->package->rides_count,
                'remaining_rides'=> $p->remaining_rides,
                'status'         => $p->status
            ];
        }

        return ApiResponse::SendRespond(200, 'سجل اشتراكات السائق', [
            'subscriptions' => $history
        ]);
    }
}


