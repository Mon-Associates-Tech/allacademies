<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureOverseerIsReadOnly
{
    private const OWNER_ONLY_ROUTES = [
        'admin.overseer-schools',
        'admin.logout-all-users',
        'admin.users.impersonate',
        'impersonate',
        'admin.author-management',
        'admin.book-management',
        'admin.book-approvals',
        'admin.subject-management',
        'admin.question-availability',
        'admin.generate-examination',
        'admin.settings.id-card-templates',
        'academic-groups.*',
        'token-allocations.*',
        'admin.pricing-settings.*',
        'admin.general-exams.subscriptions',
        'admin.general-exams.pricing-tiers',
        'admin.artisan-commands',
        'admin.logins',
        'admin.activity-trail.*',
        'admin.academic-activities',
        'admin.change-log*',
        'admin.selective-backup',
        'admin.messenger-transactions.*',
        'media.*',
    ];

    private const MUTATING_GET_ROUTES = [
        'payment.initialize',
        'payment.book.initialize',
        'payment.callback',
        'payment.book.callback',
        'payment.subAccount',
        'schoolfees.callback',
        'feepayment.callback',
        'feepayment.student.callback',
        'students.fees.callback',
        'parent.fees.callback',
        'payments.public.callback',
        'general-exams.subscription.payment.callback',
        'user.mock-exams.subscription.payment.callback',
        'token-payments.initialize',
        'token-payments.callback',
        'token-payments.cancel',
        'bookshop.shop.payments.callback',
        'bookshop.shop.payments.initialize',
        'bookshop.shop.notifications.open',
        'sponsorships.payment.callback',
    ];

    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (! $user || ! $user->isReadOnlyOverseer()) {
            return $next($request);
        }

        abort_if($request->routeIs(...self::OWNER_ONLY_ROUTES), 403, 'This area is not available to overseers.');

        abort_if($request->routeIs(...self::MUTATING_GET_ROUTES), 403, 'Overseers have read-only access.');

        if ($request->routeIs('logout') || $request->is('livewire/update')) {
            return $next($request);
        }

        abort_unless(
            in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'], true),
            403,
            'Overseers have read-only access.'
        );

        return $next($request);
    }
}
