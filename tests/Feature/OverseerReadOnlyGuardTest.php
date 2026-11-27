<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureOverseerIsReadOnly;
use App\Livewire\Administrators\AdminNavigation;
use App\Livewire\Common\FlashMessageHandler;
use App\Livewire\Support\OverseerReadOnlyHook;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Mockery;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class OverseerReadOnlyGuardTest extends TestCase
{
    public function test_overseer_livewire_actions_are_denied_except_navigation_actions(): void
    {
        $this->setReadOnlyOverseer();
        $hook = new OverseerReadOnlyHook;
        $hook->setComponent(new AdminNavigation);

        $this->assertNull($hook->call('setActiveTab', ['overview'], static fn () => null));

        $this->expectException(HttpException::class);
        $hook->call('save', [], static fn () => null);
    }

    public function test_overseer_can_dismiss_flash_messages_and_receive_notification_events(): void
    {
        $this->setReadOnlyOverseer();
        $hook = new OverseerReadOnlyHook;
        $hook->setComponent(new FlashMessageHandler);

        $this->assertNull($hook->call('dismissMessage', ['message-id'], static fn () => null));
        $this->assertNull($hook->call('__dispatch', ['success', ['message' => 'Saved']], static fn () => null));
    }

    public function test_overseer_cannot_change_properties_with_persistence_hooks(): void
    {
        $this->setReadOnlyOverseer();
        $hook = new OverseerReadOnlyHook;
        $hook->setComponent(new ReadOnlyGuardComponent);

        $this->expectException(HttpException::class);
        $hook->update('name', 'name', 'Changed name');
    }

    public function test_overseer_http_mutations_are_denied(): void
    {
        $this->setReadOnlyOverseer();
        $request = Request::create('/admin/students', 'POST');
        $request->setUserResolver(fn () => Auth::user());

        $this->expectException(HttpException::class);
        app(EnsureOverseerIsReadOnly::class)->handle($request, static fn () => response('ok'));
    }

    public function test_overseer_cannot_access_get_routes_that_process_payments(): void
    {
        $this->setReadOnlyOverseer();

        foreach ([
            'payment.callback',
            'general-exams.subscription.payment.callback',
            'user.mock-exams.subscription.payment.callback',
            'parent.fees.callback',
            'sponsorships.payment.callback',
            'bookshop.shop.payments.callback',
        ] as $routeName) {
            $request = Request::create('/payment/callback', 'GET');
            $request->setUserResolver(fn () => Auth::user());
            $route = new RoutingRoute('GET', '/payment/callback', static fn () => null);
            $route->name($routeName);
            $request->setRouteResolver(static fn () => $route);

            try {
                app(EnsureOverseerIsReadOnly::class)->handle($request, static fn () => response('ok'));
                $this->fail("Read-only access should deny [{$routeName}].");
            } catch (HttpException $exception) {
                $this->assertSame(403, $exception->getStatusCode());
            }
        }
    }

    private function setReadOnlyOverseer(): void
    {
        $overseer = Mockery::mock(User::class);
        $overseer->shouldReceive('isReadOnlyOverseer')->andReturn(true);
        Auth::guard()->setUser($overseer);
    }
}

class ReadOnlyGuardComponent extends Component
{
    public $name = '';

    public function updatedName(): void
    {
        // Simulates a component lifecycle hook that may persist an updated value.
    }

    public function render()
    {
        return view('livewire.navigations.admin-navigation');
    }
}
