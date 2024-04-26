<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();

            //$table->integer('IdSupplier');
            //$table->integer('IdProduct');
            $table->datetime('CreatedDate')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->datetime('RequieredDate');
            $table->decimal('Quantity',10,2);
            $table->string('UnitMeasurement');
            $table->string('Description')->nullable();//Concatenar IdProduct-IdQuote-description

            // Crea atributo de llave foránea
            $table->foreignId('IdProduct')->constrained('products')->cascadeOnUpdate()->cascadeOnDelete();
            // Crea atributo de llave foránea
            $table->foreignId('IdSupplier')->constrained('suppliers')->cascadeOnUpdate()->cascadeOnDelete();

            $table->foreignId('IdArea')->constrained('areas')->cascadeOnUpdate()->cascadeOnDelete();

            $table->timestamps();
        });

        // Trigger para calcular la descriccion del pedido , con IdQuote-IdProduct-NameProduct
        DB::unprepared("
            CREATE OR ALTER TRIGGER trg_UpdateDescription
            ON dbo.quotes
            AFTER INSERT
            AS
            BEGIN
                SET NOCOUNT ON;

                -- Declarar variables para almacenar los valores de [id] e [IdProduct]
                DECLARE @id bigint, @IdProduct bigint, @ProductName nvarchar(255);

                -- Obtener los valores de [id] e [IdProduct] de la tabla INSERTED
                SELECT @id = [id], @IdProduct = [IdProduct]
                FROM INSERTED;

                -- Obtener el nombre del producto relacionado con el IdProduct insertado
                SELECT @ProductName = p.[Name]
                FROM dbo.products p
                WHERE p.[id] = @IdProduct;

                -- Actualizar el campo [Description] con la concatenación de [id], [IdProduct], y el nombre del producto
                UPDATE dbo.quotes
                SET [Description] = 'Q' + CAST(@id AS nvarchar(50)) + 'P' + CAST(@IdProduct AS nvarchar(50)) + '- ' + @ProductName
                WHERE [id] = @id;
            END;
        ");

        // Trigger para calcular la descriccion del pedido , con IdQuote-IdProduct-NameProduct
        DB::unprepared("
            CREATE OR ALTER TRIGGER trg_InsertStocksAfterQuoteInsert
            ON dbo.quotes
            AFTER INSERT
            AS
            BEGIN
                SET NOCOUNT ON;
            
                -- Insertar en la tabla Stocks si no existe el registro
                INSERT INTO dbo.stocks (IsActive, EstimatedDurability, Status, Quantity, UnitMeasurement, IdArea, IdProduct)
                SELECT 
                    1 AS IsActive,  -- Valor predeterminado para IsActive
                    0 AS EstimatedDurability,  -- Valor predeterminado para EstimatedDurability
                    'Sin stock' AS Status,  -- Valor predeterminado para Status
                    0 AS Quantity,  -- Valor predeterminado para Quantity
                    i.UnitMeasurement,  -- Tomar el UnitMeasurement del registro insertado en Quotes
                    i.IdArea,  -- Tomar el IdArea del registro insertado en Quotes
                    i.IdProduct  -- Tomar el IdProduct del registro insertado en Quotes
                FROM
                    inserted i
                LEFT JOIN
                    dbo.stocks s ON i.IdArea = s.IdArea AND i.IdProduct = s.IdProduct
                WHERE
                    s.Id IS NULL;  -- Verificar si no existe un registro en Stocks para el área y producto insertados en Quotes
            END;
        ");




    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
