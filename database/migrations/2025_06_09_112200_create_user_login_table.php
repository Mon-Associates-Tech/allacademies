    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        public function up(): void
        {
            Schema::create('user_logins', static function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('action');
                $table->string('ip_address')->nullable();
                $table->text('user_agent')->nullable();
                $table->string('device_type')->nullable();
                $table->string('browser')->nullable();
                $table->string('platform')->nullable();
                $table->string('location')->nullable();
                $table->string('session_id')->nullable();
                $table->timestamps();
            });
        }

        public function down(): void
        {
            Schema::dropIfExists('user_logins');
        }
    };
