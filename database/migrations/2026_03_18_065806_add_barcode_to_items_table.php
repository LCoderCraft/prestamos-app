public function up()
{
    Schema::table('items', function (Blueprint $table) {
        // Agregamos la columna barcode, permitiendo nulos temporalmente para los existentes
        $table->string('barcode')->unique()->nullable()->after('name');
    });
}

public function down()
{
    Schema::table('items', function (Blueprint $table) {
        $table->dropColumn('barcode');
    });
}