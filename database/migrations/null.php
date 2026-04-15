public function up(): void
{
    Schema::table('proses_mfg', function (Blueprint $table) {
        $table->unsignedBigInteger('partlist_id')->nullable()->change();
    });
}

public function down(): void
{
    Schema::table('proses_mfg', function (Blueprint $table) {
        $table->unsignedBigInteger('partlist_id')->nullable(false)->change();
    });
}