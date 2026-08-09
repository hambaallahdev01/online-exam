<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\QuestionGroup;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $school = Auth::user()->school;
        $teachersCount = User::where('school_id', $school->id)->where('role', 'teacher')->count();
        $studentsCount = User::where('school_id', $school->id)->where('role', 'student')->count();
        $classroomsCount = Classroom::where('school_id', $school->id)->count();
        $subjectsCount = Subject::where('school_id', $school->id)->count();

        return view('admin.dashboard', compact('school', 'teachersCount', 'studentsCount', 'classroomsCount', 'subjectsCount'));
    }

    public function teachers()
    {
        $schoolId = Auth::user()->school_id;
        $teachers = User::where('school_id', $schoolId)->where('role', 'teacher')->get();
        return view('admin.teachers', compact('teachers'));
    }

    public function storeTeacher(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'identity_number' => 'nullable|string|max:100',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'school_id' => Auth::user()->school_id,
            'name' => $request->name,
            'email' => $request->email,
            'identity_number' => $request->identity_number,
            'role' => 'teacher',
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Teacher registered successfully!');
    }

    public function students()
    {
        $schoolId = Auth::user()->school_id;
        $students = User::where('school_id', $schoolId)->where('role', 'student')->get();
        return view('admin.students', compact('students'));
    }

    public function storeStudent(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'identity_number' => 'nullable|string|max:100',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'school_id' => Auth::user()->school_id,
            'name' => $request->name,
            'email' => $request->email,
            'identity_number' => $request->identity_number,
            'role' => 'student',
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Student registered successfully!');
    }

    public function updateSchoolProfile(Request $request)
    {
        $school = Auth::user()->school;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:schools,email,' . $school->id,
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
        ]);

        $school->update($validated);

        return back()->with('success', 'School identity profile updated successfully!');
    }
}
