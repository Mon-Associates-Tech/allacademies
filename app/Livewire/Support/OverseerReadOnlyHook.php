<?php

namespace App\Livewire\Support;

use App\Livewire\Administrators\AccountantManagement;
use App\Livewire\Administrators\ActivityTrail;
use App\Livewire\Administrators\AdminNavigation;
use App\Livewire\Administrators\AuthorManagement;
use App\Livewire\Administrators\BookManagement;
use App\Livewire\Administrators\Dashboard;
use App\Livewire\Administrators\GroupManagement;
use App\Livewire\Administrators\LibrarianManagement;
use App\Livewire\Administrators\ParentManagement;
use App\Livewire\Administrators\PaymentManagement;
use App\Livewire\Administrators\SchoolSwitcherPage;
use App\Livewire\Administrators\StudentGroupManagement;
use App\Livewire\Administrators\StudentManagement;
use App\Livewire\Administrators\TeacherManagement;
use App\Livewire\Common\FlashMessageHandler;
use Illuminate\Support\Str;
use Livewire\ComponentHook;
use ReflectionMethod;

class OverseerReadOnlyHook extends ComponentHook
{
    private const SAFE_ACTIONS = [
        AdminNavigation::class => ['setActiveTab', 'updateActiveTab'],
        Dashboard::class => ['setActiveTab'],
        FlashMessageHandler::class => [
            'dismissMessage', 'addMessage', 'addSuccessMessage', 'addErrorMessage', 'addWarningMessage', 'addInfoMessage',
            'clearAllMessages',
        ],
        SchoolSwitcherPage::class => ['switchToSchool', 'viewSchoolDetails'],
    ];

    private const SAFE_FLASH_MESSAGE_EVENTS = [
        'message', 'showMessage', 'success', 'error', 'warning', 'info', 'clearMessages',
    ];

    private const SAFE_UPDATE_PROPERTIES = [
        AccountantManagement::class => ['searchTerm', 'statusFilter'],
        ActivityTrail::class => [
            'selectedUser', 'selectedQuestionType', 'selectedAction', 'selectedModule', 'showOnlyQuestions', 'search',
        ],
        AuthorManagement::class => ['searchTerm', 'selectAll'],
        BookManagement::class => ['title', 'selectAll', 'selectedBooks'],
        GroupManagement::class => ['name'],
        LibrarianManagement::class => [
            'searchTerm', 'statusFilter', 'departmentFilter', 'positionFilter', 'selectAll',
        ],
        ParentManagement::class => ['search', 'statusFilter'],
        PaymentManagement::class => [
            'searchTerm', 'filterPaymentType', 'filterStatus', 'filterAcademicGroup',
        ],
        SchoolSwitcherPage::class => ['search'],
        StudentGroupManagement::class => ['name'],
        StudentManagement::class => [
            'filterAcademicGroup', 'filterAcademicLevel', 'filterStudentGroup', 'filterTeacher', 'filterSubject', 'searchTerm',
        ],
        TeacherManagement::class => [
            'searchTerm', 'filterAcademicGroup', 'filterAcademicLevel', 'filterSubject', 'filterSpecialization',
            'academicGroupId', 'academicLevelId',
        ],
    ];

    public function call($method, $params, $returnEarly): void
    {
        if (! auth()->user()?->isReadOnlyOverseer() || $this->isSafeAction($method) || $this->isSafeFlashMessageEvent($method, $params)) {
            return;
        }

        abort(403, 'Overseers have read-only access.');
    }

    private function isSafeFlashMessageEvent(string $method, array $params): bool
    {
        return get_class($this->component) === FlashMessageHandler::class
            && $method === '__dispatch'
            && isset($params[0])
            && in_array($params[0], self::SAFE_FLASH_MESSAGE_EVENTS, true);
    }

    public function update($propertyName, $fullPath, $newValue): void
    {
        if (! auth()->user()?->isReadOnlyOverseer() || ! $this->hasUpdateHook($propertyName)) {
            return;
        }

        $safeProperties = self::SAFE_UPDATE_PROPERTIES[get_class($this->component)] ?? [];

        abort_unless(in_array($propertyName, $safeProperties, true), 403, 'Overseers have read-only access.');
    }

    private function isSafeAction(string $method): bool
    {
        $componentClass = get_class($this->component);

        if (in_array($method, self::SAFE_ACTIONS[$componentClass] ?? [], true)) {
            return true;
        }

        if (! in_array($method, ['gotoPage', 'nextPage', 'previousPage', 'resetPage', 'setPage'], true)) {
            return false;
        }

        try {
            $methodFile = (new ReflectionMethod($this->component, $method))->getFileName();
        } catch (\ReflectionException) {
            return false;
        }

        return $methodFile && str_contains(
            str_replace('\\', '/', $methodFile),
            '/Features/SupportPagination/HandlesPagination.php'
        );
    }

    private function hasUpdateHook(string $propertyName): bool
    {
        $propertyMethod = Str::studly($propertyName);

        return method_exists($this->component, 'updating')
            || method_exists($this->component, 'updated')
            || method_exists($this->component, 'updating'.$propertyMethod)
            || method_exists($this->component, 'updated'.$propertyMethod);
    }
}
