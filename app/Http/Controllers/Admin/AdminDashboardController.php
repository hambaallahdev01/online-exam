<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\User;
use App\Services\TenantDateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminDashboardController extends Controller
{
    public function index(TenantDateTime $tenantDateTime)
    {
        $school = Auth::user()->school;
        $teachersCount = User::where('school_id', $school->id)->where('role', 'teacher')->count();
        $studentsCount = User::where('school_id', $school->id)->where('role', 'student')->count();
        $classroomsCount = Classroom::where('school_id', $school->id)->count();
        $subjectsCount = Subject::where('school_id', $school->id)->count();

        $timezoneOptions = $tenantDateTime->timezoneOptions();
        $schoolTimezone = $tenantDateTime->timezoneFor($school);

        return view('admin.dashboard', compact(
            'school',
            'teachersCount',
            'studentsCount',
            'classroomsCount',
            'subjectsCount',
            'timezoneOptions',
            'schoolTimezone',
        ));
    }

    public function teachers(TenantDateTime $tenantDateTime)
    {
        $school = Auth::user()->school;
        $teachers = User::where('school_id', $school->id)->where('role', 'teacher')->get();
        $registeredDates = $teachers->mapWithKeys(fn (User $teacher): array => [
            $teacher->id => $tenantDateTime->format($teacher->created_at, $school, 'd M Y'),
        ]);

        return view('admin.teachers', compact('teachers', 'registeredDates'));
    }

    public function storeTeacher(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'identity_number' => 'nullable|string|max:100',
            'password' => 'required|string|min:8',
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

    public function students(TenantDateTime $tenantDateTime)
    {
        $school = Auth::user()->school;
        $students = User::where('school_id', $school->id)->where('role', 'student')->get();
        $registeredDates = $students->mapWithKeys(fn (User $student): array => [
            $student->id => $tenantDateTime->format($student->created_at, $school, 'd M Y'),
        ]);

        return view('admin.students', compact('students', 'registeredDates'));
    }

    public function storeStudent(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'identity_number' => 'nullable|string|max:100',
            'password' => 'required|string|min:8',
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
            'email' => 'required|email|max:255|unique:schools,email,'.$school->id,
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'locale' => 'required|string|in:id,en,ar,zh',
            'timezone' => 'required|timezone:all',
        ]);

        $school->update($validated);

        return back()->with('success', __('messages.success_update_profile'));
    }
}
