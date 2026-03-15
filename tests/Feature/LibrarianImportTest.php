<?php

namespace Tests\Feature;

use App\Models\Librarian;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LibrarianImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_librarian_import_with_valid_csv(): void
    {
        $school = School::factory()->create();
        $admin = User::factory()->create([
            'role' => 'admin',
            'school_id' => $school->id,
        ]);

        Storage::fake('public');

        $csvContent = "first_name,last_name,email,phone,gender,employee_id\n";
        $csvContent .= "John,Doe,john@example.com,123456789,male,EMP001\n";
        $csvContent .= "Jane,Smith,jane@example.com,987654321,female,EMP002\n";

        $file = UploadedFile::fake()->createWithContent('librarians.csv', $csvContent);

        $response = $this->actingAs($admin)
            ->post(route('admin.import', ['role' => 'librarians']), [
                'file' => $file,
                'school_id' => $school->id,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals(2, Librarian::count());
        $this->assertEquals(2, User::where('role', 'librarian')->count());

        $this->assertDatabaseHas('users', ['email' => 'john@example.com', 'first_name' => 'John']);
        $this->assertDatabaseHas('librarians', ['employee_id' => 'EMP001']);
    }

    public function test_librarian_import_skips_duplicates(): void
    {
        $school = School::factory()->create();
        $admin = User::factory()->create([
            'role' => 'admin',
            'school_id' => $school->id,
        ]);

        // Create an existing librarian
        $existingUser = User::factory()->create([
            'email' => 'john@example.com',
            'role' => 'librarian',
            'school_id' => $school->id,
        ]);
        Librarian::create([
            'user_id' => $existingUser->id,
            'school_id' => $school->id,
            'employee_id' => 'OLD001',
        ]);

        Storage::fake('public');

        $csvContent = "first_name,last_name,email,phone,gender,employee_id\n";
        $csvContent .= "John,Doe,john@example.com,123456789,male,EMP001\n";

        $file = UploadedFile::fake()->createWithContent('librarians.csv', $csvContent);

        $response = $this->actingAs($admin)
            ->post(route('admin.import', ['role' => 'librarians']), [
                'file' => $file,
                'school_id' => $school->id,
            ]);

        $response->assertSessionHas('success');
        $this->assertStringContainsString('Imported: 0', session('success'));
        $this->assertStringContainsString('Skipped: 1', session('success'));
        $this->assertEquals(1, Librarian::count());
    }
}
