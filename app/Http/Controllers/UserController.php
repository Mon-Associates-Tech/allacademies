<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\School;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource(user).
     *
     * @return Application|Factory|\Illuminate\View\View|object|View
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (! $user->isReadOnlyOverseer() && ! $user->isSuperAdmin()) {
            $this->authorize('administrate');
        }

        $canFilterBySchool = $user->canAccessCrossSchool() || $user->isOverseer();
        $selectedSchool = $canFilterBySchool && $request->filled('school')
            ? $request->input('school')
            : null;

        $usersQuery = User::query();
        if ($selectedSchool !== null) {
            $usersQuery->forSchool($selectedSchool);
        } elseif ($user->isReadOnlyOverseer()) {
            $usersQuery->where(function ($query) use ($user) {
                $query->whereIn('school_id', $user->overseenSchools()->select('schools.id'));

                if ($user->school_id) {
                    $query->orWhere('school_id', $user->school_id);
                }
            });
        } else {
            $usersQuery->forCurrentSchool();
        }

        $users = $usersQuery
            ->with(['school', 'student.academicGroup', 'student.academicLevel', 'teacher'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                });
            })
            ->when($request->filled('role'), function ($query) use ($request) {
                $query->where('role', $request->input('role'));
            })
            ->when($request->filled('gender'), function ($query) use ($request) {
                $query->where('gender', $request->input('gender'));
            })
            ->when($request->filled('academic_group'), function ($query) use ($request) {
                $query->whereHas('student', function ($q) use ($request) {
                    $q->where('academic_group_id', $request->input('academic_group'));
                });
            })
            ->when($request->filled('academic_level'), function ($query) use ($request) {
                $query->whereHas('student', function ($q) use ($request) {
                    $q->where('academic_level_id', $request->input('academic_level'));
                });
            })
            ->when($request->filled('subject'), function ($query) use ($request) {
                $query->whereHas('teacher.subjects', function ($q) use ($request) {
                    $q->where('academic_subjects.id', $request->input('subject'));
                });
            })
            ->when($request->boolean('verified'), function ($query) {
                $query->whereNotNull('email_verified_at');
            })
            ->when($request->boolean('unverified'), function ($query) {
                $query->whereNull('email_verified_at');
            })
            ->when($request->boolean('online'), function ($query) {
                $query->where('is_online', true);
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $status = $request->input('status');
                if ($status === 'active') {
                    $query->where('is_active', true);
                } elseif ($status === 'inactive') {
                    $query->where('is_active', false);
                }
            })
            ->when($request->filled('sort_by'), function ($query) use ($request) {
                $sortBy = $request->input('sort_by');
                $sortDirection = $request->input('sort_direction', 'asc');

                // Validate sort direction
                $sortDirection = in_array(strtolower($sortDirection), ['asc', 'desc']) ? $sortDirection : 'asc';

                // Validate and apply sorting
                $allowedSortColumns = ['name', 'email', 'created_at', 'last_seen_at', 'role'];
                if (in_array($sortBy, $allowedSortColumns)) {
                    $query->orderBy($sortBy, $sortDirection);
                } else {
                    $query->orderBy('created_at', 'desc');
                }
            }, function ($query) {
                $query->orderBy('created_at', 'desc');
            })
            ->paginate(15)
            ->withQueryString();

        // Get filter options
        $academicGroups = AcademicGroup::forCurrentSchool()
            ->orderBy('name')
            ->get(['id', 'name']);

        $academicLevels = AcademicLevel::forCurrentSchool()
            ->with('academicGroup:id,name')
            ->orderBy('name')
            ->get(['id', 'name', 'academic_group_id']);

        $subjects = AcademicSubject::whereHas('academicLevel.schools', function ($q) {
            $user = auth()->user();
            $schoolId = getSchoolId();

            if ($user->isOverseer()) {
                $q->where('school_id', $schoolId ?? 0);
            } elseif ($user->canAccessCrossSchool()) {
                if ($schoolId) {
                    $q->where('school_id', $schoolId);
                }
            } else {
                $q->where('school_id', $user->school_id);
            }
        })
            ->orderBy('name')
            ->get(['id', 'name', 'academic_level_id']);

        if ($user->canAccessCrossSchool()) {
            $schools = School::orderBy('name')->get(['id', 'name', 'city']);
        } elseif ($user->isOverseer()) {
            $schools = $user->overseenSchools()
                ->orderBy('name')
                ->get(['schools.id', 'schools.name', 'schools.city']);

            if ($user->school_id && ! $schools->contains('id', (int) $user->school_id)) {
                $ownSchool = School::find($user->school_id);
                if ($ownSchool) {
                    $schools->push($ownSchool);
                }
            }

            $schools = $schools->sortBy('name')->values();
        } else {
            $schools = collect();
        }

        return view('users.index', [
            'users' => $users,
            'academicGroups' => $academicGroups,
            'academicLevels' => $academicLevels,
            'subjects' => $subjects,
            'schools' => $schools,
            'canFilterBySchool' => $canFilterBySchool,
            'schoolOptions' => $schools->map(fn (School $school) => [
                'id' => $school->id,
                'name' => $school->name,
                'city' => $school->city,
            ])->values(),
        ]);
    }

    /**
     * Store a newly created user in storage.
     *
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $this->authorize('administrate');

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,teacher,student,librarian,moderator,author,parent,guest',
        ]);

        // Create the user
        $user = User::create([
            'school_id' => getSchoolId(),
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Assign role via many-to-many relationship
        $user->assignRole($request->role);

        // Create associated model based on role
        $user->handleRoleChange();
        $user->createFreeTrialSubscription(true);

        return redirect()->route('users.index')->with('success',
            "User '{$user->name}' has been created successfully with the role of {$request->role}."
        );
    }

    /**
     * Create role-specific account when user is created
     */
    private function createRoleSpecificAccount(User $user, string $role): void
    {
        switch ($role) {
            case 'student':
                if (! $user->student) {
                    Student::create([
                        'user_id' => $user->id,
                        'student_group_id' => null,
                    ]);
                }
                break;

            case 'teacher':
                logInfo("Creating teacher account for user {$user->name}");
                if (! $user->teacher) {
                    Teacher::create([
                        'user_id' => $user->id,
                    ]);
                }
                break;

        }
    }

    /**
     * Display the specified resource(user).
     *
     * @return Factory|View|Application|\Illuminate\View\View|object
     */
    public function show(User $user)
    {
        $viewer = auth()->user();

        if ($viewer->isReadOnlyOverseer()) {
            abort_unless(
                $user->school_id && $viewer->canAccessSchool($user->school_id),
                403
            );
        } elseif (! $viewer->isSuperAdmin()) {
            $this->authorize('administrate');
        }

        // Load all relationship counts for comprehensive display
        $user->loadCount([
            'subscriptions',
            'ownedTeams',
            'joinedTeams',
            'worksheets',
            'notes',
            'quizSessions',
            'borrowedBooks',
            'bookSubscriptions',
            'loginActivities',
            'tokenSubscriptions',
            'subscriptionCycles',
            'tokenUsageLogs',
            'uploadedMedia',
            'preferences',
            'roles',
        ]);

        // Load relationships with limits for display
        $user->load([
            'school',
            'primaryRole',
            'currentTeam',
            'suspendedBy',
            // Role-specific profiles
            'student',
            'teacher',
            'author',
            'librarian',
            'parent',
            // Content relationships
            'subscriptions' => function ($query) {
                $query->latest()->limit(10);
            },
            'ownedTeams' => function ($query) {
                $query->latest()->limit(10);
            },
            'joinedTeams' => function ($query) {
                $query->latest()->limit(10);
            },
            'notes' => function ($query) {
                $query->latest()->limit(10);
            },
            'worksheets' => function ($query) {
                $query->latest()->limit(10);
            },
            'quizSessions' => function ($query) {
                $query->latest()->limit(10);
            },
            'borrowedBooks' => function ($query) {
                $query->latest()->limit(10);
            },
            'bookSubscriptions' => function ($query) {
                $query->latest()->limit(10);
            },
            'loginActivities' => function ($query) {
                $query->latest()->limit(10);
            },
            'tokenSubscriptions' => function ($query) {
                $query->latest()->limit(10);
            },
            'subscriptionCycles' => function ($query) {
                $query->latest()->limit(10);
            },
            'preferences',
            'roles',
        ]);

        return view('users.show', [
            'user' => $user,
        ]);
    }

    /**
     * Change the role of a user.
     *
     * @return RedirectResponse
     */
    public function changeRole(Request $request)
    {
        $this->authorize('own');

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'email' => 'required|email|exists:users,email',
            'role' => 'required|in:guest,student,teacher,librarian,author,parent,moderator,admin',
        ]);

        $user = User::findOrFail($request->user_id);

        // Additional validation: make sure the email matches the user
        if ($user->email !== $request->email) {
            throw ValidationException::withMessages([
                'email' => 'The provided email does not match the selected user.',
            ]);
        }

        // Prevent changing owner role
        if ($user->role === UserRole::OWNER) {
            throw ValidationException::withMessages([
                'role' => "You cannot change this user's role.",
            ]);
        }

        $oldRole = $user->role->value;
        $user->role = UserRole::from($request->role);
        $user->save();
        $user->assignRole($request->role);

        $user->handleRoleChange($user);

        // Create student record if role is changed to student
        if ($request->role === 'student' && ! $user->student) {
            Student::create([
                'user_id' => $user->id,
                'student_group_id' => null, // You might want to assign to a default group
            ]);
        }

        // Optionally, remove student record if role is changed away from student
        if ($oldRole === 'student' && $request->role !== 'student' && $user->student) {
            //  $user->student->delete();
        }

        return redirect()->route('users.index')->with('success',
            "Successfully changed {$user->name}'s role from {$oldRole} to {$user->role->value}."
        );
    }

    public function changeSchool(Request $request): RedirectResponse
    {
        $actor = $request->user();
        abort_unless($actor && ($actor->isOwner() || $actor->isSuperAdmin()), 403);

        $validated = $request->validate([
            'user_id' => 'required|integer',
            'new_school_id' => 'required|integer|exists:schools,id',
        ]);

        $user = User::forCurrentSchool()->findOrFail($validated['user_id']);
        abort_if($user->isOwner() || $user->isSuperAdmin(), 403, 'Owner and superadmin accounts cannot be moved between schools.');

        $oldSchool = $user->school;
        $newSchool = School::findOrFail($validated['new_school_id']);

        if ((int) $user->school_id !== (int) $newSchool->id) {
            DB::transaction(function () use ($user, $newSchool) {
                $user->school_id = $newSchool->id;
                $user->save();

                foreach (['students', 'teachers', 'accountants', 'parents', 'librarians', 'authors'] as $table) {
                    DB::table($table)
                        ->where('user_id', $user->id)
                        ->update(['school_id' => $newSchool->id]);
                }
            });

            $user->logActivity('update', 'User School Changed', 'user', [
                'user_name' => $user->name,
                'previous_school' => $oldSchool?->name,
                'new_school' => $newSchool->name,
                'changed_by' => $actor->name,
            ]);
        }

        return redirect()->route('users.index')->with('success', "Successfully moved {$user->name} to {$newSchool->name}.");
    }

    /**
     * Mark a user's email as verified.
     *
     * @return RedirectResponse
     */
    public function markAsVerified(User $user)
    {
        $this->authorize('administrate');

        // Only allow verification if not already verified
        if ($user->email_verified_at) {
            return redirect()->route('users.index')
                ->with('error', 'User is already verified.');
        }

        $user->update(['email_verified_at' => now()]);

        // Log activity
        $user->logActivity('update', 'User Verified', 'user', [
            'user_name' => $user->name,
            'email' => $user->email,
            'verified_by' => auth()->user()?->name ?? 'Unknown',
            'verified_at' => now(),
        ]);

        return redirect()->route('users.index')
            ->with('success', "Successfully marked {$user->name} as verified.");
    }
}
