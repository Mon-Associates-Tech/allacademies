<?php

namespace App\Livewire\Teachers\Messages;

use App\Models\User;
use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject as Subject;

class TeacherMessageService
{
    public function getAcademicGroupsForTeacher($teacherId)
    {
        return AcademicGroup::whereHas('teachers', function ($query) use ($teacherId) {
            $query->where('user_id', $teacherId);
        })->get();
    }

    public function getAcademicLevelsForTeacher($teacherId)
    {
        return AcademicLevel::whereHas('teachers', function ($query) use ($teacherId) {
            $query->where('user_id', $teacherId);
        })->get();
    }

    public function getSubjectsForTeacher($teacherId)
    {
        return Subject::whereHas('teachers', function ($query) use ($teacherId) {
            $query->where('user_id', $teacherId);
        })->get();
    }

    public function getStudentsInAcademicGroup($groupId)
    {
        return User::whereHas('academicGroups', function ($query) use ($groupId) {
            $query->where('academic_group_id', $groupId);
        })->whereHas('roles', function ($query) {
            $query->where('name', 'student');
        })->get();
    }

    public function getStudentsInAcademicLevel($levelId)
    {
        return User::whereHas('academicLevels', function ($query) use ($levelId) {
            $query->where('academic_level_id', $levelId);
        })->whereHas('roles', function ($query) {
            $query->where('name', 'student');
        })->get();
    }

    public function getStudentsInSubject($subjectId)
    {
        return User::whereHas('subjects', function ($query) use ($subjectId) {
            $query->where('subject_id', $subjectId);
        })->whereHas('roles', function ($query) {
            $query->where('name', 'student');
        })->get();
    }

    public function getStudentGroups()
    {
        // This would depend on how student groups are implemented in your system
        // For example, you might have a StudentGroup model
        return [];
    }
}

